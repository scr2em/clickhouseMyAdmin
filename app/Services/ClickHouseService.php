<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClickHouseService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private int $timeout;

    public function __construct()
    {
        $config = config('clickhouse');
        $this->baseUrl = "{$config['protocol']}://{$config['host']}:{$config['port']}";
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->timeout = $config['timeout'];
    }

    public function ping(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/ping");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function query(string $sql, ?string $database = null): array
    {
        $sql = trim($sql);

        // Auto-add LIMIT if it's a SELECT without one
        if ($this->isSelect($sql) && !$this->hasLimit($sql)) {
            $sql = rtrim($sql, ';') . ' LIMIT 1000';
        }

        $sql = rtrim($sql, ';') . ' FORMAT JSON';

        return $this->execute($sql, $database);
    }

    public function statement(string $sql, ?string $database = null): array
    {
        return $this->execute(trim($sql), $database);
    }

    public function getDatabases(): array
    {
        $result = $this->query("SELECT name FROM system.databases WHERE name NOT IN ('INFORMATION_SCHEMA', 'information_schema') ORDER BY name");

        if ($result['success']) {
            return array_column($result['data'], 'name');
        }

        return [];
    }

    public function getTables(string $database): array
    {
        $db = addslashes($database);
        $result = $this->query("
            SELECT
                name,
                engine,
                total_rows,
                formatReadableSize(total_bytes) as size
            FROM system.tables
            WHERE database = '{$db}'
            ORDER BY name
        ");

        return $result['success'] ? $result['data'] : [];
    }

    public function getSchema(?string $database = null): array
    {
        $where = "database NOT IN ('INFORMATION_SCHEMA', 'information_schema')";
        if ($database) {
            $db = addslashes($database);
            $where = "database = '{$db}'";
        }

        $result = $this->query("
            SELECT database, table, name
            FROM system.columns
            WHERE {$where}
            ORDER BY database, table, position
        ");

        if (!$result['success']) {
            return [];
        }

        $schema = [];
        foreach ($result['data'] as $row) {
            $key = $row['database'] . '.' . $row['table'];
            $schema[$key][] = $row['name'];
        }

        return $schema;
    }

    public function getColumns(string $database, string $table): array
    {
        $db = addslashes($database);
        $tbl = addslashes($table);
        $result = $this->query("
            SELECT
                name,
                type,
                default_kind,
                default_expression,
                comment,
                is_in_partition_key,
                is_in_sorting_key,
                is_in_primary_key,
                is_in_sampling_key
            FROM system.columns
            WHERE database = '{$db}' AND table = '{$tbl}'
            ORDER BY position
        ");

        return $result['success'] ? $result['data'] : [];
    }

    public function getTableInfo(string $database, string $table): array
    {
        $db = addslashes($database);
        $tbl = addslashes($table);

        $infoResult = $this->query("
            SELECT
                name,
                engine,
                engine_full,
                partition_key,
                sorting_key,
                primary_key,
                sampling_key,
                total_rows,
                total_bytes,
                formatReadableSize(total_bytes) as size,
                metadata_modification_time as modified_at,
                create_table_query
            FROM system.tables
            WHERE database = '{$db}' AND name = '{$tbl}'
        ");

        if ($infoResult['success'] && !empty($infoResult['data'])) {
            return $infoResult['data'][0];
        }

        return [];
    }

    public function getTableData(
        string $database,
        string $table,
        int $limit = 50,
        int $offset = 0,
        ?string $orderBy = null,
        string $direction = 'ASC'
    ): array {
        $db = addslashes($database);
        $tbl = addslashes($table);
        $limit = min($limit, 500);
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM `{$db}`.`{$tbl}`";

        if ($orderBy) {
            $orderBy = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy);
            $sql .= " ORDER BY `{$orderBy}` {$direction}";
        }

        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        return $this->query($sql);
    }

    public function getRowCount(string $database, string $table): int
    {
        $db = addslashes($database);
        $tbl = addslashes($table);

        // Try system.tables first (instant)
        $result = $this->query("
            SELECT total_rows
            FROM system.tables
            WHERE database = '{$db}' AND name = '{$tbl}'
        ");

        if ($result['success'] && !empty($result['data']) && $result['data'][0]['total_rows'] !== null) {
            return (int) $result['data'][0]['total_rows'];
        }

        // Fallback to COUNT()
        $result = $this->query("SELECT count() as cnt FROM `{$db}`.`{$tbl}`");

        if ($result['success'] && !empty($result['data'])) {
            return (int) $result['data'][0]['cnt'];
        }

        return 0;
    }

    public function getServerInfo(): array
    {
        $result = $this->query("
            SELECT
                version() as version,
                uptime() as uptime,
                (SELECT formatReadableSize(sum(total_bytes)) FROM system.tables WHERE total_bytes IS NOT NULL) as total_size,
                (SELECT count() FROM system.databases WHERE name NOT IN ('INFORMATION_SCHEMA', 'information_schema')) as database_count,
                (SELECT count() FROM system.tables WHERE database NOT IN ('INFORMATION_SCHEMA', 'information_schema')) as table_count
        ");

        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }

        return [];
    }

    public function getUsers(): array
    {
        $result = $this->query("SELECT name, auth_type, host_ip, host_names, default_database, default_roles_all, default_roles_list FROM system.users ORDER BY name");

        return $result['success'] ? $result['data'] : [];
    }

    public function getUserInfo(string $user): array
    {
        $u = addslashes($user);
        $result = $this->query("SELECT name, auth_type, host_ip, host_names, default_database, default_roles_all, default_roles_list FROM system.users WHERE name = '{$u}'");

        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }

        return [];
    }

    public function getUserGrants(string $user): array
    {
        $u = preg_replace('/[^a-zA-Z0-9_]/', '', $user);
        $result = $this->query("SHOW GRANTS FOR `{$u}`");

        if ($result['success'] && !empty($result['data'])) {
            return array_column($result['data'], 'grants');
        }

        return [];
    }

    public function getRoles(): array
    {
        $result = $this->query("SHOW ROLES");

        if ($result['success'] && !empty($result['data'])) {
            return array_column($result['data'], 'name');
        }

        return [];
    }

    private function execute(string $sql, ?string $database = null): array
    {
        try {
            $params = [];
            if ($database) {
                $params['database'] = $database;
            }

            $url = $this->baseUrl . '/';
            if ($params) {
                $url .= '?' . http_build_query($params);
            }

            $response = Http::timeout($this->timeout)
                ->withBasicAuth($this->username, $this->password)
                ->withBody($sql, 'text/plain')
                ->post($url);

            if ($response->successful()) {
                $body = $response->body();
                $json = json_decode($body, true);

                if ($json !== null) {
                    return [
                        'success' => true,
                        'meta' => $json['meta'] ?? [],
                        'data' => $json['data'] ?? [],
                        'rows' => $json['rows'] ?? 0,
                        'statistics' => $json['statistics'] ?? [],
                        'rawJson' => $body,
                    ];
                }

                return [
                    'success' => true,
                    'meta' => [],
                    'data' => [],
                    'rows' => 0,
                    'statistics' => [],
                    'raw' => $body,
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function isSelect(string $sql): bool
    {
        return preg_match('/^\s*(SELECT|WITH|SHOW|DESCRIBE|DESC|EXPLAIN)\b/i', $sql) === 1;
    }

    private function hasLimit(string $sql): bool
    {
        return preg_match('/\bLIMIT\s+\d+/i', $sql) === 1;
    }
}

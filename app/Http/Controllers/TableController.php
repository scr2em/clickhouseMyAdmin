<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function structure(string $database, string $table, ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            return view('errors.connection');
        }

        $columns = $ch->getColumns($database, $table);
        $tableInfo = $ch->getTableInfo($database, $table);
        $databases = $ch->getDatabases();

        return view('table.structure', compact('database', 'table', 'columns', 'tableInfo', 'databases'));
    }

    public function data(string $database, string $table, Request $request, ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            return view('errors.connection');
        }

        $databases = $ch->getDatabases();
        $schema = $ch->getSchema($database);
        $customSql = trim($request->input('sql', ''));
        $isCustomQuery = !empty($customSql);

        if ($isCustomQuery) {
            $result = $ch->query($customSql, $database);
            $sql = $customSql;

            $data = [
                'database' => $database,
                'table' => $table,
                'result' => $result,
                'sql' => $sql,
                'isCustomQuery' => true,
                'totalRows' => $result['rows'] ?? 0,
                'totalPages' => 1,
                'page' => 1,
                'perPage' => 50,
                'orderBy' => null,
                'direction' => 'ASC',
                'databases' => $databases,
                'schema' => $schema,
            ];

            if ($request->hasHeader('HX-Request') && !$request->hasHeader('HX-Boosted')) {
                return view('table.partials.data-results', $data);
            }

            return view('table.data', $data);
        }

        $perPage = min((int) $request->get('per_page', 50), 500);
        $page = max((int) $request->get('page', 1), 1);
        $offset = ($page - 1) * $perPage;
        $orderBy = $request->get('order_by');
        $direction = $request->get('direction', 'ASC');

        $result = $ch->getTableData($database, $table, $perPage, $offset, $orderBy, $direction);
        $totalRows = $ch->getRowCount($database, $table);
        $totalPages = $totalRows > 0 ? (int) ceil($totalRows / $perPage) : 1;

        $db = addslashes($database);
        $tbl = addslashes($table);
        $sql = "SELECT * FROM `{$db}`.`{$tbl}` LIMIT {$perPage}";

        $data = compact(
            'database', 'table', 'result', 'totalRows', 'totalPages',
            'page', 'perPage', 'orderBy', 'direction', 'databases', 'sql', 'schema'
        ) + ['isCustomQuery' => false];

        if ($request->hasHeader('HX-Request') && !$request->hasHeader('HX-Boosted')) {
            return view('table.partials.data-results', $data);
        }

        return view('table.data', $data);
    }

    public function updateCell(string $database, string $table, Request $request, ClickHouseService $ch)
    {
        $column = $request->input('column');
        $value = $request->input('value');
        $wheres = $request->input('wheres', []);

        if (!$column || empty($wheres)) {
            return response('<div class="text-sm text-red-600">Missing column or row identifiers.</div>', 400);
        }

        $db = addslashes($database);
        $tbl = addslashes($table);
        $col = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

        // Build WHERE clause from row identifiers
        $conditions = [];
        foreach ($wheres as $wCol => $wVal) {
            $wCol = preg_replace('/[^a-zA-Z0-9_]/', '', $wCol);
            if ($wVal === null) {
                $conditions[] = "`{$wCol}` IS NULL";
            } elseif (is_bool($wVal)) {
                $conditions[] = "`{$wCol}` = " . ($wVal ? '1' : '0');
            } elseif (is_int($wVal) || is_float($wVal)) {
                $conditions[] = "`{$wCol}` = {$wVal}";
            } else {
                $escaped = addslashes((string) $wVal);
                $conditions[] = "`{$wCol}` = '{$escaped}'";
            }
        }
        $whereClause = implode(' AND ', $conditions);

        if ($value === '' || $value === null) {
            $setValue = 'NULL';
        } else {
            $escapedVal = addslashes($value);
            $setValue = "'{$escapedVal}'";
        }
        $sql = "ALTER TABLE `{$db}`.`{$tbl}` UPDATE `{$col}` = {$setValue} WHERE {$whereClause}";

        $result = $ch->statement($sql);

        if (!empty($result['success'])) {
            return response('<div class="text-sm text-green-600">Updated successfully.</div>');
        }

        $error = $result['error'] ?? 'Update failed.';
        return response('<div class="text-sm text-red-600">' . e($error) . '</div>', 422);
    }
}

<?php

return [
    'host' => env('CLICKHOUSE_HOST', 'localhost'),
    'port' => env('CLICKHOUSE_PORT', 8123),
    'protocol' => env('CLICKHOUSE_PROTOCOL', 'http'),
    'username' => env('CLICKHOUSE_USER', 'default'),
    'password' => env('CLICKHOUSE_PASSWORD', ''),
    'database' => env('CLICKHOUSE_DATABASE', 'default'),
    'timeout' => env('CLICKHOUSE_TIMEOUT', 30),
];

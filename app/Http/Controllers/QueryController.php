<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index(ClickHouseService $ch)
    {
        $databases = $ch->getDatabases();
        $schema = $ch->getSchema();
        $result = null;
        $sql = '';
        $selectedDatabase = null;

        return view('query.index', compact('databases', 'result', 'sql', 'selectedDatabase', 'schema'));
    }

    public function execute(Request $request, ClickHouseService $ch)
    {
        $sql = trim($request->input('sql', ''));
        $selectedDatabase = $request->input('database');
        $databases = $ch->getDatabases();
        $schema = $ch->getSchema();

        if (empty($sql)) {
            $result = ['success' => false, 'error' => 'Please enter a SQL query.'];
        } else {
            $isReadQuery = $this->isReadQuery($sql);

            if ($isReadQuery) {
                $result = $ch->query($sql, $selectedDatabase ?: null);
            } else {
                $result = $ch->statement($sql, $selectedDatabase ?: null);
            }
        }

        if ($request->hasHeader('HX-Request')) {
            return view('query.partials.results', compact('result'));
        }

        return view('query.index', compact('databases', 'result', 'sql', 'selectedDatabase', 'schema'));
    }

    private function isReadQuery(string $sql): bool
    {
        return preg_match('/^\s*(SELECT|WITH|SHOW|DESCRIBE|DESC|EXPLAIN)\b/i', $sql) === 1;
    }
}

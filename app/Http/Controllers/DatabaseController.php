<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function show(string $database, Request $request, ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            if ($request->hasHeader('HX-Request')) {
                return response('<span class="text-xs text-red-400 px-3">Connection error</span>');
            }
            return view('errors.connection');
        }

        $tables = $ch->getTables($database);

        // HTMX request from sidebar (not boosted navigation) — return table links partial
        if ($request->hasHeader('HX-Request') && !$request->hasHeader('HX-Boosted')) {
            $tableNames = collect($tables)->pluck('name');
            return view('components.sidebar-tables', [
                'db' => $database,
                'tables' => $tableNames,
                'currentTable' => null,
            ]);
        }

        $databases = $ch->getDatabases();

        return view('database.show', compact('database', 'tables', 'databases'));
    }
}

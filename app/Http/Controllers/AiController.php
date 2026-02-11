<?php

namespace App\Http\Controllers;

use App\Services\AnthropicService;
use App\Services\ClickHouseService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function generateSql(Request $request, AnthropicService $anthropic, ClickHouseService $ch)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'database' => 'nullable|string',
            'table' => 'nullable|string',
        ]);

        $prompt = $request->input('prompt');
        $database = $request->input('database');
        $table = $request->input('table');

        $context = $this->buildSchemaContext($ch, $database, $table);

        $result = $anthropic->generateSql($prompt, $context);

        return response()->json($result);
    }

    private function buildSchemaContext(ClickHouseService $ch, ?string $database, ?string $table): string
    {
        if ($database && $table) {
            $columns = $ch->getColumns($database, $table);
            if (empty($columns)) {
                return "Table: {$database}.{$table} (no column info available)";
            }

            $lines = ["Table: {$database}.{$table}", "Columns:"];
            foreach ($columns as $col) {
                $line = "  - {$col['name']} ({$col['type']})";
                if (!empty($col['comment'])) {
                    $line .= " -- {$col['comment']}";
                }
                $lines[] = $line;
            }

            return implode("\n", $lines);
        }

        $schema = $ch->getSchema($database);
        if (empty($schema)) {
            return $database ? "Database: {$database} (no tables found)" : "No schema information available.";
        }

        $lines = [];
        foreach ($schema as $tableKey => $columnNames) {
            $lines[] = "{$tableKey}: " . implode(', ', $columnNames);
        }

        return implode("\n", $lines);
    }
}

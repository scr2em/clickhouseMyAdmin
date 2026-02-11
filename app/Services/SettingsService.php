<?php

namespace App\Services;

class SettingsService
{
    private string $path;

    public function __construct()
    {
        $this->path = storage_path('app/settings.json');
    }

    public function all(): array
    {
        if (!file_exists($this->path)) {
            return [];
        }

        $contents = file_get_contents($this->path);
        return json_decode($contents, true) ?: [];
    }

    public function get(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $settings = $this->all();
        $settings[$key] = $value;

        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->path, json_encode($settings, JSON_PRETTY_PRINT));
    }
}

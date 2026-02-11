<?php

namespace App\Utilities;

class FileStorage {
    private string $basePath;

    public function __construct(string $basePath) {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    public function getBasePath(): string {
        return $this->basePath;
    }

    public function listDirectories(string $path): array {
        $fullPath = trim($this->basePath . $path, '/');
        if (!is_dir($fullPath)) {
            return [];
        }

        return array_values(array_filter(scandir($fullPath), function ($item) use ($fullPath) {
            return $item !== '.' && $item !== '..' && is_dir("$fullPath/$item");
        }));
    }

    public function listFiles(string $path, string $extension = 'json'): array {
        $fullPath = $this->basePath . '/' . trim($path, '/');
        if (!is_dir($fullPath)) {
            return [];
        }

        return array_values(array_filter(scandir($fullPath), function ($item) use ($fullPath, $extension) {
            return pathinfo($item, PATHINFO_EXTENSION) === $extension && is_file("$fullPath/$item");
        }));
    }

    public function readJson(string $path): ?object {
        $fullPath = $this->basePath . $path;
        return file_exists($fullPath) ? json_decode(file_get_contents($fullPath), false) : null;
    }

    public function writeJson(string $path, array|object $data): bool {
        $fullPath = $this->basePath . $path;
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }
        return file_put_contents($fullPath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    public function deleteFile(string $path): bool {
        $fullPath = $this->basePath . $path;
        return file_exists($fullPath) ? unlink($fullPath) : false;
    }

    public function deleteDirectory(string $path): bool {
        $fullPath = $this->basePath . $path;
        if (!is_dir($fullPath)) return false;

        foreach (scandir($fullPath) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $fullPath . '/' . $file;
                is_dir($filePath) ? $this->deleteDirectory($filePath) : unlink($filePath);
            }
        }
        return rmdir($fullPath);
    }
}

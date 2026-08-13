<?php
declare(strict_types=1);
namespace App\Core;

class Request {
    public function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath(): string {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function post(string $key): ?string {
        return $_POST[$key] ?? null;
    }
}
<?php
declare(strict_types=1);
namespace app\services;

class authservice {
    public function verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function hash(string $password): string {
        return password_hash($password, password_argon2id);
    }
}
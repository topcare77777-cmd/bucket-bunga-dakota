<?php
declare(strict_types=1);
namespace app\core;

class session {
    public static function start(): void {
        if (session_status() === php_session_none) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => true,
                'cookie_samesite' => 'strict'
            ]);
        }
    }
}
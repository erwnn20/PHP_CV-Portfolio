<?php

use JetBrains\PhpStorm\NoReturn;

class ErrorHandler {
    #[NoReturn] public static function handleException(Throwable $exception): void
    {
        $code = $exception->getCode();

        switch ($code) {
            case 403:
                self::render403();
            case 404:
                self::render404();
            default:
                self::render500();

        }
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null) {
            self::render500();
        }
    }

    #[NoReturn] public static function render403(): void
    {
        http_response_code(403);
        require 'errors/403.php';
        exit();
    }

    #[NoReturn] public static function render404(): void
    {
        http_response_code(404);
        require 'errors/404.php';
        exit();
    }

    #[NoReturn] public static function render500(): void
    {
        http_response_code(500);
        require 'errors/500.php';
        exit();
    }
}

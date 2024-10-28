<?php

class ErrorController
{
    public function render403(): void
    {
        require 'app/views/errors/403.php';
    }

    public function render404(): void
    {
        require 'app/views/errors/404.php';
    }

    public function render500(): void
    {
        require 'app/views/errors/500.php';
    }
}
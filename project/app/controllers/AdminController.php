<?php

class AdminController
{
    public function index(): void
    {
        require 'app/views/admin-panel.php';
    }

    public function ban(): void
    {
        require 'app/helpers/post/ban.php';
    }
}
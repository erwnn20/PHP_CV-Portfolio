<?php

class HomeController
{
    public function index(): void
    {
        require 'app/views/home.php';
    }

    public function login(): void
    {
        require 'app/auth/login.php';
    }

    public function logout(): void
    {
        require 'app/auth/logout.php';
    }
}
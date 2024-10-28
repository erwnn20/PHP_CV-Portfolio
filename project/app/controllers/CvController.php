<?php

class CvController
{
    public function index(): void
    {
        require 'app/views/resume.php';
    }

    public function edit(): void
    {
        require 'app/views/cv-edit.php';
    }

    public function select(): void
    {
        require 'app/helpers/post/resume-select.php';
    }

    public function post(): void
    {
        require 'app/helpers/post/cv-post.php';
    }
}
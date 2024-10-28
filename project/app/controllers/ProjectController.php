<?php

class ProjectController
{
    public function edit(): void
    {
        require 'app/views/projects-edit.php';
    }

    public function post(): void
    {
        require 'app/helpers/post/projects-post.php';
    }
}
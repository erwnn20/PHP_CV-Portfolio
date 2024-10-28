<?php

class ProfileController
{
    public function edit(): void
    {
        require 'app/views/profile-edit.php';
    }

    public function post(): void
    {
        require 'app/helpers/post/profile-post.php';
    }
}
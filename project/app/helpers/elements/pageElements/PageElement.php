<?php

class PageElement
{
    public static function headerUser($userData, $id, bool $home = false) : string
    {
        if (isset($userData['first_name'], $userData['last_name']))
            return '<li class="nav-item">
                        <div class="nav-link d-flex">
                            <a class="nav-link fw-bold d-flex align-items-center p-0" href="/profile">
                                <img src="/public/img/profile/'.($userData['profile_picture'] ? 'user/'.$id.'.png' : 'default.png').'" alt="Photo de profil" class="profile-picture me-2" id="profileImage">
                                <span>' . htmlspecialchars($userData['first_name']) . ' ' . htmlspecialchars($userData['last_name']) . '</span>
                            </a>
                            <a href="/logout" class="nav-link p-0 ms-2"><i class="bi bi-power"></i></a>
                        </div>
                    </li>';
        if (!$home)
            return '<li class="nav-item align-content-center ms-2">
                        <form method="post" action="/login" class="m-0">
                            <button type="submit" name="connection" value="1" class="btn btn-success btn-sm">Connexion</button>
                        </form>
                    </li>';
        return '<li class="nav-item align-content-center ms-2">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</button>
                </li>';
    }

    public static function footer($userData) : string {
        return '<footer class="py-4">
                    <div class="container text-center">
                        <p>&copy; 2024 Mon CV/Portfolio</p>
                        <div class="mt-3">
                            <a href="https://www.instagram.com/erwnn_20/" target="_blank" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" target="_blank" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://github.com/erwnn20" target="_blank" class="text-light me-3"><i class="fab fa-github"></i></a>
                            <a href="https://github.com/erwnn20/PHP-TP" target="_blank" class="text-light"><i class="fab bi-download"></i></a>'.
                            (isset($userData['admin']) && $userData['admin'] ? '<a href="/admin" class="text-light ms-3"><i class="fab bi-gear-fill"></i></a>' : '').'
                        </div>
                    </div>
                </footer>';
    }
}
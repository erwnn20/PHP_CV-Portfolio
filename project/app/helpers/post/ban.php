<?php

require_once 'app/helpers/uuid.php';
require_once 'app/models/projects-data.php';
require_once 'app/models/user-data.php';

session_start();
global $pdo;

if (isset($_POST['banType'])) {
    User::ban(
        banID: UUID::generate(),
        adminID: $_SESSION['user']['id'],
        userID: $_POST['banId'],
        banCause: $_POST['banCause'],
        banMessage: $_POST['banMessage'],
        banTable: $_POST['banType']
    );
}

if (isset($_POST['userUnban'])) User::unban($_POST['userUnban']);

if (isset($_POST['projectUnban'])) Projects::unban($_POST['projectUnban']);

header("Location: /admin");
exit;

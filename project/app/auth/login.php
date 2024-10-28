<?php

require_once 'app/helpers/uuid.php';
require_once 'app/models/user-data.php';

session_start();
global $pdo;

if (isset($_POST['loginEmail'])) {
    $loginEmail = $_POST['loginEmail'];
    $data = User::login($loginEmail);

    if (isset($data['password'])) {
        if (!$data['ban_id']) {
            if (password_verify($_POST['loginPassword'], $data['password'])) $_SESSION['user']['id'] = $data['id'];
            else $_SESSION['loginError'] = array(
                'loginEmail' => $loginEmail,
                'password' => true
            );
        } else $_SESSION['loginError'] = array(
            'loginEmail' => $loginEmail,
            'ban' => true
        );
    } else $_SESSION['loginError'] = array('email' => true);
}

if (isset($_POST['registerEmail'])) {
    $uuid = UUID::generate();
    $registerValue = array(
        'email' => $_POST['registerEmail'],
        'first_name' => $_POST['registerFirstName'],
        'last_name' => $_POST['registerLastName'],
        'password' => $_POST['registerPassword']
    );
    try {
        User::register(
            $uuid,
            $registerValue['email'],
            $registerValue['first_name'],
            $registerValue['last_name'],
            $registerValue['password']
        );
        $_SESSION['user']['id'] = $uuid;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['registerError'] = array(
                'email' => true,
                'value' => $registerValue
            );
        }
    }
}

if (isset($_POST['connection'])) {
    $_SESSION['loginError'] = array('external' => true);
}

header("Location: /");
exit;
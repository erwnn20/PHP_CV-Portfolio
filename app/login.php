<?php
session_start();
global $pdo;

if (isset($_POST['loginEmail'])) {
    $loginEmail = $_POST['loginEmail'];
    $stmt = $pdo->prepare('SELECT id, email, password, ban_id  FROM user WHERE email = :email');
    $stmt->execute(array('email' => $loginEmail));
    $data = $stmt->fetch();

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
    $uuid = uuid_v4();
    $registerValue = array(
        'email' => $_POST['registerEmail'],
        'first_name' => $_POST['registerFirstName'],
        'last_name' => $_POST['registerLastName'],
        'password' => $_POST['registerPassword']
    );
    try {
        $stmt = $pdo->prepare('INSERT INTO user (id, email, first_name, last_name, password) 
                                        VALUES (:id, :email, :first_name, :last_name, :password)');
        $stmt->execute(array(
            'id' => $uuid,
            'email' => $registerValue['email'],
            'first_name' => $registerValue['first_name'],
            'last_name' => $registerValue['last_name'],
            'password' => password_hash($registerValue['password'], PASSWORD_BCRYPT)
        ));
        $_SESSION['user']['id'] = $uuid;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['registerError'] = array('email' => true);
        }
    }
}

if (isset($_POST['connection'])) {
    $_SESSION['loginError'] = array('external' => true);
}

header("Location: /");
exit;
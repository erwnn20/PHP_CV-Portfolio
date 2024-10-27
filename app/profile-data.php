<?php
session_start();
require_once 'util/user.php';
global $pdo;

if (isset($_POST['email'])) {
    $newData = array(
        'id' => $_SESSION['user']['id'],
        'email' => $_POST['email'],
        'first_name' => $_POST['firstName'],
        'last_name' => $_POST['lastName'],
    );
    if (isset($_POST['newPassword'])) $newData['password'] = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
    if (User::saveImg('img/profile/user/','profilePicture' , $_SESSION['user']['id'])) {
        $newData['profile_picture'] = true;
    }

    $sql = 'UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name'
        .(isset($newData['password']) ? ', password = :password' : '')
        .(isset($newData['profile_picture']) ? ', profile_picture = :profile_picture' : '')
        .' WHERE id = :id;';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($newData);
}

if (isset($_POST['deletePicture'])) {
    $stmt = $pdo->prepare('UPDATE user SET profile_picture = FALSE WHERE id = :id;');
    $stmt->execute(array('id' => $_SESSION['user']['id']));
    User::deleteImg('img/profile/user/', $_SESSION['user']['id']);
}

$_SESSION['user']['data'] = User::getData($_SESSION['user']['id']);
header("Location: /profile");
exit;
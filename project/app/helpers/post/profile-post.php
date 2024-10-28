<?php

require_once 'app/models/img-data.php';
require_once 'app/models/user-data.php';

session_start();

if (isset($_POST['email'])) {
    $newData = array(
        'id' => $_SESSION['user']['id'],
        'email' => $_POST['email'],
        'first_name' => $_POST['firstName'],
        'last_name' => $_POST['lastName'],
    );
    if (isset($_POST['newPassword'])) $newData['password'] = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
    if (Images::save('profile/user/','profilePicture' , $_SESSION['user']['id'])) {
        $newData['profile_picture'] = true;
    }

    User::update($newData);
}

if (isset($_POST['deletePicture'])) {
    User::deleteProfilePicture($_SESSION['user']['id']);
    Images::delete('profile/user/', $_SESSION['user']['id']);
}

$_SESSION['user']['data'] = User::getData($_SESSION['user']['id']);
header("Location: /profile");
exit;
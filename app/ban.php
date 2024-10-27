<?php
session_start();
global $pdo;

if (isset($_POST['banType'])) {
    $banData = array(
        'banID' => uuid_v4(),
        'adminID' => $_SESSION['user']['id'],
        'banType' => $_POST['banType'],
        'banCause' => $_POST['banCause'],
        'banMessage' => $_POST['banMessage'],
        'bannedID' => $_POST['banId']
    );

    $stmt = $pdo->prepare('INSERT INTO ban (id, admin_id, cause, message)
                                    VALUES (:id, :admin_id, :cause, :message)
                                    ON DUPLICATE KEY
                                    UPDATE
                                        admin_id = VALUES(admin_id),
                                        cause = VALUES(cause),
                                        message = VALUES(message);');
    $stmt->execute(array(
        'id' => $banData['banID'],
        'admin_id' => $banData['adminID'],
        'cause' => $banData['banCause'],
        'message' => $banData['banMessage'],
    ));

    $stmt = $pdo->prepare('UPDATE '.$banData['banType'].' SET ban_id = :ban_id WHERE id = :id;');
    $stmt->execute(array(
        'id' => $banData['bannedID'],
        'ban_id' => $banData['banID'],
    ));
}

if (isset($_POST['userUnban'])) {
    $stmt = $pdo->prepare('DELETE FROM ban WHERE id = (SELECT ban_id FROM user WHERE id = :id);');
    $stmt->execute(array(
        'id' => $_POST['userUnban'],
    ));
}

if (isset($_POST['projectUnban'])) {
    $stmt = $pdo->prepare('DELETE FROM ban WHERE id = (SELECT ban_id FROM project WHERE id = :id);');
    $stmt->execute(array(
        'id' => $_POST['projectUnban'],
    ));
}

header("Location: /admin");
exit;
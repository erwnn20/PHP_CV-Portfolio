<?php

require_once 'db.php';

class User
{
    public static function getData($id) : array|bool
    {
        global $pdo;
        if ($id) {
            $stmt = $pdo->prepare('SELECT email, first_name, last_name, profile_picture, admin FROM user WHERE id = :id');
            $stmt->execute(array('id' => $id));
            return $stmt->fetch();
        }
        return array();
    }

    public static function saveImg($targetDirectory, $formName, $id): bool
    {
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $tmpName = $_FILES[$formName]['tmp_name'];
        $imageError = $_FILES[$formName]['error'];

        if ($imageError === UPLOAD_ERR_OK) {
            $uniqueName =  $id . '.png';
            move_uploaded_file($tmpName, $targetDirectory . $uniqueName);

            return true;
        }

        return false;
    }

    public static function deleteImg($targetDirectory, $id): bool
    {
        $filePath = $targetDirectory . $id . '.png';
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
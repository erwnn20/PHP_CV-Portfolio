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

    public static function getList(bool $withAdmin = true): array|bool
    {
        $sql = $withAdmin ?
            'SELECT id, first_name, last_name FROM user' :
            'SELECT 
                user.id as user_id, 
                user.first_name as user_first_name, 
                user.last_name as user_last_name,
                user.email as user_email,
                user.profile_picture as user_img,
                ban.cause as ban_cause,
                ban.message as ban_message,
                admin.first_name as admin_first_name,
                admin.last_name as admin_last_name
            FROM user
                LEFT JOIN ban ON ban.id = user.ban_id
                LEFT JOIN user admin ON admin.id = ban.admin_id
            WHERE user.admin IS FALSE';

        global $pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function saveImg($targetDirectory, $formName, $id): bool
    {
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $tmpName = @$_FILES[$formName]['tmp_name'];
        $imageError = @$_FILES[$formName]['error'];

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
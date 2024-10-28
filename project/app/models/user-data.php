<?php

require_once 'db.php';

class User
{
    public static function getData($id): array|bool
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

    public static function login($email): array|bool
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT id, email, password, ban_id  FROM user WHERE email = :email');
        $stmt->execute(array('email' => $email));
        return $stmt->fetch();
    }

    public static function register($id, $email, $firstName, $lastName, $password): void
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO user (id, email, first_name, last_name, password) 
                                        VALUES (:id, :email, :first_name, :last_name, :password)');
        $stmt->execute(array(
            'id' => $id,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ));
    }

    public static function update($data): void
    {
        global $pdo;
        $sql = 'UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name'
            .(isset($data['password']) ? ', password = :password' : '')
            .(isset($data['profile_picture']) ? ', profile_picture = :profile_picture' : '')
            .' WHERE id = :id;';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }

    public static function deleteProfilePicture($userID): void
    {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE user SET profile_picture = FALSE WHERE id = :id;');
        $stmt->execute(array(
            'id' => $userID
        ));
    }

    public static function ban($banID, $adminID, $userID, $banCause, $banMessage, $banTable): void
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO ban (id, admin_id, cause, message)
                                    VALUES (:id, :admin_id, :cause, :message)
                                    ON DUPLICATE KEY
                                    UPDATE
                                        admin_id = VALUES(admin_id),
                                        cause = VALUES(cause),
                                        message = VALUES(message);');
        $stmt->execute(array(
            'id' => $banID,
            'admin_id' => $adminID,
            'cause' => $banCause,
            'message' => $banMessage,
        ));

        $stmt = $pdo->prepare("UPDATE $banTable SET ban_id = :ban_id WHERE id = :id;");
        $stmt->execute(array(
            'id' => $userID,
            'ban_id' => $banID,
        ));
    }

    public static function unban($userID): void
    {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM ban WHERE id = (SELECT ban_id FROM user WHERE id = :id);');
        $stmt->execute(array(
            'id' => $userID,
        ));
    }
}
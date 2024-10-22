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
}
<?php

require_once 'db.php';

class CV
{
    public static function getData($userID) : array
    {
        global $pdo;
        if (isset($userID)) {
            $stmt = $pdo->prepare('SELECT title, description, skills, certificates, experiences FROM cv WHERE creator_id = :id');
            $stmt->execute(array(
                'id' => $userID
            ));
            return $stmt->fetch();
        }
        return array();
    }
}
<?php

require_once 'db.php';

class Projects
{
    public static function getData($userID = null, $projectID = null) : array
    {
        global $pdo;
        if (isset($userID)) {
            $stmt = $pdo->prepare('SELECT id, title, description, theme, link, images FROM project WHERE creator_id = :id');
            $stmt->execute(array(
                'id' => $userID
            ));
            return $stmt->fetchAll();
        }
        if (isset($projectID)) {
            $stmt = $pdo->prepare('SELECT * FROM project WHERE id = :id');
            $stmt->execute(array(
                'id' => $projectID
            ));
            return $stmt->fetch();
        }
        return array();
    }
}
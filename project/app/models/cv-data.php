<?php

require_once 'app/helpers/uuid.php';
require_once 'db.php';

class CV
{
    public static function getData($userID): array|bool
    {
        global $pdo;
        if (isset($userID)) {
            $stmt = $pdo->prepare('SELECT id, image, title, description, email, phone_number, address, skills, languages, interests, certificates, experiences 
                                            FROM cv WHERE creator_id = :id');
            $stmt->execute(array(
                'id' => $userID
            ));
            return $stmt->fetch();
        }
        return array();
    }

    public static function update(string $dbDataID, $userID, $data): void
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, ' . $dbDataID . ') VALUES (:id, :creator_id, :' . $dbDataID . ') ON DUPLICATE KEY UPDATE ' . $dbDataID . ' = VALUES(' . $dbDataID . ')');
        $stmt->execute(array(
            'id' => UUID::generate(),
            'creator_id' => $userID,
            $dbDataID => is_array($data) ? json_encode($data) : $data
        ));
    }

    public static function setImg(bool $set, $cvID): void {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE cv SET image = '.($set ? 'TRUE' : 'FALSE').' WHERE id = :id');
        $stmt->execute(array(
            'id' => $cvID,
        ));
    }
}
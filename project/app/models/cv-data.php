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

    public static function getStyle($userID = null, $cvID = null): array|bool
    {
        global $pdo;
        if (isset($userID)) {
            $stmt = $pdo->prepare('SELECT background, text_color, background_2, text_color_2 
                                            FROM cv_style WHERE cv_id = (
                                                SELECT id FROM cv WHERE creator_id = :id
                                            )');
            $stmt->execute(array(
                'id' => $userID
            ));
            return $stmt->fetch();
        }
        if (isset($cvID)) {
            $stmt = $pdo->prepare('SELECT background, text_color, background_2, text_color_2 
                                            FROM cv_style WHERE cv_id = :id');
            $stmt->execute(array(
                'id' => $cvID
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

    public static function updateStyle($cvID, $style): void
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO cv_style (cv_id, background, text_color, background_2, text_color_2)
                                    VALUES (:cv_id, :background, :text_color, :background_2, :text_color_2)
                                    ON DUPLICATE KEY
                                    UPDATE
                                        background = VALUES(background),
                                        text_color = VALUES(text_color),
                                        background_2 = VALUES(background_2),
                                        text_color_2 = VALUES(text_color_2);');
        $stmt->execute(array(
            'cv_id' => $cvID,
            'background' => $style['background'],
            'text_color' => $style['text_color'],
            'background_2' => $style['background_2'],
            'text_color_2' => $style['text_color_2']
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

function my_print($obj, $prefix = null): void
{
    echo '<pre>'.($prefix ? $prefix.' - ' : '').print_r($obj, true).'</pre>';
}
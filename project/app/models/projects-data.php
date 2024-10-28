<?php

require_once 'db.php';

class Projects
{
    public static function getData($userID = null, $projectID = null, bool $forBan = false): array|bool
    {
        global $pdo;
        if (isset($userID)) {
            $stmt = $pdo->prepare('SELECT id, title, description, theme, link, images, ban_id FROM project WHERE creator_id = :id');
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
        if ($forBan) {
            $stmt = $pdo->prepare(
                'SELECT 
                    project.id as project_id, 
                    project.title as project_title, 
                    project.theme as project_theme, 
                    project.link as project_link, 
                    creator.first_name as creator_first_name, 
                    creator.last_name as creator_last_name,
                    ban.cause as ban_cause,
                    ban.message as ban_message,
                    admin.first_name as admin_first_name,
                    admin.last_name as admin_last_name
                FROM project
                    LEFT JOIN user creator ON creator.id = project.creator_id
                    LEFT JOIN ban ON ban.id = project.ban_id
                    LEFT JOIN user admin ON admin.id = ban.admin_id'
            );
            $stmt->execute();
            return $stmt->fetchAll();
        }
        return array();
    }

    public static function update($ID, $userID, $title, $description, $theme, $link, $imgPostValue): void
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO project (id, creator_id, title, description, theme, link, images)
                                    VALUES (:id, :creator_id, :title, :description, :theme, :link, :images)
                                    ON DUPLICATE KEY
                                    UPDATE
                                        title = VALUES(title),
                                        description = VALUES(description),
                                        theme = VALUES(theme),
                                        link = VALUES(link),
                                        images = VALUES(images);');
        $stmt->execute(array(
            'id' => $ID,
            'creator_id' => $userID,
            'title' => $title,
            'description' => $description,
            'theme' => $theme,
            'link' => $link,
            'images' => json_encode(Images::saveFolder('projects/', $ID, $imgPostValue)),
        ));
    }

    public static function delete($ID): void
    {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM project WHERE id = :id');
        $stmt->execute(array(
            'id' => $ID
        ));
    }

    public static function unban($projectID): void
    {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM ban WHERE id = (SELECT ban_id FROM project WHERE id = :id);');
        $stmt->execute(array(
            'id' => $projectID,
        ));
    }
}
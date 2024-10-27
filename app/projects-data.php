<?php
session_start();
global $pdo;

function saveProjectImg($projectID): array
{
    $files_name = array();

    $targetDirectory = 'img/projects/' . $projectID . '/';
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }

    foreach ($_FILES['projectImages']['name'] as $key => $imageName) {
        $tmpName = $_FILES['projectImages']['tmp_name'][$key];
        $imageError = $_FILES['projectImages']['error'][$key];

        if ($imageError === UPLOAD_ERR_OK) {
            $uniqueName = 'img-' . $key . '.png';
            $files_name[] = $uniqueName;

            move_uploaded_file($tmpName, $targetDirectory . $uniqueName);
        }
    }
    return $files_name;
}

function deleteProjectImg($projectID): void
{
    $dir = 'img/projects/' . $projectID . '/';
    $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($dir);
}

if (isset($_POST['projectTitle'])) {
    $uuid = $_POST['projectId'] ?: uuid_v4();
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
        'id' => $uuid,
        'creator_id' => $_SESSION['user']['id'],
        'title' => $_POST['projectTitle'],
        'description' => $_POST['projectDescription'],
        'theme' => $_POST['projectTheme'],
        'link' => $_POST['projectLink'],
        'images' => json_encode(saveProjectImg($uuid)),
    ));
}

if (isset($_POST['editProjectId'])) {
    $_SESSION['editProjectId'] = $_POST['editProjectId'];
}

if (isset($_POST['deleteProjectId'])) {
    deleteProjectImg($_POST['deleteProjectId']);
    $stmt = $pdo->prepare('DELETE FROM project WHERE id = :id;');
    $stmt->execute(array(
        'id' => $_POST['deleteProjectId']
    ));
}

header("Location: /projects/edit");
exit;
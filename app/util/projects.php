<?php

require_once 'db.php';

class Projects
{
    public static function getData($userID = null, $projectID = null, bool $forBan = false): array|bool
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

    private static function displayImg(array|null $images, int $index, $projectID): void
    {
        if ($images) {
            if (count($images) > 1) {
                echo '  <div id="carouselProject-' . $index . '" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">';
                foreach ($images as $image_i => $image)
                    echo '      <button type="button" data-bs-target="#carouselProject-' . $index . '" 
                                    data-bs-slide-to="' . $image_i . '"' . ($image_i == 0 ? ' class="active" aria-current="true"' : '') . '>
                                </button>';
                echo '      </div>
                            <div class="carousel-inner">';
                foreach ($images as $image_i => $image)
                    echo '      <div class="carousel-item ' . ($image_i == 0 ? ' active' : '') . '">
                                    <img src="img/projects/' . $projectID . '/' . $image . '" class="project-image rounded-top w-100" alt="project_image-' . $image_i . '">
                                </div>';
                echo '      </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>';
            } else echo '<img src="img/projects/' . $projectID . '/' . $images[0] . '" class="card-img-top project-image" alt="no image project">';
        } else if ($images !== null)
            echo '<img src="img/projects/no_img.png" class="card-img-top project-image" alt="no image project">';
    }

    // on index.php
    public static function displayCard_home($id, $title, $description, $theme, $link, array|null $images, int $index): void
    {
        echo '<div class="col-md-4 mb-4">
                <div class="card">';
        self::displayImg($images, $index, $id);
        echo '        <div class="card-body d-flex flex-column">
                        <div class="d-flex flex-wrap align-items-center mb-2">
                            <h5 class="card-title p-2 mb-0">' . htmlspecialchars($title) . '</h5>
                            <span class="badge rounded-pill text-bg-primary">' . htmlspecialchars($theme) . '</span>' .
                    ($link ?
                            '<a href="' . $link . '" target="_blank" class="btn btn-sm btn-primary btn-custom ms-auto">Voir le projet</a>' : '') .
                        '</div>
                        <p class="card-text mb-1">' . nl2br(htmlspecialchars($description)) . '</p>
                    </div>
                </div>
            </div>';
    }

    // on projects-edit.php
    public static function displayCard_projectsEdit($id, $title, $description, $theme, $link, $images, int $index, bool $endBtn): void
    {
        echo '<div class="col">
                <div class="card project-card">';
        self::displayImg($images, $index, $id);
        echo '      <div class="card-body d-flex flex-column">
                        <div class="d-flex flex-wrap align-items-center mb-2">
                            <h5 class="card-title text-break p-2 ps-0 m-0">' . htmlspecialchars($title) . '</h5>
                            <span class="badge rounded-pill text-bg-primary ms-auto">' . htmlspecialchars($theme) . '</span>
                        </div>
                        <p class="card-text">' . nl2br(htmlspecialchars($description)) . '</p>
                        <div class="d-flex mt-auto">' .
                    ($link ?
                            '<a href="' . $link . '" class="btn btn-sm btn-outline-primary btn-custom me-auto" target="_blank">
                                Voir le projet
                            </a>' : '') .
                    ($endBtn ?
                            '<form method="post" class="ms-auto">
                                <button type="submit" class="btn btn-sm btn-outline-secondary" name="editProjectId" value="' . $id . '">Modifier</button>
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1" name="deleteProjectId" value="' . $id . '">Supprimer</button>
                            </form>' : '') .
                        '</div>
                    </div>
                </div>
            </div>';
    }

    // on profile.php
    public static function displayCard_profile($id, $title, $description, $theme, $link, array $images, int $index): void
    {
        echo '<div class="project-item row">
                <div class="col-md-8 mb-2">
                    <div class="d-flex flex-wrap align-items-center mb-2">
                        <h4 class="card-title text-break py-2 pe-2 m-0">'.htmlspecialchars($title).'</h4>
                        <span class="badge rounded-pill text-bg-primary">'.htmlspecialchars($theme).'</span>
                    </div>
                    <p class="mb-3">'.nl2br(htmlspecialchars($description)).'</p>'.
                ($link ?
                    '<a href="#" class="btn btn-sm btn-primary btn-custom mt-auto" target="_blank">
                        Voir le projet
                    </a>' : '').'
                </div>';
        if ($images) {
            echo '<div class="col-md-4">';
            if (count($images) > 1) {
                echo '<div id="carouselProject-' . $index . '" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">';
                foreach ($images as $image_i => $image)
                    echo '  <button type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide-to="' . $image_i . '"' .
                        ($image_i === 0 ? ' class="active" aria-current="true"' : '') . '>
                            </button>';
                echo '  </div>
                        <div class="carousel-inner rounded">';
                foreach ($images as $image_i => $image)
                    echo '  <div class="carousel-item' . ($image_i === 0 ? ' active' : '') . '">
                                <img src="img/projects/' . $id . '/' . $image . '" class="project-img" alt="project image">
                            </div>';
                echo '  </div>
                    </div>';
            } else echo '<img src="img/projects/' . $id . '/' . $images[0] . '" class="project-img rounded" alt="project image">';
            echo '</div>';
        }
        echo '</div>';
    }
}
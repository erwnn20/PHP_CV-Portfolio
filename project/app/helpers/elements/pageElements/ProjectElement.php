<?php

require_once 'app/helpers/elements/ProjectsElements.php';

class ProjectElement
{
    public static function displayProjectCard($id, $title, $description, $theme, $link, $images, bool $ban, int $index, bool $endBtn): void
    {
        echo '<div class="col">
                <div class="card project-card">';
        ProjectsElements::displayImg($images, $index, $id);
        echo '      <div class="card-body d-flex flex-column">' .
            ($ban ?
                '<span id="loginEmailBan" class="text-danger fst-italic ms-1" style="font-size: .9rem;" role="alert">
                            <i class="bi-exclamation-circle"></i>
                            Ce projet a été masquer par un administrateur, il n\'est donc visible que par vous.<br>
                            Contactez-nous pour savoir pourquoi.
                        </span>' : '') . '
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
}
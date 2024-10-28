<?php

class ProfileElement
{
    public static function displayExperienceCard($role, $company, $start_date, $end_date): void
    {
        echo '<div class="experience-item">
                <h4 class="mb-1">' . htmlspecialchars($role) . '</h4>
                <p class="experience-company fw-medium">' . htmlspecialchars($company) . '</p>' .
            ($start_date ?
                '<p>
                    <small>' . date_format(date_create($start_date), "F Y") . ' - ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') . '</small>
                </p>' : '') . '
            </div>';
    }

    public static function displayProjectCard($id, $title, $description, $theme, $link, array $images, bool $ban, int $index): void
    {
        echo '<div class="project-item row">
                <div class="col-md-8 mb-2">
                    <div class="d-flex flex-wrap align-items-center mb-2">
                        <h4 class="card-title text-break py-2 pe-2 m-0">' . htmlspecialchars($title) . '</h4>
                        <span class="badge rounded-pill text-bg-primary">' . htmlspecialchars($theme) . '</span>
                    </div>' .
            ($ban ?
                '<span id="loginEmailBan" class="text-danger fst-italic ms-1" style="font-size: .9rem;" role="alert">
                        <i class="bi-exclamation-circle"></i>
                        Ce projet a été masquer par un administrateur, il n\'est donc visible que par vous.
                        Contactez-nous pour savoir pourquoi.
                    </span>' : '') . '
                    <p class="mb-3">' . nl2br(htmlspecialchars($description)) . '</p>' .
            ($link ?
                '<a href="' . $link . '" class="btn btn-sm btn-primary btn-custom mt-auto" target="_blank">
                        Voir le projet
                    </a>' : '') . '
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
                                <img src="/public/img/projects/' . $id . '/' . $image . '" class="project-img" alt="project image">
                            </div>';
                echo '  </div>
                    </div>';
            } else echo '<img src="/public/img/projects/' . $id . '/' . $images[0] . '" class="project-img rounded" alt="project image">';
            echo '</div>';
        }
        echo '</div>';
    }
}
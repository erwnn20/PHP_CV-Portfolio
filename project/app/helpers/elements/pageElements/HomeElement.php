<?php

require_once 'app/helpers/elements/ProjectsElements.php';

class HomeElement
{
    public static function displaySkill($skill, $year_exp): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                ' . htmlspecialchars($skill) . '
                <span class="badge rounded-pill bg-custom">' . $year_exp . ' an' . ($year_exp > 1 ? 's' : '') . '</span>
            </li>';
    }

    public static function displayExperienceCard($role, $company, $start_date, $end_date): void
    {
        echo '<div class="timeline-item">' .
            ($start_date ?
                '<div class="timeline-date">' .
                date_format(date_create($start_date), "F Y") . ' - ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .
                '</div>' : '') . '
                <h5 class="timeline-title">' . htmlspecialchars($role) . '</h5>
                <p class="timeline-subtitle">' . htmlspecialchars($company) . '</p>
            </div>';
    }

    public static function displayCertificatesCard($certificate, $school, $year): void
    {
        echo '<div class="timeline-item">' .
            ($year ?
                '<div class="timeline-date">' . date_format(date_create($year), "Y") . '</div>' : '') . '
                <h5 class="timeline-title">' . htmlspecialchars($certificate) . '</h5>
                <p class="timeline-subtitle">' . htmlspecialchars($school) . '</p>
            </div>';
    }

    public static function displayProjectCard($id, $title, $description, $theme, $link, array|null $images, int $index): void
    {
        echo '<div class="col-md-4 mb-4">
                <div class="card">';
        ProjectsElements::displayImg($images, $index, $id);
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
}
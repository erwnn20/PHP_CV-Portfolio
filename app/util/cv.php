<?php

require_once 'db.php';

class CV
{
    public static function getData($userID) : array|bool
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

    // on index.php
    public static function displaySkill_home($skill, $year_exp): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                '.htmlspecialchars($skill).'
                <span class="badge rounded-pill bg-custom">'.$year_exp.' an'.($year_exp > 1 ? 's': '').'</span>
            </li>';
    }

    public static function displayExperienceCard_home($role, $company, $start_date, $end_date): void
    {
        echo '<div class="timeline-item">'.
        ($start_date ?
                '<div class="timeline-date">'.
                    date_format(date_create($start_date), "F Y") . ' - ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .
                '</div>' : '').'
                <h5 class="timeline-title">'.htmlspecialchars($role).'</h5>
                <p class="timeline-subtitle">'.htmlspecialchars($company).'</p>
            </div>';
    }

    public static function displayCertificatesCard_home($certificate, $school, $year): void
    {
        echo '<div class="timeline-item">'.
            ($year ?
                '<div class="timeline-date">' . date_format(date_create($year), "Y") . '</div>' : '').'
                <h5 class="timeline-title">' . htmlspecialchars($certificate) . '</h5>
                <p class="timeline-subtitle">' . htmlspecialchars($school) . '</p>
            </div>';
    }

    // on cv-edit.php
    public static function displaySkill_cvEdit($skill, $year_exp, int $index): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="fw-bold">'.htmlspecialchars($skill).'</span>
                    <span class="badge rounded-pill bg-custom ms-2">'.$year_exp.' an'.($year_exp > 1 ? 's': '').'</span>
                </div>
                <form method="post" class="d-flex align-items-center">
                    <button type="submit" name="delSkillIndex" value="'.$index.'"  class="btn btn-close"></button>
                </form>
            </li>';
    }

    public static function displayLang_cvEdit($lang, $lvl, array $lvlArray, int $index): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                '.htmlspecialchars($lang).' - '.$lvlArray[$lvl].'
                <form method="post" class="d-flex align-items-center">
                    <button type="submit" name="delLangIndex" value="'.$index.'"  class="btn btn-close"></button>
                </form>
            </li>';
    }

    public static function displayInterest_cvEdit($interest, int $index): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                '.htmlspecialchars($interest).'
                <form method="post" class="d-flex align-items-center">
                    <button type="submit" name="delInterestIndex" value="'.$index.'"  class="btn btn-close"></button>
                </form>
            </li>';
    }

    public static function displayExperienceCard_cvEdit($role, $company, $tasks, $start_date, $end_date, int $index, bool $delBtn): void
    {
        echo '<div class="col">
                <div class="card bg-dark shadow">
                    <div class="card-body">
                        <div class="d-flex mb-2">
                            <h5 class="card-title text-break mb-0">' . htmlspecialchars($role) . '</h5>' .
            ($delBtn ?
                '            <form method="post" class="ms-auto">
                                <button type="submit" name="delExpIndex" value="' . $index . '" class="btn btn-sm btn-outline-danger ms-1">Supprimer</button>
                             </form>' : '') . '
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted">' . htmlspecialchars($company) . '</h6>' .
            ($start_date ?
                '       <p class="mb-0">
                            <small class="text-muted">
                                Du ' . date_format(date_create($start_date), "F Y") . ' au ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .
                '           </small>
                        </p>' : '') .
            '           <ul class="tasks mb-0">';
        if ($tasks) foreach ($tasks as $task)
            echo '          <li>' . htmlspecialchars($task) . '</li>';
        echo '          </ul>
                    </div>
                </div>
            </div>';
    }

    public static function displayCertificatesCard_cvEdit($certificate, $school, $year, int $index, bool $delBtn): void
    {
        echo '<div class="col">
                <div class="card bg-dark shadow">
                    <div class="card-body">
                        <h5 class="card-title text-break">' . htmlspecialchars($certificate) . '</h5>
                        <h6 class="card-subtitle text-muted">' . htmlspecialchars($school) . '</h6>
                        <div class="d-flex">' .
                    ($year ?
                            '<p class="mb-0">
                                <small class="text-muted h-100">
                                    En '.$year.'
                                </small>
                            </p>' : '') .
                    ($delBtn ? '
                            <form method="post" class="ms-auto">
                                <button type="submit" name="delCertificateIndex" value="' . $index . '" class="btn btn-outline-danger btn-sm">Supprimer</button>
                            </form>' : '') .
                        '</div>
                    </div>
                </div>
            </div>';
    }

    // on profile.php
    public static function displayExperienceCard_profile($role, $company, $start_date, $end_date): void
    {
        echo '<div class="experience-item">
                <h4 class="mb-1">'.htmlspecialchars($role).'</h4>
                <p class="experience-company fw-medium">'.htmlspecialchars($company).'</p>'.
            ($start_date ?
                '<p>
                    <small>' . date_format(date_create($start_date), "F Y") . ' - ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .'</small>
                </p>' : '').'
            </div>';
    }
}
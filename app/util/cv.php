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
    public static function displayExperienceCard_home($role, $company, $start_date, $end_date, bool $margin): void
    {
        echo '<div class="card' . ($margin ? ' mb-3' : '') . '">
                <div class="card-body">
                    <h5 class="card-title">' . $role . '</h5>
                    <h6 class="card-subtitle mb-2">' . $company . '</h6>'.
                ($start_date ?
                    '<p class="card-text">' .
                        date_format(date_create($start_date), "F Y") . ' - ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .
                    '</p>' : '').
                '</div>
              </div>';
    }

    public static function displayCertificatesCard_home($degree, $school, $year, bool $margin): void
    {
        echo '<div class="card' . ($margin ? ' mb-3' : '') . '">
                <div class="card-body">
                    <h5 class="card-title">' . $degree . '</h5>
                    <h6 class="card-subtitle mb-2">' . $school . '</h6>'.
                ($year ?
                    '<p class="card-text">' . date_format(date_create($year), "Y") . '</p>' : '').
                '</div>
              </div>';
    }

    // on cv-edit.php
    public static function displaySkill_cvEdit($skill, $year_exp, int $index): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="fw-bold">'.$skill.'</span>
                    <span class="badge rounded-pill text-bg-primary ms-2">'.$year_exp.' an'.($year_exp > 1 ? 's': '').'</span>
                </div>
                <form method="post" class="d-flex align-items-center">
                    <button type="submit" name="delSkillIndex" value="'.$index.'"  class="btn btn-close"></button>
                </form>
            </li>';
    }

    public static function displayLang_cvEdit($lang, $lvl, array $lvlArray, int $index): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                '.$lang.' - '.$lvlArray[$lvl].'
                <form method="post" class="d-flex align-items-center">
                    <button type="submit" name="delLangIndex" value="'.$index.'"  class="btn btn-close"></button>
                </form>
            </li>';
    }

    public static function displayInterest_cvEdit($interest, int $index): void
    {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                '.$interest.'
                <form method="post" class="d-flex align-items-center">
                    <button type="submit" name="delInterestIndex" value="'.$index.'"  class="btn btn-close"></button>
                </form>
            </li>';
    }

    public static function displayExperienceCard_cvEdit($role, $company, $start_date, $end_date, int $index, bool $delBtn): void
    {
        echo '<div class="col">
                <div class="card bg-dark shadow">
                    <div class="card-body">
                        <h5 class="card-title mb-3">' . $role . '</h5>
                        <h6 class="card-subtitle mb-2 text-muted">' . $company . '</h6>
                        <div class="d-flex">' .
                    ($start_date ?
                            '<p class="mb-0">
                                <small class="text-muted">
                                    Du ' . date_format(date_create($start_date), "F Y") . ' au ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .
                                '</small>
                            </p>' : '') .
                    ($delBtn ?
                            '<form method="post" class="ms-auto">
                                <button type="submit" name="delExpIndex" value="' . $index . '" class="btn btn-outline-danger btn-sm">Supprimer</button>
                            </form>' : '') .
                        '</div>
                    </div>
                </div>
            </div>';
    }

    public static function displayCertificatesCard_cvEdit($degree, $school, $year, int $index, bool $delBtn): void
    {
        echo '<div class="col">
                <div class="card bg-dark shadow">
                    <div class="card-body">
                        <h5 class="card-title text-break">' . $degree . '</h5>
                        <h6 class="card-subtitle text-muted">' . $school . '</h6>
                        <div class="d-flex">' .
                    ($year ?
                            '<p class="mb-0">
                                <small class="text-muted h-100">
                                    En ' . date_format(date_create($year), "Y") .
                                '</small>
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
                <h4>'.$role.'</h4>
                <p class="mb-2">'.$company.'</p>'.
            ($start_date ?
                '<p class="mb-0">
                    <small>' . date_format(date_create($start_date), "F Y") . ' - ' . ($end_date ? date_format(date_create($end_date), "F Y") : 'Present') .'</small>
                </p>' : '').'
            </div>';
    }
}
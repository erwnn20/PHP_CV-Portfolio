<?php

require_once 'app/models/cv-data.php';
require_once 'app/models/user-data.php';

ob_start();
session_start();

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['loginError'] = array('external' => true);

    header("Location: /");
    exit;
}

//

$userData = User::getData($_SESSION['user']['id']);
$cvData = CV::getData($_SESSION['user']['id']);
?>
    <link rel="stylesheet" href="/public/styles/download.css">
    <style type="text/css">
        body {
            background-color: #e0e0e0;
            color: #212529;
        }

        .cv-section h2 {
            border-color: #5c3ba4;
        }

        .personal-info {
            background-color: #5c3ba4;
            color: #e0e0e0;
        }

        .personal-info h2 {
            color: #e0e0e0;
        }

        .personal-info .cv-section h2 {
            border-color: #212529;
        }
    </style>
<?php
$stylesheet = ob_get_contents();
ob_end_clean();
ob_start();
?>
    <body>
    <div id="cvContent">
        <div class="row">
            <div class="col left-section p-4 text-center personal-info">
                <img src="<?php echo __DIR__ . '/../../public/img/'.(isset($cvData['image']) && $cvData['image'] ? 'cv/' . $cvData['id'] . '.png' : 'profile/default.png') ?>"
                     alt="Photo de profil" class="profile-image mb-3">
                <h2 id="userName"><?php echo htmlspecialchars($userData['first_name'] ?? '') . ' ' . htmlspecialchars($userData['last_name'] ?? '') ?></h2>
                <p id="userTitle"><?php echo htmlspecialchars($cvData['title'] ?? '') ?></p>

                <div class="cv-section">
                    <h2>Coordonnées</h2>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="fas me-1" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                        </svg>
                        <span id="userEmail"><?php echo ($cvData['email'] ?? 'Pas enregistré') ?></span>
                    </p>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="fas me-1" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                        </svg>
                        <span id="userPhone"><?php echo ($cvData['phone_number'] ?? 'Pas enregistré') ?></span>
                    </p>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="fas me-1" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                        </svg>
                        <span id="userAddress"><?php echo htmlspecialchars($cvData['address'] ?? 'Pas enregistré') ?></span>
                    </p>
                </div>

                <div class="cv-section">
                    <?php
                    $skills = json_decode($cvData['skills'] ?? '[]', true);
                    if ($skills) {
                        echo '<h2>Compétences</h2>
                                        <ul id="userSkills" class="list-inline">';
                        foreach ($skills as $skill)
                            echo '<li class="list-inline-item badge text-bg-dark me-2 mb-2">'. htmlspecialchars($skill['skill']) .'</li>';
                        echo '  </ul>';
                    }
                    ?>
                </div>
                <div class="cv-section">
                    <?php
                    $langLvl = array(
                        'native' => 'Langue maternelle',
                        'A1' => 'Débutant (A1)',
                        'A2' => 'Élémentaire (A2)',
                        'B1' => 'Intermédiaire (B1)',
                        'B2' => 'Avancé (B2)',
                        'C1' => 'Courant (C1)',
                        'C2' => 'Maîtrise (C2)'
                    );

                    $languages = json_decode($cvData['languages'] ?? '[]', true);
                    if ($languages) {
                        echo '<h2>Langues</h2>
                                        <ul id="userLanguages" class="list-unstyled">';
                        foreach ($languages as $language)
                            echo '<li>'.htmlspecialchars($language['lang']) . ' - ' . $langLvl[$language['level']] .'</li>';
                        echo '  </ul>';
                    }
                    ?>
                </div>
                <div class="cv-section">
                    <?php
                    $interests = json_decode($cvData['interests'] ?? '[]', true);
                    if ($interests) {
                        echo '<h2>Centres d\'intérêt</h2>
                                    <ul id="userInterests" class="list-inline">';
                        foreach ($interests as $interest)
                            echo '<li class="list-inline-item badge text-bg-light me-2 mb-2">'. htmlspecialchars($interest) .'</li>';
                        echo '  </ul>';
                    }
                    ?>
                </div>
            </div>

            <div class="col right-section p-4 ps-4">
                <div class="cv-section">
                    <h2>À propos de moi</h2>
                    <p id="userDescription"><?php echo nl2br(htmlspecialchars($cvData['description'] ?? 'Pas enregistré')) ?></p>
                </div>

                <div class="cv-section">
                    <?php
                    $experiences = json_decode($cvData['experiences'] ?? '[]', true);
                    if ($experiences) {
                        echo '<h2>Expériences professionnelles</h2>
                                        <div id="userExperiences">';
                        foreach ($experiences as $experience) {
                            echo '<div class="experience mb-3">
                                            <h4>' . htmlspecialchars($experience['role']) . '</h4>
                                            <h5 class="fs-6 fst-italic">' . htmlspecialchars($experience['company']) . '</h5>
                                            <p class="text-muted mb-1">' .
                                date_format(date_create($experience['start_date']), "F Y") . ' - ' . ($experience['end_date'] ? date_format(date_create($experience['end_date']), "F Y") : 'Present') . '
                                            </p>
                                            <ul>';
                            if (isset($experience['tasks'])) foreach ($experience['tasks'] as $task)
                                echo '<li>' . htmlspecialchars($task) . '</li>';
                            echo '  </ul>
                                          </div>';
                        }
                        echo '  </div>';
                    }
                    ?>
                </div>

                <div class="cv-section">
                    <?php
                    $certificates = json_decode($cvData['certificates'] ?? '[]', true);
                    if ($certificates) {
                        echo '<h2>Formation</h2>
                                        <div id="userEducation">';
                        foreach ($certificates as $certificate)
                            echo '<div class="mb-3">
                                            <h4>' . htmlspecialchars($certificate['degree']) . '</h4>
                                            <h5>' . htmlspecialchars($certificate['school']) . '</h5>
                                            <p class="text-muted">' . date_format(date_create($certificate['date']), "Y") . '</p>
                                          </div>';
                        echo '  </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    </body>
<?php
$html = ob_get_contents();
ob_end_clean();

require_once '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'margin_top' => 0,
    'margin_right' => -5,
    'margin_bottom' => 0,
    'margin_left' => 3
]);
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html);
$mpdf->Output('cv-'.$userData['first_name'].'-'.$userData['last_name'].'.pdf','D');
?>
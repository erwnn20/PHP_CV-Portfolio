<?php

require_once 'app/helpers/elements/pageElements/HomeElement.php';
require_once 'app/helpers/elements/pageElements/PageElement.php';
require_once 'app/models/cv-data.php';
require_once 'app/models/projects-data.php';
require_once 'app/models/user-data.php';

ob_start();
session_start();

//

$userSelectID = $_SESSION['user']['select'] ?? $_SESSION['user']['id'] ?? null;
$userData = User::getData($userSelectID);
$cvData = CV::getData($userSelectID);
$cvStyle = CV::getStyle(cvID: $cvData['id'] ?? 0);
$projectData = Projects::getData($userSelectID);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu du CV - Mon CV/Portfolio</title>

    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/styles/style.css">
    <link rel="stylesheet" href="/public/styles/resume.css">
    <style>
        .cv-info {
            background-color: <?php echo $cvStyle['background'] ?? 'inherit' ?>;
            color: <?php echo $cvStyle['text_color'] ?? 'inherit' ?>;
        }

        .cv-info .cv-section h2 {
            border-color: <?php echo $cvStyle['background_2'] ?? 'var(--purple-dark-0)' ?>;
        }

        .personal-info {
            background-color: <?php echo $cvStyle['background_2'] ?? 'var(--purple-dark-0)' ?>;
            color: <?php echo $cvStyle['text_color_2'] ?? 'inherit' ?>;
        }

        .personal-info .cv-section h2 {
            border-color: <?php echo $cvStyle['text_color'] ?? 'var(--black-0)' ?>;
        }
    </style>

</head>

<body data-bs-theme="dark">
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Mon CV/Portfolio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#cv">CV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#projects">Projets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#contact">Contact</a>
                    </li>
                    <?php echo PageElement::headerUser($_SESSION['user']['data'], $_SESSION['user']['id'] ?? 0) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container my-5">
            <h1 class="text-center mb-4">CV Professionnel</h1>

            <form method="post" class="d-flex w-50 mx-auto mb-4">
                <div class="flex-grow-1">
                    <label for="userSelect" class="form-label">Sélectionner un utilisateur :</label>
                    <div class="input-group">
                        <select class="form-select" id="userSelect" name="userSelect" required>
                            <?php
                            if ($userSelectID)
                                echo '<option value="' . $userSelectID . '">' . htmlspecialchars($userData['first_name']) . ' ' . htmlspecialchars($userData['last_name']) . '</option>';

                            $users = User::getList();
                            if ($users)
                                foreach ($users as $user) if ($user['id'] != $userSelectID)
                                    echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) . '</option>';
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-custom">Mettre à jour</button>
                    </div>
                </div>
                <?php
                if (isset($_SESSION['user']['id']) && $userSelectID === $_SESSION['user']['id'])
                    echo '<a href="/download" class="btn btn-outline-success ms-2 mt-auto"><i class="fab bi-download"></i></a>';
                ?>
            </form>

            <div id="cvContent">
                <div class="row">
                    <div class="col-md-4 p-4 text-center personal-info">
                        <img src="/public/img/<?php echo isset($cvData['image']) && $cvData['image'] ? 'cv/' . $cvData['id'] . '.png' : 'profile/default.png' ?>"
                             alt="Photo de profil" class="profile-image mb-3">
                        <h2 id="userName"><?php echo htmlspecialchars($userData['first_name'] ?? '') . ' ' . htmlspecialchars($userData['last_name'] ?? '') ?></h2>
                        <p id="userTitle"><?php echo htmlspecialchars($cvData['title'] ?? '') ?></p>

                        <div class="cv-section">
                            <h2>Coordonnées</h2>
                            <p><i class="fas fa-envelope me-2"></i> <span
                                        id="userEmail"><?php echo $cvData['email'] ?? 'Pas enregistré' ?></span></p>
                            <p><i class="fas fa-phone me-2"></i> <span
                                        id="userPhone"><?php echo $cvData['phone_number'] ?? 'Pas enregistré' ?></span></p>
                            <p><i class="fas fa-map-marker-alt me-2"></i> <span
                                        id="userAddress"><?php echo htmlspecialchars($cvData['address'] ?? 'Pas enregistré') ?></span></p>
                        </div>

                        <div class="cv-section">
                            <?php
                            $skills = json_decode($cvData['skills'] ?? '[]', true);
                            if ($skills) {
                                echo '<h2>Compétences</h2>
                                        <ul id="userSkills" class="list-inline">';
                                foreach ($skills as $skill)
                                    echo '<li class="list-inline-item badge text-bg-dark me-2 mb-2">' . htmlspecialchars($skill['skill']) . '</li>';
                                echo '</ul>';
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
                                    echo '<li>' . htmlspecialchars($language['lang']) . ' - ' . $langLvl[$language['level']] . '</li>';
                                echo '</ul>';
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
                                    echo '<li class="list-inline-item badge text-bg-light me-2 mb-2">' . htmlspecialchars($interest) . '</li>';
                                echo '</ul>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col-md-8 p-4 cv-info">
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
                                                <p class="mb-1">' .
                                        date_format(date_create($experience['start_date']), "F Y") . ' - ' . ($experience['end_date'] ? date_format(date_create($experience['end_date']), "F Y") : 'Present') . '
                                                </p>
                                                <ul>';
                                    if (isset($experience['tasks'])) foreach ($experience['tasks'] as $task)
                                        echo '    <li>' . htmlspecialchars($task) . '</li>';
                                    echo '      </ul>
                                            </div>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <div class="cv-section">
                            <?php
                            $certificates = json_decode($cvData['certificates'] ?? '[]', true);
                            if ($certificates) {
                                echo '<h2>Formation</h2>
                                        <div id="userEducation">';
                                foreach ($certificates as $certificate) {
                                    echo '<div class="mb-3">
                                                <h4>' . htmlspecialchars($certificate['degree']) . '</h4>
                                                <h5>' . htmlspecialchars($certificate['school']) . '</h5>
                                                <p>' . date_format(date_create($certificate['date']), "Y") . '</p>
                                            </div>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h2 class="text-center mb-4">Projets de l'utilisateur</h2>
                <div id="userProjects" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php
                    if ($projectData) {
                        foreach ($projectData as $project_i => $project) if (!$project['ban_id'])
                            HomeElement::displayProjectCard(
                                $project['id'],
                                $project['title'],
                                $project['description'],
                                $project['theme'],
                                $project['link'],
                                json_decode($project['images'], true),
                                $project_i
                            );
                    } else HomeElement::displayProjectCard(
                        -1,
                        'Pas de projet enregistrée',
                        isset($_SESSION['user']['id']) ? 'Gérez et  ajoutez vos projets personnels et professionnels' : 'Connectez vous pour afficher vos projets personnels et professionnels',
                        '',
                        '',
                        null,
                        0
                    );
                    ?>
                </div>
            </div>
        </div>
    </main>

    <?php echo PageElement::footer($_SESSION['user']['data']) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
ob_end_flush();

<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/elements.php';
require_once 'util/user.php';
require_once 'util/cv.php';
require_once 'util/projects.php';
global $pdo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    my_print($_POST);
    if (isset($_POST['userSelect'])) {
        $_SESSION['user']['select'] = $_POST['userSelect'];
    }

    if (isset($_POST['connection'])) {
        $_SESSION['loginError'] = array('external' => true);

        header("Location: /");
        exit;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

//

$userSelectID = $_SESSION['user']['select'] ?? $_SESSION['user']['id'] ?? null;
$userData = User::getData($userSelectID);
$cvData = CV::getData($userSelectID);
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
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/resume.css">
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
                    <?php echo Element::headerUser($_SESSION['user']['data'], $_SESSION['user']['id'] ?? 0) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container my-5">
            <h1 class="text-center mb-4">CV Professionnel</h1>

            <form method="post" class="mb-4">
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
            </form>

            <div id="cvContent">
                <div class="row">
                    <div class="col-md-4 pt-4 text-center personal-info">
                        <img src="<?php echo 'img/' . (isset($cvData['image']) ? 'cv/' . $cvData['id'] . '.png' : 'profile/default.png') ?>"
                             alt="Photo de profil" class="profile-image mb-3">
                        <h2 id="userName"><?php echo htmlspecialchars($userData['first_name']) . ' ' . htmlspecialchars($userData['last_name']) ?></h2>
                        <p id="userTitle"><?php echo htmlspecialchars($cvData['title']) ?></p>

                        <div class="cv-section">
                            <h2>Coordonnées</h2>
                            <p><i class="fas fa-envelope me-2"></i> <span
                                        id="userEmail"><?php echo $cvData['email'] ?></span></p>
                            <p><i class="fas fa-phone me-2"></i> <span
                                        id="userPhone"><?php echo $cvData['phone_number'] ?></span></p>
                            <p><i class="fas fa-map-marker-alt me-2"></i> <span
                                        id="userAddress"><?php echo htmlspecialchars($cvData['address']) ?></span></p>
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

                    <div class="col-md-8 pt-4 ps-4">
                        <div class="cv-section">
                            <h2>À propos de moi</h2>
                            <p id="userDescription"><?php echo nl2br(htmlspecialchars($cvData['description'])) ?></p>
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
                                                <p class="text-muted">' . date_format(date_create($certificate['date']), "Y") . '</p>
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
                        foreach ($projectData as $project_i => $project)
                            Projects::displayCard_home(
                                $project['id'],
                                $project['title'],
                                $project['description'],
                                $project['theme'],
                                $project['link'],
                                json_decode($project['images'], true),
                                $project_i
                            );
                    } else Projects::displayCard_home(
                        -1,
                        'Pas de projet enregistrée',
                        isset($_SESSION['user']['id']) ? 'Gerez et  ajoutez vos projets personnels et professionnels' : 'Connectez vous pour afficher vos projets personnels et professionnels',
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

    <?php echo Element::footer($_SESSION['user']['data']) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
ob_end_flush();

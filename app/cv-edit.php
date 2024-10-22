<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/user.php';
require_once 'util/cv.php';
global $pdo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cvTitle'])) {
        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, title) VALUES (:id, :creator_id, :title) ON DUPLICATE KEY UPDATE title = VALUES(title)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'title' => $_POST['cvTitle']
        ));
    }

    if (isset($_POST['cvDescription'])) {
        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, description) VALUES (:id, :creator_id, :description) ON DUPLICATE KEY UPDATE description = VALUES(description)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'description' => $_POST['cvDescription']
        ));
    }

    if (isset($_POST['newSkill'])) {
        $skillData = json_decode(CV::getData($_SESSION['user_id'])['skills'] ?? '[]', true);
        $skillData[] = $_POST['newSkill'];

        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, skills) VALUES (:id, :creator_id, :skills) ON DUPLICATE KEY UPDATE skills = VALUES(skills)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'skills' => json_encode($skillData)
        ));
    }

    if (isset($_POST['delSkillIndex'])) {
        $skillData = array();
        foreach (json_decode(CV::getData($_SESSION['user_id'])['skills'] ?? '[]', true) as $skill_i => $skill) {
            if ($skill_i != $_POST['delSkillIndex'])
                $skillData[] = $skill;
        }

        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, skills) VALUES (:id, :creator_id, :skills) ON DUPLICATE KEY UPDATE skills = VALUES(skills)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'skills' => json_encode($skillData)
        ));
    }

    if (isset($_POST['experienceTitle'])) {
        $expData = json_decode(CV::getData($_SESSION['user_id'])['experiences'] ?? '[]', true);
        $expData[] = array(
            'role' => $_POST['experienceTitle'],
            'company' => $_POST['experienceCompany'],
            'start_date' => $_POST['experienceStartDate'],
            'end_date' => $_POST['experienceEndDate'] ?? ''
        );

        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, experiences) VALUES (:id, :creator_id, :experiences) ON DUPLICATE KEY UPDATE experiences = VALUES(experiences)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'experiences' => json_encode($expData)
        ));
    }

    if (isset($_POST['delExpIndex'])) {
        $expData = array();
        foreach (json_decode(CV::getData($_SESSION['user_id'])['experiences'] ?? '[]', true) as $certificate_i => $certificate) {
            if ($certificate_i != $_POST['delExpIndex'])
                $expData[] = $certificate;
        }

        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, experiences) VALUES (:id, :creator_id, :experiences) ON DUPLICATE KEY UPDATE experiences = VALUES(experiences)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'experiences' => json_encode($expData)
        ));
    }

    if (isset($_POST['educationTitle'])) {
        $degData = json_decode(CV::getData($_SESSION['user_id'])['certificates'] ?? '[]', true);
        $degData[] = array(
            'degree' => $_POST['educationTitle'],
            'school' => $_POST['educationSchool'],
            'date' => $_POST['educationDate'],
        );

        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, certificates) VALUES (:id, :creator_id, :certificates) ON DUPLICATE KEY UPDATE certificates = VALUES(certificates)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'certificates' => json_encode($degData)
        ));
    }

    if (isset($_POST['delDegreeIndex'])) {
        $degData = array();
        foreach (json_decode(CV::getData($_SESSION['user_id'])['certificates'] ?? '[]', true) as $certificate_i => $certificate) {
            if ($certificate_i != $_POST['delDegreeIndex'])
                $degData[] = $certificate;
        }

        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, certificates) VALUES (:id, :creator_id, :certificates) ON DUPLICATE KEY UPDATE certificates = VALUES(certificates)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'certificates' => json_encode($degData)
        ));
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

$cv_data = CV::getData($_SESSION['user_id'] ?? 0);
$userInfo = User::getData($_SESSION['user_id'] ?? 0);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon CV - Mon CV/Portfolio</title>

    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cv.css">

    <style>
        .form-floating>.form-control-plaintext~label::after,
        .form-floating>.form-control:focus~label::after,
        .form-floating>.form-control:not(:placeholder-shown)~label::after,
        .form-floating>.form-select~label::after {
            background-color: transparent;
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
                        <a class="nav-link" href="/#cv">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Modifier CV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#projects">Projets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#contact">Contact</a>
                    </li>
                    <?php
                    if (isset($_SESSION['user_id']))
                        echo '<li class="nav-item d-flex">
                                    <div class="nav-link d-flex align-items-center">
                                        <a class="nav-link fw-bold p-0" href="profile.php">
                                            ' . $userInfo['first_name'] . ' ' . $userInfo['last_name'] . '
                                        </a>
                                        <a href="logout.php" class="nav-link align-content-center p-0 ms-2"><i class="bi bi-power"></i></a>
                                    </div>
                                </li>';
                    else echo '<li class="nav-item align-content-center ms-2">
                                    <form method="post" class="m-0">
                                        <button type="submit" name="connection" value="1" class="btn btn-success btn-sm">Connexion</button>
                                    </form>
                                </li>';
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container my-5">
            <div class="text-center mb-5">
                <h1>Modifier mon CV</h1>
                <?php if (!isset($_SESSION['user_id'])) echo '<p class="lead">Connectez vous pour modifier votre CV</p>' ?>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h2 class="card-title">Informations générales</h2>
                            <form method="post" id="cvInfoForm" class="flex-grow-1 d-flex flex-column">
                                <div class="form-floating mb-3">
                                    <?php echo '<input type="text" class="form-control form-control-lg" id="cvTitle" name="cvTitle" 
                                            value="' . ($cv_data['title'] ?? '') . '" placeholder' . (isset($_SESSION['user_id']) ? '' : ' disabled') . '>' ?>
                                    <label for="cvTitle" class="form-label">Titre du CV</label>
                                </div>
                                <div class="form-floating flex-grow-1 d-flex flex-column mb-3">
                                    <?php echo '<textarea class="form-control form-control-sm flex-grow-1" id="cvDescription" name="cvDescription" 
                                            oninput="textAreaAdjust(this)"' . (isset($_SESSION['user_id']) ? '' : ' disabled') . '>' . ($cv_data['description'] ?? '') . '</textarea>' ?>
                                    <label for="cvDescription" class="form-label">Description</label>
                                </div>
                                <div class="d-flex">
                                    <?php echo '<button type="submit" class="btn btn-primary btn-custom ms-auto"' . (isset($_SESSION['user_id']) ? '' : ' disabled') . '>Enregistrer</button>' ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Compétences</h2>
                            <ul id="skillsList" class="list-group list-group-horizontal-sm d-flex flex-wrap mb-4">
                                <?php
                                if (isset($cv_data['skills'])) {
                                    foreach (json_decode($cv_data['skills'], true) as $skill_i => $skill)
                                        echo '<li class="list-group-item d-flex skill-item">'
                                            . $skill .
                                            '<form method="post" class="d-flex align-items-center">
                                                    <button type="submit" name="delSkillIndex" value="' . $skill_i . '"  class="btn btn-sm btn-close pe-0 ms-2"></button>
                                                </form>
                                            </li>';
                                } else echo '<li class="list-group-item">Pas de compétence enregistrée</li>';
                                ?>
                            </ul>
                            <h5 class="card-subtitle mb-2">Nouvelle compétence</h5>
                            <?php
                            echo '<form method="post" id="addSkillForm">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="skillName" name="newSkill" placeholder' . (isset($_SESSION['user_id']) ? '' : ' disabled') . ' required>
                                    <label for="skillName" class="form-label">Nom de la nouvelle compétence</label>
                                </div>
                                <button type="submit" class="btn btn-success"' . (isset($_SESSION['user_id']) ? '' : ' disabled') . '>Ajouter</button>
                            </form>'
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Expériences</h2>
                    <div id="experiencesList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-3">
                        <?php
                        if (isset($cv_data['experiences'])) {
                            foreach (json_decode($cv_data['experiences'], true) as $experience_i => $experience)
                                CV::displayExperienceCard_cvEdit(
                                    $experience['role'],
                                    $experience['company'],
                                    $experience['start_date'],
                                    $experience['end_date'],
                                    $experience_i,
                                    true
                                );
                        } else CV::displayExperienceCard_cvEdit(
                            'Pas d\'expérience enregistrée',
                            isset($_SESSION['user_id']) ? 'Ajoutez vos experiances professionnelles ici !' : 'Connectez vous pour afficher vos experiences professionnelles',
                            0,
                            0,
                            -1,
                            false
                        );
                        ?>
                    </div>
                    <?php
                    echo '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExperienceModal"' . (isset($_SESSION['user_id']) ? '' : ' disabled') . '>
                            Ajouter une expérience
                        </button>'
                    ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Diplômes et Certifications</h2>
                    <div id="educationList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-3">
                        <?php
                        if (isset($cv_data['certificates'])) {
                            foreach (json_decode($cv_data['certificates'], true) as $certificate_i => $certificate)
                                CV::displayCertificatesCard_cvEdit(
                                    $certificate['degree'],
                                    $certificate['school'],
                                    $certificate['date'],
                                    $certificate_i,
                                    true
                                );
                        } else CV::displayCertificatesCard_cvEdit(
                            'Pas de diplome enregistrée',
                            isset($_SESSION['user_id']) ? 'Ajoutez vos diplomes et certifications ici !' : 'Connectez vous pour afficher vos diplomes et certifications',
                            0,
                            -1,
                            false
                        );
                        ?>
                    </div>
                    <?php
                    echo '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEducationModal"' . (isset($_SESSION['user_id']) ? '' : ' disabled') . '>
                            Ajouter un diplôme
                        </button>'
                    ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark py-4">
        <div class="container text-center">
            <p>&copy; 2024 Mon CV/Portfolio</p>
            <div class="mt-3">
                <a href="https://www.instagram.com/erwnn_20/" target="_blank" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://github.com/erwnn20" target="_blank" class="text-light me-3"><i class="fab fa-github"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" target="_blank" class="text-light"><i class="fab bi-download"></i></a>
                <?php if ($userInfo['admin']) echo '<a href="admin.php" class="text-light ms-3"><i class="fab bi-gear-fill"></i></a>'; ?>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="addExperienceModal" tabindex="-1" aria-labelledby="addExperienceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="addExperienceModalLabel">Ajouter une expérience</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="addExperienceForm">
                        <div class="mb-3">
                            <label for="experienceTitle" class="form-label">
                                <h5 class="mb-0">Titre</h5>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="experienceTitle" name="experienceTitle" required>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control form-control-sm" id="experienceCompany" name="experienceCompany" required>
                            <label for="experienceCompany" class="form-label">Entreprise</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="month" class="form-control" id="experienceStartDate" name="experienceStartDate" oninput="setMinExpDate()" required>
                            <label for="experienceStartDate" class="form-label">Date de début</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="month" class="form-control" id="experienceEndDate" name="experienceEndDate">
                            <label for="experienceEndDate" class="form-label">Date de fin<small
                                    class="text-muted ms-2">laisser vide si encore en cours</small></label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link btn-sm link-secondary" style="font-size: .8rem;"
                                onclick="resetForm('addExperienceForm')">Réinitialiser</button>
                            <button type="submit" class="btn btn-primary btn-custom">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="addEducationModalLabel">Ajouter un diplôme</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="addEducationForm">
                        <div class="mb-3">
                            <label for="educationTitle" class="form-label">
                                <h5 class="mb-0">Diplôme</h5>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="educationTitle" name="educationTitle" required>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="educationSchool" name="educationSchool" required>
                            <label for="educationSchool" class="form-label">École</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" min="1900" max="2100" step="1" class="form-control" id="educationDate" name="educationDate" value="2016" required>
                            <label for="educationDate" class="form-label">Année d'obtention</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link btn-sm link-secondary" style="font-size: .8rem;"
                                onclick="resetForm('addEducationForm')">Réinitialiser</button>
                            <button type="submit" class="btn btn-primary btn-custom">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/textAreaAdjust.js"></script>
    <script>
        function resetForm(formId) {
            document.forms[formId].reset();
        }

        function setMinExpDate() {
            const startDate = document.getElementById('experienceStartDate').value;
            const endDateElement = document.getElementById('experienceEndDate');
            endDateElement.setAttribute('min', startDate);

            if (endDateElement.value < startDate) {
                endDateElement.value = '';
            }
        }
    </script>
</body>

</html>

<?php
ob_end_flush();

<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/user.php';
require_once 'util/cv.php';
global $pdo;

function appendData(string $dbDataID, array|string $newData): void
{
    global $pdo;
    $data = json_decode(CV::getData($_SESSION['user_id'])[$dbDataID] ?? '[]', true);
    $data[] = $newData;

    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, '.$dbDataID.') VALUES (:id, :creator_id, :'.$dbDataID.') ON DUPLICATE KEY UPDATE '.$dbDataID.' = VALUES('.$dbDataID.')');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user_id'],
        $dbDataID => json_encode($data)
    ));
}

function deleteData(string $dbDataID, int $delIndex): void
{
    global $pdo;
    $data = array();
    foreach (json_decode(CV::getData($_SESSION['user_id'])[$dbDataID] ?? '[]', true) as $i => $element) {
        if ($i != $delIndex)
            $data[] = $element;
    }

    $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, '.$dbDataID.') VALUES (:id, :creator_id, :'.$dbDataID.') ON DUPLICATE KEY UPDATE '.$dbDataID.' = VALUES('.$dbDataID.')');
    $stmt->execute(array(
        'id' => uuid_v4(),
        'creator_id' => $_SESSION['user_id'],
        $dbDataID => json_encode($data)
    ));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // global infos
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
    if (isset($_POST['cvEmail'])) {
        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, email) VALUES (:id, :creator_id, :email) ON DUPLICATE KEY UPDATE email = VALUES(email)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'email' => $_POST['cvEmail']
        ));
    }
    if (isset($_POST['cvPhone'])) {
        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, phone_number) VALUES (:id, :creator_id, :phone_number) ON DUPLICATE KEY UPDATE phone_number = VALUES(phone_number)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'phone_number' => $_POST['cvPhone']
        ));
    }
    if (isset($_POST['cvAddress'])) {
        $stmt = $pdo->prepare('INSERT INTO cv (id, creator_id, address) VALUES (:id, :creator_id, :address) ON DUPLICATE KEY UPDATE address = VALUES(address)');
        $stmt->execute(array(
            'id' => uuid_v4(),
            'creator_id' => $_SESSION['user_id'],
            'address' => $_POST['cvAddress']
        ));
    }

    if (User::saveImg('img/cv/', 'cvProfileImage', CV::getData($_SESSION['user_id'] ?? 0)['id'])) {
        $stmt = $pdo->prepare('UPDATE cv SET image = TRUE WHERE id = :id;');
        $stmt->execute(array(
            'id' => CV::getData($_SESSION['user_id'] ?? 0)['id']
        ));
    }
    if (isset($_POST['delCvProfileImage'])) {
        $stmt = $pdo->prepare('UPDATE cv SET image = FALSE WHERE id = :id;');
        $stmt->execute(array(
            'id' => $_POST['delCvProfileImage']
        ));
        User::deleteImg('img/cv/', $_POST['delCvProfileImage']);
    }

    // skills, languages, interests infos
    if (isset($_POST['newSkill'])) {
        appendData('skills', array(
            'skill' => $_POST['newSkill'],
            'year_exp' => $_POST['skillExp'],
        ));
    }
    if (isset($_POST['delSkillIndex'])) {
        deleteData('skills', $_POST['delSkillIndex']);
    }

    if (isset($_POST['languageName'])) {
        appendData('languages', array(
            'lang' => $_POST['languageName'],
            'level' => $_POST['languageLevel'],
        ));
    }
    if (isset($_POST['delLangIndex'])) {
        deleteData('languages', $_POST['delLangIndex']);
    }

    if (isset($_POST['interestName'])) {
        appendData('interests', $_POST['interestName']);
    }
    if (isset($_POST['delInterestIndex'])) {
        deleteData('interests', $_POST['delInterestIndex']);
    }

    // experience, degree
    if (isset($_POST['experienceTitle'])) {
        appendData('experiences', array(
            'role' => $_POST['experienceTitle'],
            'company' => $_POST['experienceCompany'],
            'start_date' => $_POST['experienceStartDate'],
            'end_date' => $_POST['experienceEndDate'] ?? ''
        ));
    }
    if (isset($_POST['delExpIndex'])) {
        deleteData('experiences', $_POST['delExpIndex']);
    }

    if (isset($_POST['certificateTitle'])) {
        appendData('certificates', array(
            'degree' => $_POST['certificateTitle'],
            'school' => $_POST['certificateSchool'],
            'date' => $_POST['certificateYear'],
        ));
    }
    if (isset($_POST['delCertificateIndex'])) {
        deleteData('certificates', $_POST['delCertificateIndex']);
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
$inputDisable = isset($_SESSION['user_id']) ? '' : 'disabled';
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
                            <form method="post" id="cvInfoForm" class="flex-grow-1 d-flex flex-column mb-0" enctype="multipart/form-data">
                                <div class="mb-3 text-center">
                                    <img src="<?php echo 'img/' . (isset($cv_data['image']) ? 'cv/'.$cv_data['id'].'.png' : 'profile/default.png') ?>"
                                         alt="Photo de profil" class="profile-image-preview mb-2" id="profileImagePreview">
                                    <div class="input-group mb-3">
                                        <input type="file" class="form-control" id="profileImage" name="cvProfileImage" accept="image/*" <?php echo $inputDisable?>>
                                        <button type="submit" name="delCvProfileImage" value="<?php echo $cv_data['id'] ?? '' ?>"
                                                class="btn btn-sm btn-outline-danger" <?php echo $inputDisable?>>Supprimer</button>
                                    </div>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control form-control-lg" id="cvTitle" name="cvTitle"
                                           value="<?php echo $cv_data['title'] ?? '' ?>" placeholder <?php echo $inputDisable?>>
                                    <label for="cvTitle" class="form-label">Titre du CV</label>
                                </div>
                                <div class="form-floating flex-grow-1 d-flex flex-column mb-3">
                                    <textarea class="form-control form-control-sm flex-grow-1" id="cvDescription"
                                              name="cvDescription" oninput="textAreaAdjust(this)" <?php echo $inputDisable?>
                                    ><?php echo ($cv_data['description'] ?? '') ?></textarea>
                                    <label for="cvDescription" class="form-label">Description</label>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="email" class="input-group-text">Adresse e-mail</label>
                                    <input type="email" class="form-control" id="email" name="cvEmail" placeholder="exemple@mail.com"
                                           value ="<?php echo $cv_data['email'] ?? $userInfo['email'] ?? '' ?>" <?php echo $inputDisable?>>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="phone" class="input-group-text">Numéro de téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="cvPhone" placeholder="+33 600000000"
                                           value ="<?php echo $cv_data['phone_number'] ?? '' ?>" <?php echo $inputDisable?>>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="address" class="input-group-text">Adresse</label>
                                    <input type="text" class="form-control" id="address" name="cvAddress" placeholder="103 Av. de Castres, 31500 Toulouse"
                                           value="<?php echo $cv_data['address'] ?? '' ?>" <?php echo $inputDisable?>>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary btn-custom ms-auto" <?php echo $inputDisable?>>Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Compétences</h2>
                            <ul id="skillsList" class="list-group mb-3">
                                <?php
                                $skills = json_decode($cv_data['skills'] ?? '[]', true);
                                if ($skills) {
                                    foreach ($skills as $skill_i => $skill)
                                        CV::displaySkill_cvEdit(
                                            $skill['skill'],
                                            $skill['year_exp'],
                                            $skill_i
                                        );
                                } else echo '<li class="list-group-item">Pas de compétence enregistrée</li>';
                                ?>
                            </ul>

                            <form method="post" id="addSkillForm">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="newSkill" placeholder="Nouvelle compétence" required aria-label="Nouvelle compétence" <?php echo $inputDisable?>>
                                    <input type="number" class="form-control form-control-sm" id="skill-experience" name="skillExp" placeholder="Année(s) d\'experience(s)" <?php echo $inputDisable?> required>
                                    <label class="input-group-text" for="skill-experience">an(s)</label>
                                    <button type="submit" class="btn btn-success" <?php echo $inputDisable?>>Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Langues</h2>
                            <ul id="languagesList" class="list-group mb-3">
                                <?php
                                $langLvl = array(
                                    'native' => 'Langue maternelle',
                                    'A1' => 'A1 (Débutant)',
                                    'A2' => 'A2 (Élémentaire)',
                                    'B1' => 'B1 (Intermédiaire)',
                                    'B2' => 'B2 (Avancé)',
                                    'C1' => 'C1 (Autonome)',
                                    'C2' => 'C2 (Maîtrise)'
                                );
                                $languages = json_decode($cv_data['languages'] ?? '[]', true);
                                if ($languages) {
                                    foreach ($languages as $lang_i => $lang)
                                        CV::displayLang_cvEdit(
                                            $lang['lang'],
                                            $lang['level'],
                                            $langLvl,
                                            $lang_i
                                        );
                                } else echo '<li class="list-group-item">Pas de langue enregistrée</li>';
                                ?>
                            </ul>
                            <form method="post" id="addLanguageForm">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="languageName" name="languageName" placeholder="Langue" aria-label="Nouvelle Langue" <?php echo $inputDisable?> required>
                                    <select class="form-select" id="languageLevel" name="languageLevel" aria-label="Nouvelle Langue" <?php echo $inputDisable?> required>
                                        <option value="">Niveau</option>
                                        <?php
                                        foreach ($langLvl as $value => $opt)
                                            echo '<option value="' . $value . '">' . $opt . '</option>';
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-success" <?php echo $inputDisable?>>Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Centres d'intérêt</h2>
                            <ul id="interestsList" class="list-group mb-3">
                                <?php
                                $interests = json_decode($cv_data['interests'] ?? '[]', true);
                                if ($interests) {
                                    foreach ($interests as $interest_i => $interest)
                                        CV::displayInterest_cvEdit($interest, $interest_i);
                                } else echo '<li class="list-group-item">Pas de langue enregistrée</li>';
                                ?>
                            </ul>
                            <form method="post" id="addInterestForm">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="interestName" name="interestName" placeholder="Centre d'intérêt" aria-label="Centre d'intérêt" <?php echo $inputDisable?> required>
                                    <button type="submit" class="btn btn-success" <?php echo $inputDisable?>>Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Expériences</h2>
                    <div id="experiencesList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-3">
                        <?php
                        $experiences = json_decode($cv_data['experiences'] ?? '[]', true);
                        if ($experiences) {
                            foreach ($experiences as $experience_i => $experience)
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
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExperienceModal" <?php echo $inputDisable ?>>
                        Ajouter une expérience
                    </button>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Diplômes et Certifications</h2>
                    <div id="educationList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-3">
                        <?php
                        $certificates = json_decode($cv_data['certificates'] ?? '[]', true);
                        if ($certificates) {
                            foreach ($certificates as $certificate_i => $certificate)
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
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEducationModal" <?php echo $inputDisable ?>>
                        Ajouter un diplôme
                    </button>
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
                <?php if (isset($userInfo['admin'])) echo '<a href="admin.php" class="text-light ms-3"><i class="fab bi-gear-fill"></i></a>'; ?>
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
                            <label for="certificateTitle" class="form-label">
                                <h5 class="mb-0">Diplôme</h5>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="certificateTitle" name="certificateTitle" required>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="certificateSchool" name="certificateSchool" required>
                            <label for="certificateSchool" class="form-label">École</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" min="1900" max="2100" step="1" class="form-control" id="certificateYear" name="certificateYear" value="2016" required>
                            <label for="certificateYear" class="form-label">Année d'obtention</label>
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

        document.getElementById('profileImage').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImagePreview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>

<?php
ob_end_flush();

<?php

require_once 'app/helpers/elements/pageElements/CvElement.php';
require_once 'app/helpers/elements/pageElements/PageElement.php';
require_once 'app/models/cv-data.php';

ob_start();
session_start();

//

$cvData = CV::getData($_SESSION['user']['id'] ?? 0);
$cvStyle = CV::getStyle(userID: $_SESSION['user']['id'] ?? 0);
$inputDisable = isset($_SESSION['user']['id']) ? '' : 'disabled';
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
    <link rel="stylesheet" href="/public/styles/style.css">
    <link rel="stylesheet" href="/public/styles/cv.css">

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
                    <?php echo PageElement::headerUser($_SESSION['user']['data'], $_SESSION['user']['id'] ?? 0) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container my-5">
            <div class="text-center mb-5">
                <h1>Modifier mon CV</h1>
                <?php if (!isset($_SESSION['user']['id'])) echo '<p class="lead">Connectez vous pour modifier votre CV</p>' ?>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h2 class="card-title">Informations générales</h2>
                            <form method="post" id="cvInfoForm" class="flex-grow-1 d-flex flex-column mb-0" enctype="multipart/form-data">
                                <div class="mb-3 text-center">
                                    <div class="row mx-auto mb-2">
                                        <img src="/public/img/<?php echo isset($cvData['image']) && $cvData['image'] ? 'cv/' . $cvData['id'] . '.png' : 'profile/default.png' ?>"
                                             alt="Photo de profil" class="profile-image-preview col-4"
                                             id="profileImagePreview">
                                        <div class="col">
                                            <div class="input-group mb-2">
                                                <label for="background" class="input-group-text" style="font-size: small">Couleur du Fond</label>
                                                <input type="color" class="form-control form-control-color" id="background" name="style_background"
                                                       value="<?php echo $cvStyle['background'] ?? '#e0e0e0' ?>" <?php echo $inputDisable?>>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-custom"
                                                        onclick="document.getElementById('background').value = '#e0e0e0'">
                                                    <i class="fas bi-arrow-counterclockwise"></i>
                                                </button>
                                            </div>
                                            <div class="input-group mb-2">
                                                <label for="text-color" class="input-group-text" style="font-size: small">Couleur de la Police</label>
                                                <input type="color" class="form-control form-control-color" id="text-color" name="style_text"
                                                       value="<?php echo $cvStyle['text_color'] ?? '#1e1e1e' ?>" <?php echo $inputDisable?>>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-custom"
                                                onclick="document.getElementById('text-color').value = '#1e1e1e'">
                                                    <i class="fas bi-arrow-counterclockwise"></i>
                                                </button>
                                            </div>
                                            <div class="input-group mb-2">
                                                <label for="background-2" class="input-group-text" style="font-size: small">Couleur du Fond 2</label>
                                                <input type="color" class="form-control form-control-color" id="background-2" name="style_background_2"
                                                       value="<?php echo $cvStyle['background_2'] ?? '#5c3ba4' ?>" <?php echo $inputDisable?>>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-custom"
                                                        onclick="document.getElementById('background-2').value = '#5c3ba4'">
                                                    <i class="fas bi-arrow-counterclockwise"></i>
                                                </button>
                                            </div>
                                            <div class="input-group">
                                                <label for="text-color-2" class="input-group-text" style="font-size: small">Couleur de la Police 2</label>
                                                <input type="color" class="form-control form-control-color" id="text-color-2" name="style_text_2"
                                                       value="<?php echo $cvStyle['text_color_2'] ?? '#e0e0e0' ?>" <?php echo $inputDisable?>>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-custom"
                                                        onclick="document.getElementById('text-color-2').value = '#e0e0e0'">
                                                    <i class="fas bi-arrow-counterclockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="file" class="form-control" id="profileImage" name="cvProfileImage" accept="image/*" <?php echo $inputDisable?>>
                                        <button type="submit" name="delCvProfileImage" value="<?php echo $cvData['id'] ?? '' ?>"
                                                class="btn btn-sm btn-outline-danger" <?php echo $inputDisable?>>Supprimer</button>
                                    </div>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control form-control-lg" id="cvTitle" name="cvTitle"
                                           value="<?php echo htmlspecialchars($cvData['title'] ?? '') ?>" placeholder <?php echo $inputDisable?>>
                                    <label for="cvTitle" class="form-label">Poste visé</label>
                                </div>
                                <div class="form-floating flex-grow-1 d-flex flex-column mb-3">
                                    <textarea class="form-control form-control-sm flex-grow-1" id="cvDescription"
                                              name="cvDescription" oninput="textAreaAdjust(this)" <?php echo $inputDisable?>
                                    ><?php echo htmlspecialchars($cvData['description'] ?? '') ?></textarea>
                                    <label for="cvDescription" class="form-label">À propos de vous</label>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="email" class="input-group-text">Adresse e-mail</label>
                                    <input type="email" class="form-control" id="email" name="cvEmail" placeholder="exemple@mail.com"
                                           value ="<?php echo $cvData['email'] ?? $_SESSION['user']['data']['email'] ?? '' ?>" <?php echo $inputDisable?>>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="phone" class="input-group-text">Numéro de téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="cvPhone" placeholder="+33 6 00 00 00 00"
                                           value ="<?php echo $cvData['phone_number'] ?? '' ?>" <?php echo $inputDisable?>>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="address" class="input-group-text">Adresse</label>
                                    <input type="text" class="form-control" id="address" name="cvAddress" placeholder="103 Av. de Castres, 31500 Toulouse"
                                           value="<?php echo htmlspecialchars($cvData['address'] ?? '') ?>" <?php echo $inputDisable?>>
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
                                $skills = json_decode($cvData['skills'] ?? '[]', true);
                                if ($skills) {
                                    foreach ($skills as $skill_i => $skill)
                                        CvElement::displaySkill(
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
                                    'A1' => 'Débutant (A1)',
                                    'A2' => 'Élémentaire (A2)',
                                    'B1' => 'Intermédiaire (B1)',
                                    'B2' => 'Avancé (B2)',
                                    'C1' => 'Courant (C1)',
                                    'C2' => 'Maîtrise (C2)'
                                );
                                $languages = json_decode($cvData['languages'] ?? '[]', true);
                                if ($languages) {
                                    foreach ($languages as $lang_i => $lang)
                                        CvElement::displayLang(
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
                                $interests = json_decode($cvData['interests'] ?? '[]', true);
                                if ($interests) {
                                    foreach ($interests as $interest_i => $interest)
                                        CvElement::displayInterest($interest, $interest_i);
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
                        $experiences = json_decode($cvData['experiences'] ?? '[]', true);
                        if ($experiences) {
                            foreach ($experiences as $experience_i => $experience)
                                CvElement::displayExperienceCard(
                                    $experience['role'],
                                    $experience['company'],
                                    $experience['tasks'] ?? array(),
                                    $experience['start_date'],
                                    $experience['end_date'],
                                    $experience_i,
                                    true
                                );
                        } else CvElement::displayExperienceCard(
                            'Pas d\'expérience enregistrée',
                            isset($_SESSION['user']['id']) ? 'Ajoutez vos experiences professionnelles ici !' : 'Connectez vous pour afficher vos experiences professionnelles',
                            array(),
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
                        $certificates = json_decode($cvData['certificates'] ?? '[]', true);
                        if ($certificates) {
                            foreach ($certificates as $certificate_i => $certificate)
                                CvElement::displayCertificatesCard(
                                    $certificate['degree'],
                                    $certificate['school'],
                                    $certificate['date'],
                                    $certificate_i,
                                    true
                                );
                        } else CvElement::displayCertificatesCard(
                            'Pas de diplôme enregistrée',
                            isset($_SESSION['user']['id']) ? 'Ajoutez vos diplômes et certifications ici !' : 'Connectez vous pour afficher vos diplômes et certifications',
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

    <?php echo PageElement::footer($_SESSION['user']['data']) ?>

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
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control-sm" id="experienceCompany" name="experienceCompany" required>
                            <label for="experienceCompany" class="form-label">Entreprise</label>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <p class="mb-0">Taches<small class="text-muted ms-2">laisser vide si encore en cours</small></p>
                                <button type="button" class="btn btn-sm btn-close btn-plus" onclick="addTask()"></button>
                            </div>
                            <ul id="taskList" class="list-group mb-3">
                                <li class="task d-flex justify-content-between align-items-center">
                                    <input type="text" class="form-control form-control-sm" name="task[]" placeholder="Nouvelle tâche" aria-label="Nouvelle tâche" required>
                                </li>
                            </ul>
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
    <script src="/public/scripts/textAreaAdjust.js"></script>
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

        function addTask() {
            const newTask = document.createElement("li");
            newTask.className = "task d-flex justify-content-between align-items-center";

            const input = document.createElement("input");
            input.type = "text";
            input.className = "form-control form-control-sm";
            input.name = "task[]";
            input.placeholder = "Nouvelle tâche";
            input.ariaLabel = "Nouvelle tâche";
            input.required = true;

            const removeButton = document.createElement("button");
            removeButton.type = "button";
            removeButton.className = "btn btn-close ms-2";
            removeButton.onclick = function() {
                newTask.remove();
            };

            newTask.appendChild(input);
            newTask.appendChild(removeButton);

            document.getElementById("taskList").appendChild(newTask);
        }

        document.getElementById('profileImage').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImagePreview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
</body>

</html>

<?php
ob_end_flush();

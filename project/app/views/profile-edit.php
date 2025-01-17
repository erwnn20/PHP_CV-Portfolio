<?php

require_once 'app/helpers/elements/pageElements/ProfileElement.php';
require_once 'app/helpers/elements/pageElements/PageElement.php';
require_once 'app/models/cv-data.php';
require_once 'app/models/projects-data.php';

ob_start();
session_start();

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['loginError'] = array('external' => true);

    header("Location: /");
    exit;
}

//

$cvData = CV::getData($_SESSION['user']['id']);
$projectsData = Projects::getData($_SESSION['user']['id']);
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mon Profil - Mon CV/Portfolio</title>

        <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/public/styles/style.css">
        <link rel="stylesheet" href="/public/styles/profile.css">

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
                    <?php echo PageElement::headerUser($_SESSION['user']['data'], $_SESSION['user']['id']) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <h1 class="mb-4">Mon Profil</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="profile-image-container position-relative mb-3">
                            <img src="/public/img/profile/<?php echo $_SESSION['user']['data']['profile_picture'] ? 'user/'.$_SESSION['user']['id'].'.png' : 'default.png' ?>" alt="Photo de profil" class="profile-image" id="profileImage">'.
                            <?php
                            if ($_SESSION['user']['data']['profile_picture'])
                                echo '<form method="post" class="reset-profile-image-container">
                                        <button type="submit" class="btn btn-sm btn-dark" name="deletePicture">Supprimer</button>
                                    </form>'
                            ?>
                        </div>
                        <h2 class="card-title" id="userFullName">
                            <?php echo htmlspecialchars($_SESSION['user']['data']['first_name']) . ' ' . htmlspecialchars($_SESSION['user']['data']['last_name']) ?>
                        </h2>
                        <p class="card-text" id="userEmail"><?php echo $_SESSION['user']['data']['email'] ?></p>
                        <?php if ($_SESSION['user']['data']['admin']) echo '<a href="/admin" class="btn btn-sm btn-dark w-100">Admin Panel</a>' ?>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Modifier mes informations</h4>
                        <form method="post" id="profileForm" enctype="multipart/form-data">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder
                                       value="<?php echo htmlspecialchars($_SESSION['user']['data']['first_name']) ?>" required>
                                <label for="firstName" class="form-label">Prénom</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="lastName" name="lastName" placeholder
                                       value="<?php echo htmlspecialchars($_SESSION['user']['data']['last_name'])?>" required>
                                <label for="lastName" class="form-label">Nom</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="email" class="form-control" id="email" name="email" placeholder value="<?php echo $_SESSION['user']['data']['email']?>" required>
                                <label for="email" class="form-label">Email</label>
                            </div>
                            <div class="mb-2">
                                <p class="card-text mb-2">Nouveau mot de passe<small class="text-muted ms-2">laisser vide si inchangé</small></p>
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="newPassword" name="newPassword"
                                               placeholder>
                                        <label for="newPassword" class="form-label">Mot de passe</label>
                                    </div>
                                    <span class="input-group-text password-toggle"
                                          onclick="togglePassword('newPassword', 'newPasswordToggle')">
                                        <i id="newPasswordToggle" class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="confirmPassword" placeholder>
                                        <label for="confirmPassword" class="form-label">Confirmer le mot de
                                            passe</label>
                                    </div>
                                    <span class="input-group-text password-toggle"
                                          onclick="togglePassword('confirmPassword', 'confirmPasswordToggle')">
                                        <i id="confirmPasswordToggle" class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <span id="profilePasswordError" class="text-danger fst-italic ms-1 d-none"
                                      style="font-size: .9rem;" role="alert">
                                    <i class="bi-exclamation-circle"></i>
                                    Les mots de passe ne correspondent pas.
                                </span>
                            </div>
                            <div class="mb-3">
                                <label for="profilePicture" class="form-label">Photo de profil<small class="text-muted ms-2">laisser vide si inchangé</small></label>
                                <div id="image-preview-container" class="mb-2"></div>
                                <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary btn-custom">Enregistrer les
                                modifications</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <h3 class="card-title m-0">Mes Projets</h3>
                            <a href="/projects/edit" class="btn btn-sm btn-primary btn-custom ms-auto">Modifier mes
                                projets</a>
                        </div>
                        <div id="projectsList">
                            <?php
                            if (!empty($projectsData)) {
                                foreach ($projectsData as $project_i => $project)
                                    ProfileElement::displayProjectCard(
                                        $project['id'],
                                        $project['title'],
                                        $project['description'],
                                        $project['theme'],
                                        $project['link'],
                                        json_decode($project['images'] ?? '[]'),
                                        (bool)$project['ban_id'],
                                        $project_i,
                                    );
                            } else ProfileElement::displayProjectCard(
                                -1,
                                'Pas de projet enregistré',
                                'Ajoutez vos projets personnels et professionnels dans la section "Gérer mes Projets"',
                                '',
                                '',
                                array(),
                                false,
                                -1,
                            );
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <h3 class="card-title m-0">Mes Expériences</h3>
                            <a href="/cv/edit" class="btn btn-sm btn-primary btn-custom ms-auto">Modifier mon CV</a>
                        </div>
                        <div id="experiencesList">
                            <?php
                            $experiences = json_decode($cvData['experiences'] ?? '[]', true);
                            if ($experiences) {
                                foreach ($experiences as $experience_i => $experience)
                                    ProfileElement::displayExperienceCard(
                                        $experience['role'],
                                        $experience['company'],
                                        $experience['start_date'],
                                        $experience['end_date'],
                                    );
                            } else ProfileElement::displayExperienceCard(
                                'Pas d\'expérience enregistrée',
                                'Ajoutez vos experiences professionnelles dans la section "Modifier mon CV"',
                                0,
                                0,
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php echo PageElement::footer($_SESSION['user']['data']) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/scripts/togglePassword.js"></script>
    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorDiv = document.getElementById('profilePasswordError');

            if (password !== confirmPassword) {
                errorDiv.classList.remove('d-none');
            } else {
                errorDiv.classList.add('d-none');
                this.submit();
            }
        });

        document.getElementById('profilePicture').addEventListener('change', function(event) {
            const imagePreview = document.getElementById('image-preview-container');
            imagePreview.innerHTML = '';
            Array.from(event.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.className = 'image-preview';
                    imagePreview.appendChild(imgElement);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>

</html>

<?php
ob_end_flush();

<?php
global $pdo;
ob_start();
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['connection'])) {
        $_SESSION['loginError'] = array('external' => true);

        header("Location: /");
        exit;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

//

$cv_data = getCVData($_SESSION['user_id'] ?? 0);
$projects_data = getProjectsData($_SESSION['user_id'] ?? 0);
$userInfo = getUserInfo($_SESSION['user_id'] ?? 0);
?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gérer mes projets - Mon CV/Portfolio</title>

        <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles/style.css">
        <link rel="stylesheet" href="styles/profile.css">

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

    <main class="container my-5">
        <h1 class="mb-4">Mon Profil</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="" alt="Photo de profil" class="profile-image mb-3" id="profileImage">
                        <h2 class="card-title" id="userFullName">Prénom Nom</h2>
                        <p class="card-text" id="userEmail">email@exemple.com</p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Modifier mes informations</h4>
                        <form id="profileForm">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="firstName" placeholder required>
                                <label for="firstName" class="form-label">Prénom</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="lastName" placeholder required>
                                <label for="lastName" class="form-label">Nom</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="email" class="form-control" id="email" placeholder required>
                                <label for="email" class="form-label">Email</label>
                            </div>
                            <div class="mb-2">
                                <p class="card-text mb-2">Nouveau mot de passe<small class="text-muted ms-2">laisser
                                        vide si inchangé</small></p>
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
                                <label for="profilePicture" class="form-label">Photo de profil</label>
                                <input type="file" class="form-control" id="profilePicture" accept="image/*">
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
                            <h3 class="card-title m-0">Expériences</h3>
                            <a href="cv-edit.php" class="btn btn-sm btn-primary btn-custom ms-auto">Modifier mon CV</a>
                        </div>
                        <div id="experiencesList">
                            <!-- Les expériences seront ajoutées ici dynamiquement -->
                            <div class="experience-item">
                                <h4>Développeur Web</h4>
                                <p class="mb-2">Entreprise A</p>
                                <p class="mb-0"><small>2020 - Présent</small></p>
                            </div>
                            <!--  -->
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <h3 class="card-title m-0">Projets</h3>
                            <a href="projects-edit.php" class="btn btn-sm btn-primary btn-custom ms-auto">Modifier mes
                                projets</a>
                        </div>
                        <div id="projectsList">
                            <!-- Les projets seront ajoutés ici dynamiquement -->
                            <div class="project-item row">
                                <div class="col-md-8 mb-2">
                                    <div class="d-flex flex-wrap align-items-center mb-2">
                                        <h4 class="card-title text-break m-0">Application mobile</h4>
                                        <span class="badge rounded-pill text-bg-primary ms-2">Theme</span>
                                    </div>
                                    <p class="mb-3">Développement d'une application mobile avec React Native</p>
                                    <a href="#" class="btn btn-sm btn-primary btn-custom mt-auto" target="_blank">
                                        Voir le projet
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <img src="img/projects/no_img.png" class="project-img rounded" alt="...">
                                </div>
                            </div>
                            <!--  -->
                        </div>
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/togglePassword.js"></script>
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
    </script>

    </body>

    </html>

<?php
ob_end_flush();

<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/user.php';
require_once 'util/cv.php';
require_once 'util/projects.php';
global $pdo;

if (!isset($_SESSION['user_id'])) {
    $_SESSION['loginError'] = array('external' => true);

    header("Location: /");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $newData = array(
            'id' => $_SESSION['user_id'],
            'email' => $_POST['email'],
            'first_name' => $_POST['firstName'],
            'last_name' => $_POST['lastName'],
        );
        if (isset($_POST['newPassword'])) $newData['password'] = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
        if (User::saveImg('img/profile/user/','profilePicture' , $_SESSION['user_id'])) {
            $newData['profile_picture'] = true;
        }

        $sql = 'UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name'
            .(isset($newData['password']) ? ', password = :password' : '')
            .(isset($newData['profile_picture']) ? ', profile_picture = :profile_picture' : '')
            .' WHERE id = :id;';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($newData);
    }

    if (isset($_POST['deletePicture'])) {
        $stmt = $pdo->prepare('UPDATE user SET profile_picture = FALSE WHERE id = :id;');
        $stmt->execute(array('id' => $_SESSION['user_id']));
        User::deleteImg('img/profile/user/', $_SESSION['user_id']);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

//

$cv_data = CV::getData($_SESSION['user_id']);
$projects_data = Projects::getData($_SESSION['user_id']);
$userInfo = User::getData($_SESSION['user_id']);
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
                    <li class="nav-item d-flex">
                        <div class="nav-link d-flex align-items-center">
                            <a class="nav-link fw-bold p-0" href="profile.php">
                                <?php echo $userInfo['first_name'] . ' ' . $userInfo['last_name'] ?>
                            </a>
                            <a href="logout.php" class="nav-link align-content-center p-0 ms-2"><i class="bi bi-power"></i></a>
                        </div>
                    </li>
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
                            <img src="img/profile/<?php echo $userInfo['profile_picture'] ? 'user/'.$_SESSION['user_id'].'.png' : 'default.png' ?>" alt="Photo de profil" class="profile-image" id="profileImage">'.
                            <?php
                            if ($userInfo['profile_picture'])
                                echo '<form method="post" class="reset-profile-image-container">
                                        <button type="submit" class="btn btn-sm btn-dark" name="deletePicture">Supprimer</button>
                                    </form>'
                            ?>
                        </div>
                        <h2 class="card-title" id="userFullName"><?php echo $userInfo['first_name'] . ' ' . $userInfo['last_name'] ?></h2>
                        <p class="card-text" id="userEmail"><?php echo $userInfo['email'] ?></p>
                        <?php if ($userInfo['admin']) echo '<a href="admin.php" class="btn btn-sm btn-dark w-100">Admin Panel</a>' ?>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Modifier mes informations</h4>
                        <form method="post" id="profileForm" enctype="multipart/form-data">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder value="<?php echo $userInfo['first_name'] ?>" required>
                                <label for="firstName" class="form-label">Prénom</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="lastName" name="lastName" placeholder value="<?php echo $userInfo['last_name']?>" required>
                                <label for="lastName" class="form-label">Nom</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="email" class="form-control" id="email" name="email" placeholder value="<?php echo $userInfo['email']?>" required>
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
                            <a href="projects-edit.php" class="btn btn-sm btn-primary btn-custom ms-auto">Modifier mes
                                projets</a>
                        </div>
                        <div id="projectsList">
                            <?php
                            if (!empty($projects_data)) {
                                foreach ($projects_data as $project_i => $project)
                                    Projects::displayCard_profile(
                                        $project['id'],
                                        $project['title'],
                                        $project['description'],
                                        $project['theme'],
                                        $project['link'],
                                        json_decode($project['images'] ?? '[]'),
                                        $project_i,
                                    );
                            } else Projects::displayCard_profile(
                                -1,
                                'Pas de projet enregistré',
                                'Ajoutez vos projets personnels et professionnels dans la section "Gerer mes Projets"',
                                '',
                                '',
                                array(),
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
                            <a href="cv-edit.php" class="btn btn-sm btn-primary btn-custom ms-auto">Modifier mon CV</a>
                        </div>
                        <div id="experiencesList">
                            <?php
                            if (isset($cv_data['experiences'])) {
                                foreach (json_decode($cv_data['experiences'], true) as $experience_i => $experience)
                                    CV::displayExperienceCard_profile(
                                        $experience['role'],
                                        $experience['company'],
                                        $experience['start_date'],
                                        $experience['end_date'],
                                    );
                            } else CV::displayExperienceCard_profile(
                                'Pas d\'expérience enregistrée',
                                'Ajoutez vos experiances professionnelles dans la section "Modifier mon CV"',
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

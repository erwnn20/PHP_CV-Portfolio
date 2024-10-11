<?php
global $pdo;
session_start();
require 'db.php';

$_SESSION['user_id'] = 0;

// Fetch user information from the database
function getUserInfo()
{
    global $pdo;
    if ($_SESSION['user_id']) {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return array(
        'email' => '',
        'first_name' => '',
        'last_name' => '',
        'password' => '',
        'admin' => false
    );
}

/*if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update personal information in the database
    $stmt = $pdo->prepare('SELECT id FROM user WHERE email = ? AND password = ?');
    $stmt->execute([$email, $password]);
    $result = $stmt->fetch();

    if ($result) {
        $_SESSION['user_id'] = $result['id'];
    } else {
        //        echo 'Aucun utilisateur trouvé avec ces informations.';
    }
}*/

$userInfo = getUserInfo();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon CV/Portfolio</title>

    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/index.css">

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
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Mon CV/Portfolio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cv">CV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#projets">Projets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <?php
                    if (isset($_SESSION['user_id']))
                        echo '<li class="nav-item d-flex">
                                    <a class="nav-link fw-bold ms-3" href="profile.php">' .
                            $userInfo['first_name'] . " " . $userInfo['last_name'] .
                            '</a>
                                    <a href="logout.php" class="nav-link align-content-center ps-0"><i class="bi bi-power"></i></a>
                                </li>';
                    else echo '<li class="nav-item align-content-center ms-2">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</button>
                                </li>';
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <header id="accueil" class="py-5">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Bienvenue sur mon CV/Portfolio</h1>
            <p class="lead">Découvrez mes compétences, expériences et projets</p>
            <a href="cv.php" class="btn btn-custom btn-lg mt-3">Voir les CV</a>
        </div>
    </header>

    <section id="cv" class="py-5">
        <?php
        $cv_data = [];
        if ($_SESSION['user_id']) {
            $stmt = $pdo->prepare('SELECT * FROM cv WHERE creator_id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $cv_data = $stmt->fetchAll();
        }
        ?>

        <div class="container">
            <h2 class="text-center mb-4">Mon CV</h2>
            <div class="row">
                <div class="col-md-3 text-end">
                    <h3>Compétences</h3>
                    <ul class="list-group list-group-flush">
                        <?php
                        if ($cv_data) {
                            $skills = [];
                            foreach ($cv_data as $cv)
                                if ($cv['skills'])
                                    foreach (json_decode($cv['skills'], true) as $skill)
                                        if (!in_array($skill, $skills)) $skills[] = $skill;
                            for ($skill_i = 0; $skill_i < min(6, count($skills)); $skill_i++)
                                echo '<li class="list-group-item text-bg-dark">' . $skills[$skill_i] . '</li>';
                        } else echo '<li class="list-group-item text-bg-dark">Pas de compétence enregistrée</li>';
                        ?>
                    </ul>
                </div>
                <div class="col-md-6 text-center">
                    <h3>Expériences</h3>
                    <?php
                    function displayExpCard($title, $subtitle, $start_year, $end_year, bool $margin)
                    {
                        echo '<div class="card text-light' . ($margin ? ' mb-3' : '') . '">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $title . '</h5>
                                        <h6 class="card-subtitle mb-2">' . $subtitle . '</h6>';
                        if ($start_year && $end_year)
                            echo '<p class="card-text">' . $start_year . ' - ' . $end_year . '</p>';
                        echo '    </div>
                              </div>';
                    }

                    if ($cv_data) {
                        $experiences = [];
                        foreach ($cv_data as $cv)
                            $experiences = array_merge($experiences, json_decode($cv['experiences'], true));
                        foreach ($experiences as $experience_i => $experience)
                            if ($experience_i < 3)
                                displayExpCard($experience['post'], $experience['company'], $experience['start_date'], $experience['end_date'], $experience_i < min(3, count($experiences)) - 1);
                    } else displayExpCard(
                        'Pas d\'expérience enregistrée',
                        $_SESSION['user_id'] ? 'Modifiez votre CV pour ajouter vos experiances professionnelles' : 'Connectez vous pour afficher vos experiences professionnelles',
                        0,
                        0,
                        false
                    );
                    ?>
                </div>
                <div class="col-md-3 text-start diplomes">
                    <h3>Diplomes</h3>
                    <?php
                    function displayCertifCard($title, $subtitle, $year, bool $margin)
                    {

                        echo '<div class="card text-light' . ($margin ? ' mb-3' : '') . '">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $title . '</h5>
                                        <h6 class="card-subtitle mb-2">' . $subtitle . '</h6>';
                        if ($year)
                            echo '<p class="card-text">' . $year . '</p>';
                        echo '</div>
                              </div>';
                    }

                    if ($cv_data) {
                        $certificates = [];
                        foreach ($cv_data as $cv)
                            $certificates = array_merge($certificates, json_decode($cv['certificates'], true));
                        foreach ($certificates as $certificate_i => $certificate)
                            if ($certificate_i < 3)
                                displayCertifCard($certificate['level'], $certificate['school'], $certificate['date'], $certificate_i != min(3, count($certificates)) - 1);
                    } else displayCertifCard(
                        'Pas de diplome enregistrée',
                        $_SESSION['user_id'] ? 'Modifiez votre CV pour ajouter vos diplomes et certifications' : 'Connectez vous pour afficher vos diplomes et certifications',
                        0,
                        false
                    );
                    ?>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="cv_modif.php" class="btn btn-custom me-3">Modifier mon CV</a>
                <a href="#" class="btn btn-custom">Télécharger le CV en PDF</a>
            </div>
        </div>
    </section>

    <section id="projets" class="py-5 bg-dark">
        <?php
        $project_data = [];
        if ($_SESSION['user_id']) {
            $stmt = $pdo->prepare('SELECT * FROM project WHERE creator_id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $project_data = $stmt->fetchAll();
        }
        ?>

        <div class="container">
            <h2 class="text-center mb-4">Mes Projets</h2>
            <div class="row" style="justify-content: center;">
                <?php
                function displayProjectCard($title, $theme, $link, $description, array $images, int $index)
                {
                    echo '<div class="col-md-4 mb-4">
                    <div class="card text-light">';
                    if ($images) {
                        echo '<div id="carouselProject' . $index . '" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">';
                        for ($img_i = 0; $img_i < count($images); $img_i++)
                            echo  '<button type="button" data-bs-target="#carouselProject' . $index . '" data-bs-slide-to="' . $img_i . '"' . ($img_i == 0 ? 'class="active" aria-current="true"' : '') . '></button>';
                        echo   '</div>
                            <div class="carousel-inner">';
                        foreach ($images as $i => $img)
                            echo '<div class="carousel-item' . ($i == 0 ? ' active' : '') . '">
                                    <img src="img/' . $img . '" class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" alt="projet_image">
                                </div>';
                        echo '</div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject' . $index . '" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselProject' . $index . '" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>';
                    }
                    echo '<div class="card-body">
                            <div class="d-flex flex-row align-items-center">
                                <h5 class="card-title p-2">' . $title . '</h5>' .
                        ($theme ? '<span class="badge text-light bg-primary ms-2">' . $theme . '</span>' : '') .
                        ($link ? '<a href="'.$link.'" class="btn btn-sm btn-custom ms-auto">Voir le projet</a>' : '').
                        '</div>
                            <p class="card-text mb-0">' . $description . '</p>
                            <div class="d-flex mt-2">' .
                        '</div>
                        </div>
                    </div>
                </div>';
                }

                if ($project_data) {
                    foreach ($project_data as $project_i => $project)
                        if ($project_i < 3)
                            displayProjectCard($project['title'], $project['theme'], $project['link'], $project['description'], json_decode($project['images'], true), $project_i);
                } else displayProjectCard(
                    'Pas de projet enregistrée',
                    '',
                    '',
                    $_SESSION['user_id'] ? 'Gerez et  ajoutez vos projets personnels et professionnels' : 'Connectez vous pour afficher vos projets personnels et professionnels',
                    [],
                    0
                );
                ?>
                <div class="text-center mt-4">
                    <a href="portfolio.php" class="btn btn-custom">Gérer mes Projets</a>
                </div>
            </div>
    </section>

    <section id="contact" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Contactez-moi</h2>
            <div class="row">
                <div class="col-md-6">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <?php echo '<input type="text" class="form-control" id="name" placeholder="John Doe" value="' . ($_SESSION['user_id'] ? $userInfo['first_name'] . ' ' . $userInfo['last_name'] : '') . '" required>'; ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <?php echo '<input type="email" class="form-control" id="email" placeholder="john_doe@exemple.com" value="' . ($_SESSION['user_id'] ? $userInfo['email'] : '') . '" required>'; ?>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="4" placeholder="Entrez votre message..."
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom">Envoyer</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h3>Localisation</h3>
                    <div class="ratio ratio-16x9">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5777.627296697174!2d1.4293162765289558!3d43.61042155519602!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12aebdbb42d83293%3A0x6e448c24640106bd!2sToulouse%20Ynov%20Campus%20-%20%C3%89cole%20des%20m%C3%A9tiers%20du%20digital!5e0!3m2!1sfr!2sfr!4v1728315900372!5m2!1sfr!2sfr"
                            allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Connexion / Inscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                                type="button" role="tab" aria-controls="login" aria-selected="true">Connexion</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
                                type="button" role="tab" aria-controls="register"
                                aria-selected="false">Inscription</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                            <form method="post" class="mt-3" id="loginForm">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="loginEmail" name="loginEmail" placeholder required>
                                    <label for="loginEmail" class="form-label">Email</label>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder required>
                                            <label for="loginPassword" class="form-label">Mot de passe</label>
                                        </div>
                                        <span class="input-group-text password-toggle" onclick="togglePassword('loginPassword', 'loginPasswordToggle')">
                                            <i id="loginPasswordToggle" class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <span id="loginError" class="text-danger fst-italic ms-1 d-none"
                                        style="font-size: .9rem;" role="alert">
                                        Mot de passe incorrect. Veuillez réessayer.
                                    </span>
                                </div>
                                <div class="d-flex gap-2 justify-content-end mt-4">
                                    <button type="button" class="btn btn-link btn-sm link-secondary" style="font-size: .8rem;" onclick="resetForm('loginForm')">Réinitialiser</button>
                                    <button type="submit" class="btn btn-custom ">Se connecter</button>
                                    <button class="btn btn-outline-custom">
                                        <i class="fab fa-google"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                            <form method="post" class="mt-3" id="registerForm">
                                <div class="d-flex gap-2 mb-3">
                                    <div class="form-floating flex-grow-1">
                                        <input type="text" class="form-control" id="registerLastName" name="registerLastName" placeholder required>
                                        <label for="registerLastName">Nom</label>
                                    </div>

                                    <div class="form-floating flex-grow-1">
                                        <input type="text" class="form-control" id="registerFirstName" name="registerFirstName" placeholder required>
                                        <label for="registerFirstName" class="form-label">Prenom</label>
                                    </div>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="registerEmail" name="registerEmail" placeholder required>
                                    <label for="registerEmail" class="form-label">Email</label>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="registerPassword" name="registerPassword" placeholder required>
                                            <label for="registerPassword" class="form-label">Mot de passe</label>
                                        </div>
                                        <span class="input-group-text password-toggle" onclick="togglePassword('registerPassword', 'registerPasswordToggle')">
                                            <i id="registerPasswordToggle" class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="registerConfirmPassword" placeholder required>
                                            <label for="registerConfirmPassword" class="form-label">Confirmer le mot de passe</label>
                                        </div>
                                        <span class="input-group-text password-toggle" onclick="togglePassword('registerConfirmPassword', 'registerConfirmPasswordToggle')">
                                            <i id="registerConfirmPasswordToggle" class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <span id="registerError" class="text-danger fst-italic ms-1 d-none"
                                        style="font-size: .9rem;" role="alert">
                                        Les mots de passe ne correspondent pas.
                                    </span>
                                </div>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-link link-secondary" style="font-size: .8rem;" onclick="resetForm('registerForm')">Réinitialiser</button>
                                    <button type="submit" class="btn btn-custom ms-auto">S'inscrire</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p>&copy; 2024 Mon CV/Portfolio</p>
            <div class="mt-3">
                <a href="https://www.instagram.com/erwnn_20/" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://github.com/erwnn20" class="text-light me-3"><i class="fab fa-github"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" class="text-light me-3"><i class="fab bi-download"></i></a>
                <?php if ($userInfo['admin']) echo '<a href="admin.php" class="text-light me-3"><i class="fab bi-gear-fill"></i></a>'; ?>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            if (input.type === "password") {
                input.type = "text";
                toggle.classList.remove("fa-eye");
                toggle.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                toggle.classList.remove("fa-eye-slash");
                toggle.classList.add("fa-eye");
            }
        }

        function resetForm(formId) {
            document.getElementById(formId).reset();
            document.getElementById('loginError').classList.add('d-none');
            document.getElementById('registerError').classList.add('d-none');
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');

            if (password /* !== 'motdepasse123'*/ ) {
                errorDiv.classList.add('d-none');
                this.submit();
            } else {
                errorDiv.classList.remove('d-none');
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirmPassword').value;
            const errorDiv = document.getElementById('registerError');

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
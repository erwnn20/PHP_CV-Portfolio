<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/elements.php';
require_once 'util/user.php';
require_once 'util/cv.php';
require_once 'util/projects.php';
global $pdo;

// Get infos for forms to Connect or Create and Connect user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['loginEmail'])) {
        $loginEmail = $_POST['loginEmail'];
        $stmt = $pdo->prepare('SELECT id, email, password  FROM user WHERE email = :email');
        $stmt->execute(array('email' => $loginEmail));
        $data = $stmt->fetch();

        if (isset($data['password'])) {
            if (password_verify($_POST['loginPassword'], $data['password'])) $_SESSION['user']['id'] = $data['id'];
            else $_SESSION['loginError'] = array(
                'loginEmail' => $loginEmail,
                'password' => true
            );
        } else $_SESSION['loginError'] = array('email' => true);
    }

    if (isset($_POST['registerEmail'])) {
        $uuid = uuid_v4();
        $registerValue = array(
            'email' => $_POST['registerEmail'],
            'first_name' => $_POST['registerFirstName'],
            'last_name' => $_POST['registerLastName'],
            'password' => $_POST['registerPassword']
        );
        try {
            $stmt = $pdo->prepare('INSERT INTO user (id, email, first_name, last_name, password) 
                                        VALUES (:id, :email, :first_name, :last_name, :password)');
            $stmt->execute(array(
                'id' => $uuid,
                'email' => $registerValue['email'],
                'first_name' => $registerValue['first_name'],
                'last_name' => $registerValue['last_name'],
                'password' => password_hash($registerValue['password'], PASSWORD_BCRYPT)
            ));
            $_SESSION['user']['id'] = $uuid;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['registerError'] = array('email' => true);
            }
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$_SESSION['user']['data'] = User::getData($_SESSION['user']['id'] ?? 0);
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
                        <a class="nav-link" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cv">CV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#projects">Projets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <?php echo Element::headerUser($_SESSION['user']['data'], $_SESSION['user']['id'] ?? 0) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <header id="accueil" class="py-5">
            <div class="container text-center">
                <h1 class="display-4 mb-4">Bienvenue sur mon CV/Portfolio</h1>
                <p class="lead">Découvrez mes compétences, expériences et projets</p>
                <a href="resume.php" class="btn btn-primary btn-custom btn-lg mt-3">Voir les CV</a>
            </div>
        </header>

        <section id="cv" class="py-5">
            <?php $cv_data = CV::getData($_SESSION['user']['id'] ?? 0) ?>
            <div class="container">
                <h2 class="text-center mb-4">Mon CV</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-center mb-4">Compétences</h3>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $skills = json_decode($cv_data['skills'] ?? '[]', true);
                                    if ($skills) {
                                        foreach ($skills as $skill_i => $skill)
                                            if ($skill_i < 7)
                                                CV::displaySkill_home($skill['skill'], $skill['year_exp']);
                                    } else echo '<li class="list-group-item">Pas de compétence enregistrée</li>';
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-center mb-4">Expériences</h3>
                                <div class="timeline">
                                    <?php
                                    $experiences = json_decode($cv_data['experiences'] ?? '[]', true);
                                    if ($experiences) {
                                        foreach ($experiences as $experience_i => $experience)
                                            if ($experience_i < 5)
                                                CV::displayExperienceCard_home(
                                                    $experience['role'],
                                                    $experience['company'],
                                                    $experience['start_date'],
                                                    $experience['end_date'],
                                                );
                                    } else CV::displayExperienceCard_home(
                                        'Pas d\'expérience enregistrée',
                                        isset($_SESSION['user']['id']) ? 'Modifiez votre CV pour ajouter vos experiances professionnelles' : 'Connectez vous pour afficher vos experiences professionnelles',
                                        0,
                                        0,
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-center mb-4">Diplômes</h3>
                                <div class="timeline">
                                    <?php
                                    $certificates = json_decode($cv_data['certificates'] ?? '[]', true);
                                    if ($certificates) {
                                        foreach ($certificates as $certificate_i => $certificate)
                                            if ($certificate_i < 5)
                                                CV::displayCertificatesCard_home(
                                                    $certificate['degree'],
                                                    $certificate['school'],
                                                    $certificate['date'],
                                                );
                                    } else CV::displayCertificatesCard_home(
                                        'Pas de diplome enregistrée',
                                        isset($_SESSION['user']['id']) ? 'Modifiez votre CV pour ajouter vos diplomes et certifications' : 'Connectez vous pour afficher vos diplomes et certifications',
                                        0,
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="cv-edit.php" class="btn btn-primary btn-custom me-3">Modifier mon CV</a>
                    <a href="#" class="btn btn-primary btn-custom disabled">Télécharger le CV en PDF</a>
                </div>
            </div>
        </section>

        <section id="projects" class="py-5">
            <?php  $project_data = Projects::getData(userID: $_SESSION['user']['id'] ?? 0) ?>
            <div class="container">
                <h2 class="text-center mb-4">Mes Projets</h2>
                <div class="row" style="justify-content: center;">
                    <?php
                    if ($project_data) {
                        foreach ($project_data as $project_i => $project)
                            if ($project_i < 3)
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
                    <div class="text-center mt-4">
                        <a href="projects-edit.php" class="btn btn-primary btn-custom">Gérer mes Projets</a>
                    </div>
                </div>
        </section>

        <section id="contact" class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Contactez-moi</h2>
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" placeholder="John Doe" value="<?php if (isset($_SESSION['user']['data']['first_name'], $_SESSION['user']['data']['last_name'])) echo $_SESSION['user']['data']['first_name'] . ' ' . $_SESSION['user']['data']['last_name'] ?>" required>
                                <label for="name" class="form-label">Nom</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" placeholder="john_doe@exemple.com" value="<?php echo $_SESSION['user']['data']['email'] ?? '' ?>" required>
                                <label for="email" class="form-label">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" style="height: 13rem;" id="message" rows="4" placeholder="Entrez votre message..." required></textarea>
                                <label for="message" class="form-label">Message</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-custom">Envoyer</button>
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
    </main>

    <?php echo Element::footer($_SESSION['user']['data']) ?>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content ">
                <div class="modal-body py-0">
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="loginModalLabel">Connexion / Inscription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
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
                                    <input type="email" class="form-control" id="loginEmail" name="loginEmail" oninput="document.getElementById('loginEmailError').classList.add('d-none');" placeholder required>
                                    <label for="loginEmail" class="form-label">Email</label>
                                    <span id="loginEmailError" class="text-danger fst-italic ms-1 d-none" style="font-size: .9rem;" role="alert">
                                        <i class="bi-exclamation-circle"></i>
                                        L'adresse email n'est associée à aucun compte. Veuillez réessayer.
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="loginPassword" name="loginPassword" oninput="document.getElementById('loginPasswordError').classList.add('d-none');" placeholder required>
                                            <label for="loginPassword" class="form-label">Mot de passe</label>
                                        </div>
                                        <span class="input-group-text password-toggle" onclick="togglePassword('loginPassword', 'loginPasswordToggle')">
                                            <i id="loginPasswordToggle" class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <span id="loginPasswordError" class="text-danger fst-italic ms-1 d-none" style="font-size: .9rem;" role="alert">
                                        <i class="bi-exclamation-circle"></i>
                                        Mot de passe incorrect. Veuillez réessayer.
                                    </span>
                                </div>
                                <div class="modal-footer d-flex gap-2 justify-content-end mt-4">
                                    <button type="button" class="btn btn-link btn-sm link-secondary p-0 me-auto" style="font-size: .8rem;" onclick="resetForm('loginForm')">Réinitialiser</button>
                                    <button type="submit" class="btn btn-primary btn-custom ">Se connecter</button>
                                    <button type="button" class="btn btn-outline-primary btn-custom" disabled>
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
                                    <input type="email" class="form-control" id="registerEmail" name="registerEmail" oninput="document.getElementById('registerEmailError').classList.add('d-none');" placeholder required>
                                    <label for="registerEmail" class="form-label">Email</label>
                                    <span id="registerEmailError" class="text-danger fst-italic ms-1 d-none" style="font-size: .9rem;" role="alert">
                                        <i class="bi-exclamation-circle"></i>
                                        L'adresse email est déjà associée à un autre compte. Veuillez réessayer.
                                    </span>
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
                                    <span id="registerError" class="text-danger fst-italic ms-1 d-none" style="font-size: .9rem;" role="alert">
                                        <i class="bi-exclamation-circle"></i>
                                        Les mots de passe ne correspondent pas.
                                    </span>
                                </div>
                                <div class="modal-footer d-flex">
                                    <button type="button" class="btn btn-link link-secondary p-0" style="font-size: .8rem;" onclick="resetForm('registerForm')">Réinitialiser</button>
                                    <button type="submit" class="btn btn-primary btn-custom ms-auto">S'inscrire</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/togglePassword.js"></script>
    <script>
        function resetForm(formId) {
            document.forms[formId].reset();
            document.getElementById('loginEmailError').classList.add('d-none');
            document.getElementById('loginPasswordError').classList.add('d-none');
            document.getElementById('registerEmailError').classList.add('d-none');
            document.getElementById('registerError').classList.add('d-none');
        }

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
    <?php
    // Display login modal on connection tab on email or password error
    if (isset($_SESSION['loginError'])) {
        echo '<script>
                (new bootstrap.Modal(document.getElementById("loginModal"))).show();
                document.getElementById("loginEmail").value = "' . ($_SESSION['loginError']['loginEmail'] ?? '') . '";' .
            (isset($_SESSION['loginError']['email']) ? 'document.getElementById("loginEmailError").classList.remove("d-none");' : '') .
            (isset($_SESSION['loginError']['password']) ? 'document.getElementById("loginPasswordError").classList.remove("d-none");' : '') .
            '</script>';
        unset($_SESSION['loginError']);
    }

    // Display login modal on register tab on email duplicate error
    if (isset($_SESSION['registerError'])) {
        echo '<script>                
                (new bootstrap.Modal(document.getElementById("loginModal"))).show();
                (new bootstrap.Tab(document.getElementById("register-tab"))).show();
                
                document.getElementById("registerLastName").value = "' . ($registerValue['last_name'] ?? '') . '";' . '
                document.getElementById("registerFirstName").value = "' . ($registerValue['first_name'] ?? '') . '";' . '
                document.getElementById("registerEmail").value = "' . ($registerValue['email'] ?? '') . '";' . '
                document.getElementById("registerPassword").value = "' . ($registerValue['password'] ?? '') . '";' .
            (isset($_SESSION['registerError']['email']) ?
                'document.getElementById("registerEmailError").classList.remove("d-none");' : '') .
            '</script>';
        unset($_SESSION['registerError']);
    }
    ?>
</body>

</html>

<?php
ob_end_flush();
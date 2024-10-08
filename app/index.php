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

    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
        }

        .navbar {
            background-color: #1e1e1e !important;
        }

        .btn-primary {
            background-color: #9965f4;
            border-color: #9965f4;
        }

        .btn-primary:hover {
            background-color: #bb86fc;
            border-color: #bb86fc;
        }

        .card {
            background-color: #1e1e1e;
        }
    </style>
</head>

<body>
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
                    if ($_SESSION['user_id']) {
                        echo "<li class=\"nav-item\">
                                    <a class=\"nav-link fw-bold ms-2\" href=\"profile.php\">
                                        {$userInfo['first_name']} {$userInfo['last_name']}
                                    </a>
                                </li>";
                    } else {
                        echo "<li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"login.php\">
                                        <button type=\"button\" class=\"btn btn-success btn-sm\">Connexion</button>
                                    </a>
                                </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <header id="accueil" class="py-5">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Bienvenue sur mon CV/Portfolio</h1>
            <p class="lead">Découvrez mes compétences, expériences et projets</p>
            <a href="cv.php" class="btn btn-primary btn-lg mt-3">Voir les CV</a>
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
                <div class="col-md-6">
                    <h3>Compétences</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-light">PHP</li>
                        <li class="list-group-item bg-dark text-light">HTML/CSS</li>
                        <li class="list-group-item bg-dark text-light">JavaScript</li>
                        <li class="list-group-item bg-dark text-light">MySQL</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3>Expériences</h3>
                    <div class="card mb-3 text-light">
                        <div class="card-body">
                            <h5 class="card-title">Développeur Web</h5>
                            <h6 class="card-subtitle mb-2">Entreprise XYZ</h6>
                            <p class="card-text">2020 - Présent</p>
                        </div>
                    </div>
                    <div class="card text-light">
                        <div class="card-body">
                            <h5 class="card-title">Stagiaire en développement</h5>
                            <h6 class="card-subtitle mb-2">Entreprise ABC</h6>
                            <p class="card-text">2019 - 2020</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="cv_modif.php" class="btn btn-primary me-3">Modifier mon CV</a>
                <a href="#" class="btn btn-primary">Télécharger le CV en PDF</a>
            </div>
        </div>
    </section>

    <section id="projets" class="py-5 bg-dark">
        <div class="container">
            <h2 class="text-center mb-4">Mes Projets</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-light">
                        <div id="carouselProject1" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselProject1" data-bs-slide-to="0" class="active" aria-current="true"></button>
                                <button type="button" data-bs-target="#carouselProject1" data-bs-slide-to="1"></button>
                                <button type="button" data-bs-target="#carouselProject1" data-bs-slide-to="2"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="500" height="300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: First slide" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <title>Placeholder</title>
                                        <rect width="100%" height="100%" fill="#777"></rect><text x="50%" y="50%" fill="#555" dy=".3em">First slide</text>
                                    </svg>
                                </div>
                                <div class="carousel-item">
                                    <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="500" height="300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Second slide" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <title>Placeholder</title>
                                        <rect width="100%" height="100%" fill="#666"></rect><text x="50%" y="50%" fill="#444" dy=".3em">Second slide</text>
                                    </svg>
                                </div>
                                <div class="carousel-item">
                                    <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="500" height="300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Third slide" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <title>Placeholder</title>
                                        <rect width="100%" height="100%" fill="#555"></rect><text x="50%" y="50%" fill="#333" dy=".3em">Third slide</text>
                                    </svg>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject1" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselProject1" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Projet 1</h5>
                            <p class="card-text">Description du projet 1</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-light">
                        <div id="carouselProject2" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselProject2" data-bs-slide-to="0" class="active" aria-current="true"></button>
                                <button type="button" data-bs-target="#carouselProject2" data-bs-slide-to="1"></button>
                                <button type="button" data-bs-target="#carouselProject2" data-bs-slide-to="2"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="500" height="300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: First slide" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <title>Placeholder</title>
                                        <rect width="100%" height="100%" fill="#777"></rect><text x="50%" y="50%" fill="#555" dy=".3em">First slide</text>
                                    </svg>
                                </div>
                                <div class="carousel-item">
                                    <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="500" height="300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Second slide" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <title>Placeholder</title>
                                        <rect width="100%" height="100%" fill="#666"></rect><text x="50%" y="50%" fill="#444" dy=".3em">Second slide</text>
                                    </svg>
                                </div>
                                <div class="carousel-item">
                                    <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="500" height="300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Third slide" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <title>Placeholder</title>
                                        <rect width="100%" height="100%" fill="#555"></rect><text x="50%" y="50%" fill="#333" dy=".3em">Third slide</text>
                                    </svg>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject2" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselProject2" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Projet 2</h5>
                            <p class="card-text">Description du projet 2</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-light">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Projet 3">
                        <div class="card-body">
                            <h5 class="card-title">Projet 3</h5>
                            <p class="card-text">Description du projet 3</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="portfolio.php" class="btn btn-primary">Gérer mes Projets</a>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Contactez-moi</h2>
            <div class="row">
                <div class="col-md-6">
                    <form method="POST" data-bs-theme="dark">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="name" placeholder="John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="john_doe@exemple.com"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="4" placeholder="Entrez votre message..."
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
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

    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p>&copy; 2024 Mon CV/Portfolio</p>
            <div class="mt-3">
                <a href="https://www.instagram.com/erwnn_20/" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://github.com/erwnn20" class="text-light me-3"><i class="fab fa-github"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" class="text-light me-3"><i class="fab bi-folder"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" class="text-light me-3"><i class="fab bi-folder2-open"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" class="text-light me-3"><i class="fab bi-folder2"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" class="text-light me-3"><i class="fab bi-copy"></i></a>
                <a href="https://github.com/erwnn20/PHP-TP" class="text-light me-3"><i class="fab bi-download"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
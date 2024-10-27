<?php
session_start();

require_once 'util/elements.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Problème de serveur | Mon CV/Portfolio</title>

    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/styles/style.css">
    <link rel="stylesheet" href="/styles/errors.css">
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
                    <?php echo Element::headerUser($_SESSION['user']['data'], $_SESSION['user']['id']) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container error-container my-5">
        <div class="error-content">
            <h1 class="error-code">500</h1>
            <h2 class="error-message">Oups ! Problème de serveur</h2>
            <p class="error-description">
                Une erreur interne est survenue sur notre serveur. Nous travaillons pour résoudre le problème.<br>
                -<br>
                Vous pouvez revenir à la page d'accueil ou réessayer dans quelques instants.
                Si le problème persiste, n'hésitez pas à contacter notre support.
            </p>
            <a href="/" class="btn btn-primary btn-custom">
                <i class="fas fa-home me-2"></i>Retour à l'accueil
            </a>
        </div>
    </main>

    <?php echo Element::footer($_SESSION['user']['data']) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
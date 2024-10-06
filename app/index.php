<?php
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ynov-PHP</title>

    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .cards-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 75px;
        }

        .card-container {
            max-width: 300px;
            min-width: 250px;
        }
    </style>
</head>

<body class="bg-secondary">

    <header class="navbar fixed-top navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="https://cdn-icons-png.flaticon.com/512/8347/8347432.png" alt="Logo" width="30" class="d-inline-block align-text-top">
                Ynov-PHP
            </a>
            <div class="nav me-auto">
                <a class="nav-link link-secondary fw-semibold" href="#">CV</a>
                <a class="nav-link link-secondary fw-semibold" href="#">Portfolio</a>
            </div>
            <div class="navbar-text">
                <a class="link-light link-underline link-underline-opacity-0 link-offset-1-hover link-underline-opacity-75-hover me-2 fw-semibold" href="#">
                  Guest
                </a>
                <a href="#" class="link-underline link-underline-opacity-0">
                  <button type="button" class="btn btn-outline-light btn-sm">Login</button>
                </a>
                <a href="#" class="link-underline link-underline-opacity-0 hidden">
                  <button type="button" class="btn btn-outline-light btn-sm">Logout</button>
                </a>
            </div>
        </div>
    </header>

    <div class="container text-center" style="margin-top: 100px;">
        <h1 class="display-4 text-white">Welcome to Ynov-PHP</h1>
        <p class="lead text-light">Create your CV and portfolio in just a few clicks!</p>
    </div>

    <div class="container cards-container">
        <div class="col card-container">
            <div class="card text-center text-bg-dark">
                <div class="p-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/8347/8347432.png" class="card-img-top" alt="CV">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Create your CV</h5>
                    <p class="card-text">Create a professional CV quickly and stand out from the crowd in just a few
                        clicks!</p>
                    <a href="#" class="btn btn-primary">Create CV</a>
                </div>
            </div>
        </div>
        <div class="col card-container">
            <div class="card text-center text-bg-dark">
                <div class="p-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/4365/4365945.png" class="card-img-top" alt="Portfolio">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Create your Portfolio</h5>
                    <p class="card-text">Quickly create a professional portfolio and showcase your talents in just a few
                        clicks!
                    </p>
                    <a href="#" class="btn btn-primary">Create Portfolio</a>
                </div>
            </div>
        </div>
    </div>

    <nav class="nav fixed-bottom navbar-dark bg-dark">
        <a class="nav-link link-secondary" href="#">Contact</a>
        <a class="nav-link link-secondary" href="#">About us</a>
    </nav>
</body>

</html>
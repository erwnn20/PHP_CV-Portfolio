<?php
global $pdo;
ob_start();
session_start();
require 'db.php';

function getProjectsData($userID = null, $projectID = null)
{
    global $pdo;
    if (isset($userID)) {
        $stmt = $pdo->prepare('SELECT id, title, description, theme, link, images FROM project WHERE creator_id = :id');
        $stmt->execute(array(
            'id' => $userID
        ));
        return $stmt->fetchAll();
    }
    if (isset($projectID)) {
        $stmt = $pdo->prepare('SELECT * FROM project WHERE id = :id');
        $stmt->execute(array(
            'id' => $projectID
        ));
        return $stmt->fetch();
    }
    return array();
}

function saveProjectImg($projectID): array
{
    $files_name = array();

    $targetDirectory = 'img/projects/' . $projectID . '/';
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }

    foreach ($_FILES['projectImages']['name'] as $key => $imageName) {
        $tmpName = $_FILES['projectImages']['tmp_name'][$key];
        $imageError = $_FILES['projectImages']['error'][$key];

        if ($imageError === UPLOAD_ERR_OK) {
            $uniqueName = 'img-' . $key . '.png';
            $files_name[] = $uniqueName;

            move_uploaded_file($tmpName, $targetDirectory . $uniqueName);
        }
    }
    return $files_name;
}

function deleteProjectImg($projectID): void
{
    $dir = 'img/projects/' . $projectID . '/';
    $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($dir);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['projectTitle'])) {
        $uuid = $_POST['projectId'] ?: uuid_v4();
        $stmt = $pdo->prepare('INSERT INTO project (id, creator_id, title, description, theme, link, images)
                                    VALUES (:id, :creator_id, :title, :description, :theme, :link, :images)
                                    ON DUPLICATE KEY
                                    UPDATE
                                        title = VALUES(title),
                                        description = VALUES(description),
                                        theme = VALUES(theme),
                                        link = VALUES(link),
                                        images = VALUES(images);');
        $stmt->execute(array(
            'id' => $uuid,
            'creator_id' => $_SESSION['user_id'],
            'title' => $_POST['projectTitle'],
            //            'description' => str_replace("\r\n", '-_-', $_POST['projectDescription']),
            'description' => $_POST['projectDescription'],
            'theme' => $_POST['projectTheme'],
            'link' => $_POST['projectLink'],
            'images' => json_encode(saveProjectImg($uuid)),
        ));
    }

    if (isset($_POST['editProjectId'])) {
        $_SESSION['editProjectId'] = $_POST['editProjectId'];
    }

    if (isset($_POST['deleteProjectId'])) {
        deleteProjectImg($_POST['deleteProjectId']);
        $stmt = $pdo->prepare('DELETE FROM project WHERE id = :id;');
        $stmt->execute(array(
            'id' => $_POST['deleteProjectId']
        ));
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
    <link rel="stylesheet" href="styles/portfolio.css">

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
                        <a class="nav-link" href="/#projects">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#cv">CV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Gérer Projets</a>
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
            <h1 class="mb-4">Gérer mes projets</h1>

            <button class="btn btn-primary btn-custom mb-4" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="fas fa-plus"></i> Ajouter un nouveau projet
            </button>

            <div id="projectsList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                function displayProjectCard($id, $title, $description, $theme, $link, $images, int $index, bool $endBtn): void
                {
                    echo '<div class="col">
                            <div class="card project-card">';
                    if ($images) {
                        if (count($images) > 1) {
                            echo '  <div id="carouselProject-' . $index . '" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">';
                            foreach ($images as $image_i => $image)
                                echo '           <button type="button" data-bs-target="#carouselProject-' . $index . '" 
                                            data-bs-slide-to="' . $image_i . '"' . ($image_i == 0 ? ' class="active" aria-current="true"' : '') . '>
                                        </button>';
                            echo '          </div>
                                    <div class="carousel-inner">';
                            foreach ($images as $image_i => $image)
                                echo '        <div class="carousel-item ' . ($image_i == 0 ? ' active' : '') . '">
                                            <img src="img/projects/' . $id . '/' . $image . '" class="project-image bd-placeholder-img bd-placeholder-img-lg d-block w-100" alt="project_image-' . $image_i . '">
                                        </div>';
                            echo '          </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselProject-' . $index . '" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>';
                        } else echo '<img src="img/projects/' . $id . '/' . $images[0] . '" class="card-img-top project-image" alt="no image project">';
                    } else echo '<img src="img/projects/no_img.png" class="card-img-top project-image" alt="no image project">';
                    echo '      <div class="card-body d-flex flex-column">
                                    <div class="d-flex flex-wrap align-items-center mb-2">
                                        <h5 class="card-title text-break p-2 ps-0 m-0">' . $title . '</h5>
                                        <span class="badge rounded-pill text-bg-primary ms-auto">' . $theme . '</span>
                                    </div>
                                    <p class="card-text">' . nl2br($description) . '</p>
                                    <div class="d-flex mt-auto">' .
                        ($link ? '  <a href="' . $link . '" class="btn btn-sm btn-primary btn-custom me-auto" target="_blank">
                                            Voir le projet
                                        </a>' : '') .
                        ($endBtn ? '<form method="post" class="ms-auto">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" name="editProjectId" value="' . $id . '">Modifier</button>
                                            <button type="submit" class="btn btn-sm btn-danger ms-1" name="deleteProjectId" value="' . $id . '">Supprimer</button>
                                        </form>' : '') .
                        '</div>
                                </div>
                            </div>
                        </div>';
                }

                if (isset($projects_data)) {
                    foreach ($projects_data as $project_i => $project)
                        displayProjectCard(
                            $project['id'],
                            $project['title'],
                            $project['description'],
                            $project['theme'],
                            $project['link'],
                            json_decode($project['images'] ?? '[]'),
                            $project_i,
                            true
                        );
                } else displayProjectCard(
                    -1,
                    'Pas de projet enregistré',
                    isset($_SESSION['user_id']) ? 'Ajoutez vos projets personnels et professionnels ici !' : 'Connectez vous pour afficher vos projets personnels et professionnels',
                    '',
                    '',
                    [],
                    -1,
                    false
                );
                ?>
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

    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="addProjectModalLabel">Ajouter un nouveau projet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="projectForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="projectTitle" class="form-label">
                                <h5 class="mb-0">Titre du projet</h5>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="projectTitle" name="projectTitle" required>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="projectDescription" name="projectDescription" oninput="textAreaAdjust(this)" required></textarea>
                            <label for="projectDescription" class="form-label">Description du projet</label>
                        </div>
                        <div class="d-flex flex-wrap mb-3">
                            <div class="flex-grow-1 me-2">
                                <label for="projectLink" class="form-label">Lien vers le projet<small class="text-muted ms-2">optionnel</small></label>
                                <input type="url" class="form-control" id="projectLink" name="projectLink">
                            </div>
                            <div>
                                <label for="projectTheme" class="form-label">Thème<small class="text-muted ms-2">optionnel</small></label>
                                <input type="text" class="form-control" id="projectTheme" name="projectTheme" maxlength="20">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="projectImages" class="form-label">Images du projet<small class="text-muted ms-2">optionnel</small></label>
                            <input type="file" class="form-control" id="projectImages" name="projectImages[]" accept="image/*" multiple oninput="document.getElementById('imgInputWarning').classList.add('d-none');">
                            <span id="imgInputWarning" class="text-warning fst-italic ms-1 d-none" style="font-size: .9rem;">
                                <i class="bi-exclamation-circle"></i>
                                Attention ! Pensez à rajouter à nouveau vos images.
                            </span>
                        </div>
                        <div id="imagePreview" class="row mb-3"></div>
                        <div class="modal-footer">
                            <p id="projectIdDisplayContainer" class="text-secondary fst-italic flex-grow-1 user-select-none" style="font-size: .85rem;">
                                Modification projet :
                                <span id="projectIdDisplay" class="text-secondary ms-1"></span>
                            </p>

                            <button type="button" class="btn btn-link btn-sm link-secondary" style="font-size: .8rem;"
                                onclick="resetForm('projectForm')">Réinitialiser</button>
                            <button type="submit" class="btn btn-primary btn-custom" name="projectId">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm(formId) {
            document.forms[formId].reset();
            document.querySelector('button[name="projectId"]').value = '';
            document.getElementById('imgInputWarning').classList.add('d-none');
            document.getElementById('projectIdDisplayContainer').classList.add('d-none');
        }

        function textAreaAdjust(element) {
            element.style.height = '1px';
            element.style.height = (5 + element.scrollHeight) + 'px';
        }

        document.getElementById('projectImages').addEventListener('change', function(event) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.innerHTML = '';
            Array.from(event.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.className = 'col-3 mb-2';
                    imgElement.style.maxHeight = '100px';
                    imgElement.style.objectFit = 'cover';
                    imagePreview.appendChild(imgElement);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
    <?php
    if (isset($_SESSION['editProjectId'])) {
        $project_data = getProjectsData(projectID: $_SESSION['editProjectId']);
        echo '<script>
                document.getElementById("projectTitle").value = "' . $project_data['title'] . '";
                document.getElementById("projectDescription").value = "' . str_replace("\r\n", "\\r\\n", $project_data['description']) . '";
                document.getElementById("projectTheme").value = "' . $project_data['theme'] . '";
                document.getElementById("projectLink").value = "' . $project_data['link'] . '";

                document.getElementById("projectIdDisplay").innerText = "' . $project_data['id'] . '";
                document.querySelector(\'button[name="projectId"]\').value = "' . $project_data['id'] . '";
                
                document.getElementById("projectIdDisplayContainer").classList.remove("d-none");
                document.getElementById("imgInputWarning").classList.remove("d-none");
                
                (new bootstrap.Modal(document.getElementById("addProjectModal"))).show();
                
            </script>';

        unset($_SESSION['editProjectId']);
    }

    ?>
</body>

</html>

<?php
ob_end_flush();

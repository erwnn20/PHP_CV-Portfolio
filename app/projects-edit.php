<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/elements.php';
require_once 'util/user.php';
require_once 'util/projects.php';
global $pdo;

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
            'creator_id' => $_SESSION['user']['id'],
            'title' => $_POST['projectTitle'],
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

$projectsData = Projects::getData($_SESSION['user']['id'] ?? 0);
$inputDisable = isset($_SESSION['user']['id']) ? '' : 'disabled';
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
    <link rel="stylesheet" href="styles/projects.css">

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
                    <?php echo Element::headerUser($_SESSION['user']['data'], $_SESSION['user']['id'] ?? 0) ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container my-5">
            <h1 class="mb-4">Gérer mes projets</h1>
            <button class="btn btn-primary btn-custom mb-4" data-bs-toggle="modal" data-bs-target="#addProjectModal" <?php echo $inputDisable ?>>
            <i class="fas fa-plus"></i> Ajouter un nouveau projet
            </button>

            <div id="projectsList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                if ($projectsData) {
                    foreach ($projectsData as $project_i => $project)
                        Projects::displayCard_projectsEdit(
                            $project['id'],
                            $project['title'],
                            $project['description'],
                            $project['theme'],
                            $project['link'],
                            json_decode($project['images'] ?? '[]'),
                            (bool)$project['ban_id'],
                            $project_i,
                            true
                        );
                } else Projects::displayCard_projectsEdit(
                    -1,
                    'Pas de projet enregistré',
                    isset($_SESSION['user']['id']) ? 'Ajoutez vos projets personnels et professionnels ici !' : 'Connectez vous pour afficher vos projets personnels et professionnels',
                    '',
                    '',
                    null,
                    false,
                    -1,
                    false
                );
                ?>
            </div>
        </div>
    </main>

    <?php echo Element::footer($_SESSION['user']['data']) ?>

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
                            <p id="projectIdDisplayContainer" class="text-secondary fst-italic flex-grow-1 user-select-none d-none" style="font-size: .85rem;">
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
    <script src="scripts/textAreaAdjust.js"></script>
    <script>
        function resetForm(formId) {
            document.forms[formId].reset();
            document.querySelector('button[name="projectId"]').value = '';
            document.getElementById('imgInputWarning').classList.add('d-none');
            document.getElementById('projectIdDisplayContainer').classList.add('d-none');
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
    function format(string $text) : string
    {
        return htmlspecialchars(str_replace(
            array(
                "\\",
                "\"",
                "\r\n"
            ),
            array(
                "\\\\",
                "\\\"",
                "\\r\\n"
            ),
            $text
        ));
    }

    if (isset($_SESSION['editProjectId'])) {
        $projectData = Projects::getData(projectID: $_SESSION['editProjectId']);
        echo '<script>
                document.getElementById("projectTitle").value = "' . format($projectData['title']) . '";
                document.getElementById("projectDescription").value = "' . format($projectData['description']) . '";
                document.getElementById("projectTheme").value = "' . format($projectData['theme']) . '";
                document.getElementById("projectLink").value = "' . $projectData['link'] . '";

                document.getElementById("projectIdDisplay").innerText = "' . $projectData['id'] . '";
                document.querySelector(\'button[name="projectId"]\').value = "' . $projectData['id'] . '";
                
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

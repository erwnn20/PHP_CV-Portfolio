<?php
ob_start();
session_start();

require_once 'util/db.php';
require_once 'util/elements.php';
require_once 'util/user.php';
//require_once 'util/cv.php';
require_once 'util/projects.php';
global $pdo;

if (!isset($_SESSION['user']['id']) || !$_SESSION['user']['data']['admin']) {
    if (!isset($_SESSION['user']['id']))
        $_SESSION['loginError'] = array('external' => true);

    header("Location: /");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    my_print($_POST);
    if (isset($_POST['banType'])) {
        $banData = array(
            'banID' => uuid_v4(),
            'adminID' => $_SESSION['user']['id'],
            'banType' => $_POST['banType'],
            'banCause' => $_POST['banCause'],
            'banMessage' => $_POST['banMessage'],
            'bannedID' => $_POST['banId']
        );

        $stmt = $pdo->prepare('INSERT INTO ban (id, admin_id, cause, message)
                                    VALUES (:id, :admin_id, :cause, :message)
                                    ON DUPLICATE KEY
                                    UPDATE
                                        admin_id = VALUES(admin_id),
                                        cause = VALUES(cause),
                                        message = VALUES(message);');
        $stmt->execute(array(
            'id' => $banData['banID'],
            'admin_id' => $banData['adminID'],
            'cause' => $banData['banCause'],
            'message' => $banData['banMessage'],
        ));

        $stmt = $pdo->prepare('UPDATE '.$banData['banType'].' SET ban_id = :ban_id WHERE id = :id;');
        $stmt->execute(array(
            'id' => $banData['bannedID'],
            'ban_id' => $banData['banID'],
        ));
    }

    if (isset($_POST['userUnban'])) {
        $stmt = $pdo->prepare('DELETE FROM ban WHERE id = (SELECT ban_id FROM user WHERE id = :id);');
        $stmt->execute(array(
            'id' => $_POST['userUnban'],
        ));
    }

    if (isset($_POST['projectUnban'])) {
        $stmt = $pdo->prepare('DELETE FROM ban WHERE id = (SELECT ban_id FROM project WHERE id = :id);');
        $stmt->execute(array(
            'id' => $_POST['projectUnban'],
        ));
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

//

$usersData = User::getList(false);
$projectsData = Projects::getData(forBan: true);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrateur - Mon CV/Portfolio</title>

    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/admin.css">
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

    <main class="container my-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <h1 class="">Panel Administrateur</h1>
            <a href="http://127.0.0.1:8080/" target="_blank" class="btn btn-primary btn-custom">
                <i class="bi bi-database"></i>
                Accès à la Base de donnée
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Gestion des utilisateurs</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($usersData) {
                            foreach ($usersData as $user) {
                                echo '<tr>
                                        <td><img src="img/profile/'.($user['user_img'] ? 'user/'.$user['user_id'].'.png' : 'default.png').'" alt="" class="profile-picture"></td>
                                        <td>'.htmlspecialchars($user['user_first_name']).'</td>
                                        <td>'.htmlspecialchars($user['user_last_name']).'</td>
                                        <td>'.htmlspecialchars($user['user_email']).'</td>
                                        <td>'.
                                    ($user['ban_cause'] ?
                                            '<form method="post">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" name="userUnban" value="'.$user['user_id'].'">
                                                    Dé bannir
                                                </button>
                                                <span class="btn btn-sm btn-link"
                                                      data-bs-toggle="tooltip" data-bs-html="true" data-bs-custom-class="custom-tooltip"
                                                      title="
                                                      <i class=\'admin\'>par '.htmlspecialchars($user['admin_first_name']).' '.htmlspecialchars($user['admin_last_name']).'</i><br>
                                                      <span>Banni pour : <strong>'.htmlspecialchars($user['ban_cause']).'</strong></span><br>
                                                      <i class=\'message\'>'.htmlspecialchars($user['ban_message']).'</i>">
                                                      Voir
                                                </span>
                                            </form>' :
                                        '<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#banModal" data-ban-type="user" data-ban-id="'.$user['user_id'].'">Bannir</button>'
                                    ).
                                    '   </td>
                                    </tr>';
                            }
                        } else echo '<tr>
                                        <td></td>
                                        <td>Pas d\'utilisateur</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Gestion des projets</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Thème</th>
                            <th>Lien</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        if ($projectsData) {
                            foreach ($projectsData as $project) {
                                echo '<tr>
                                        <td>'.htmlspecialchars($project['project_title']).'</td>
                                        <td>'.htmlspecialchars($project['creator_first_name']).' '.htmlspecialchars($project['creator_last_name']).'</td>
                                        <td>'.htmlspecialchars($project['project_theme']).'</td>
                                        <td>'.($project['project_link'] ?
                                            '<a href="'.$project['project_link'].'" class="btn btn-sm btn-primary btn-custom">Voir projet</a>' : '').'
                                        </td>
                                        <td>'.
                                    ($project['ban_cause'] ?
                                            '<form method="post">
                                                <button type="submit" class="btn btn-sm btn-outline-success" name="projectUnban" value="'.$project['project_id'].'">
                                                    Afficher
                                                </button>
                                                <span class="btn btn-sm btn-link"
                                                      data-bs-toggle="tooltip" data-bs-html="true" data-bs-custom-class="custom-tooltip"
                                                      title="
                                                      <i class=\'admin\'>par '.htmlspecialchars($project['admin_first_name']).' '.htmlspecialchars($project['admin_last_name']).'</i><br>
                                                      <span>Banni pour : <strong>'.htmlspecialchars($project['ban_cause']).'</strong></span><br>
                                                      <i class=\'message\'>'.htmlspecialchars($project['ban_message']).'</i>">
                                                      Voir
                                                </span>
                                            </form>' :
                                        '<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#banModal" data-ban-type="project" data-ban-id="'.$project['project_id'].'">Masquer</button>'
                                    ).
                                    '   </td>
                                    </tr>';
                            }
                        } else echo '<tr>
                                        <td></td>
                                        <td>Pas de Projet</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php echo Element::footer($_SESSION['user']['data']) ?>

    <div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="banModalLabel">Bannir l'utilisateur/projet</h5>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="banForm">
                        <div class="mb-3">
                            <label for="banCause" class="form-label">Raison du bannissement</label>
                            <select class="form-select" id="banCause" name="banCause" required>
                                <option value="">Choisir une raison...</option>
                                <option value="inappropriate_content">Contenu inapproprié</option>
                                <option value="spam">Spam</option>
                                <option value="">Contrefaçon ou Plagiat</option>
                                <option value="">Activités Frauduleuses</option>
                                <option value="">Atteinte à la Vie Privée</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="banMessage" class="form-label">Message additionnel</label>
                            <textarea class="form-control" id="banMessage" name="banMessage" rows="3" oninput="textAreaAdjust(this)"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link btn-sm link-secondary" style="font-size: .8rem;" onclick="resetForm('addEducationForm')">Réinitialiser</button>
                            <button type="submit" class="btn btn-danger" id="confirmBan">Confirmer le bannissement</button>
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
        }

        document.getElementById('banModal').addEventListener('show.bs.modal', function (event) {
            const banForm = document.getElementById('banForm');
            const button = event.relatedTarget;
            const banType = button.getAttribute('data-ban-type');
            const banId = button.getAttribute('data-ban-id');
            const modalTitle = event.target.querySelector('.modal-title');

            modalTitle.textContent = banType === 'user' ? "Bannir l'utilisateur" : "Bannir le projet";

            let banTypeInput = document.getElementById('banTypeInput');
            if (!banTypeInput) {
                banTypeInput = document.createElement('input');
                banTypeInput.type = 'hidden';
                banTypeInput.name = 'banType';
                banTypeInput.id = 'banTypeInput';
                banForm.appendChild(banTypeInput);
            }
            banTypeInput.value = banType;

            let banIdInput = document.getElementById('banIdInput');
            if (!banIdInput) {
                banIdInput = document.createElement('input');
                banIdInput.type = 'hidden';
                banIdInput.name = 'banId';
                banIdInput.id = 'banIdInput';
                banForm.appendChild(banIdInput);
            }
            banIdInput.value = banId;
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>

</html>

<?php
ob_end_flush();

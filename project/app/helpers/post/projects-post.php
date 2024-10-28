<?php

require_once 'app/models/img-data.php';
require_once 'app/models/projects-data.php';
require_once 'app/helpers/uuid.php';

session_start();

if (isset($_POST['projectTitle']))
    Projects::update(
        ID: $_POST['projectId'] ?: UUID::generate(),
        userID: $_SESSION['user']['id'],
        title: $_POST['projectTitle'],
        description: $_POST['projectDescription'],
        theme: $_POST['projectTheme'],
        link: $_POST['projectLink'],
        imgPostValue: 'projectImages'
    );

if (isset($_POST['editProjectId']))
    $_SESSION['editProjectId'] = $_POST['editProjectId'];

if (isset($_POST['deleteProjectId'])) {
    Images::deleteFolder('projects/', $_POST['deleteProjectId']);
    Projects::delete($_POST['deleteProjectId']);
}

header("Location: /projects/edit");
exit;
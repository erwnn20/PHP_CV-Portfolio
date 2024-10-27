<?php

require_once 'util/router.php';
require_once 'util/db.php';
global $pdo;

// error handler
set_exception_handler(['ErrorHandler', 'handleException']);
register_shutdown_function(['ErrorHandler', 'handleShutdown']);

//router
$router = new Router($pdo);

$router->addRoute('GET', '/', 'home.php');
$router->addRoute('GET', '/profile', 'profile-edit.php');
$router->addRoute('GET', '/cv', 'resume.php');
$router->addRoute('GET', '/cv/edit', 'cv-edit.php');
$router->addRoute('GET', '/projects/edit', 'projects-edit.php');
$router->addRoute('GET', '/admin', 'admin-panel.php');
$router->addRoute('GET', '/logout', 'logout.php');

$router->addRoute('POST', '/profile', 'profile-data.php');
$router->addRoute('POST', '/cv', 'resume-select.php');
$router->addRoute('POST', '/cv/edit', 'cv-data.php');
$router->addRoute('POST', '/projects/edit', 'projects-data.php');
$router->addRoute('POST', '/ban', 'ban.php');
$router->addRoute('POST', '/login', 'login.php');

$router->handleRequest();

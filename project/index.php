<?php

require_once 'app/models/db.php';
require_once 'app/util/Router.php';
global $pdo;

$router = new Router($pdo);

$router->addRoute('GET', '/', 'HomeController', 'index');
$router->addRoute('GET', '/profile', 'ProfileController', 'edit');
$router->addRoute('GET', '/cv', 'CvController', 'index');
$router->addRoute('GET', '/cv/edit', 'CvController', 'edit');
$router->addRoute('GET', '/projects/edit', 'ProjectController', 'edit');
$router->addRoute('GET', '/admin', 'AdminController', 'index');
$router->addRoute('GET', '/logout', 'HomeController', 'logout');
$router->addRoute('GET', '/download', 'CvController', 'download');

$router->addRoute('POST', '/profile', 'ProfileController', 'post');
$router->addRoute('POST', '/cv', 'CvController', 'select');
$router->addRoute('POST', '/cv/edit', 'CvController', 'post');
$router->addRoute('POST', '/projects/edit', 'ProjectController', 'post');
$router->addRoute('POST', '/ban', 'AdminController', 'ban');
$router->addRoute('POST', '/login', 'HomeController', 'login');

$router->handleRequest($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

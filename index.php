<?php
define('BASE_URL', '/bigzone/');

define('ROOT_PATH', __DIR__);

$page = $_GET['page'] ?? 'accueil';

$pages_autorisees = [
    'accueil',
    'contact',
    'login',
    'inscription',
    'dashboard',
    'home',
    'devenir_partenaire',
    'profil_partenaire',
    'annonces',
    'logout',
    'conditions',
    'confidentialite'
];

$viewFile = __DIR__ . "/views/$page.php";

if (!in_array($page, $pages_autorisees) || !file_exists($viewFile)) {
    $viewFile = __DIR__ . "/views/404.php";
}


require $viewFile;

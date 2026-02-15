<?php
// logout.php
session_start();
session_destroy();

// Supprimer les cookies "Se souvenir de moi" si existants
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header("Location: " . BASE_URL . "login");
exit;
?>
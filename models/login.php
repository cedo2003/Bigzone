<?php
// models/login.php

header('Content-Type: application/json');
include '../controls/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, full_name, email, partenaire, code_unique, password FROM users WHERE email = ? AND is_active = 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
    exit;
}

session_start();
$_SESSION['user_id']   = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email']     = $user['email'];
$_SESSION['partenaire'] = $user['partenaire'];
$_SESSION['code_unique'] = $user['code_unique'];

// Option "Se souvenir de moi" → cookie sécurisé (exemple 30 jours)
if ($remember) {
    $token = bin2hex(random_bytes(32));
    $expiry = time() + (30 * 24 * 60 * 60); // 30 jours

    // Stocker le token dans la base (vous pouvez ajouter une table remember_tokens)
    // Pour simplifier ici, on utilise juste un cookie (moins sécurisé, mais rapide)
    setcookie('remember_token', $token, $expiry, '/', '', true, true);
    // À améliorer : stocker token + user_id + expiry dans une table
}

echo json_encode([
    'success'    => true,
    'full_name'  => $user['full_name']
]);
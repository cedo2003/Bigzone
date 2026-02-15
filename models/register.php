<?php
// register.php

header('Content-Type: application/json');
include '../controls/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$full_name       = trim($_POST['full_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$partenaire = 0; // Par défaut, pas un partenaire

$errors = [];

if (empty($full_name)) $errors[] = "Le nom complet est requis.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Un email valide est requis.";
if (empty($password) || strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
if ($password !== $password_confirm) $errors[] = "Les mots de passe ne correspondent pas.";

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
    exit;
}

// Vérifier email existant
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => "Cet email est déjà utilisé."]);
    exit;
}


do {
    $randomPart = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    $code_unique = "BZ-" . $randomPart;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE code_unique = ?");
    $stmt->execute([$code_unique]);
} while ($stmt->fetch());

// Hashage mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion
try {
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password, code_unique, created_at, partenaire)
        VALUES (?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([$full_name, $email, $hashed_password, $code_unique, $partenaire]);

    echo json_encode([
        'success'    => true,
        'code_unique' => $code_unique
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]);
}
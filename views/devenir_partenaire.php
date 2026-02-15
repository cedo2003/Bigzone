<?php
session_start();

// Récupération du code unique utilisateur
$code_unique = $_SESSION['code_unique'] ?? null;
if (!$code_unique) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non authentifié"
    ]);
    exit;
}

// Connexion à la base
require_once 'controls/bd.php'; // $pdo

// TRAITEMENT AJAX JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');

    // Vérifie que la requête contient du JSON
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (!str_contains($contentType, 'application/json')) {
        echo json_encode([
            "success" => false,
            "message" => "Requête invalide"
        ]);
        exit;
    }

    // Récupération et décodage du JSON
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        echo json_encode([
            "success" => false,
            "message" => "Données non reçues"
        ]);
        exit;
    }

    // Vérification des champs obligatoires
    $required = ['entreprise', 'secteur', 'responsable', 'telephone', 'email', 'description'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode([
                "success" => false,
                "message" => "Champ manquant : $field"
            ]);
            exit;
        }
    }

    // Transaction : insertion partenaire + update user
    try {
        $pdo->beginTransaction();

        // 1️⃣ Insertion dans partenaire
        $stmt = $pdo->prepare("
            INSERT INTO partenaire 
            (code_unique, entreprise, secteur, responsable, telephone, email, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            htmlspecialchars($code_unique),
            htmlspecialchars($data['entreprise']),
            htmlspecialchars($data['secteur']),
            htmlspecialchars($data['responsable']),
            htmlspecialchars($data['telephone']),
            htmlspecialchars($data['email']),
            htmlspecialchars($data['description'])
        ]);

        // 2️⃣ Mise à jour colonne partenaire dans table user
        $stmt = $pdo->prepare("
            UPDATE users
            SET partenaire = 1
            WHERE code_unique = ?
        ");
        $stmt->execute([htmlspecialchars($code_unique)]);

        $pdo->commit();

        echo json_encode([
            "success" => true,
            "message" => "Votre demande de partenariat a été envoyée et votre statut mis à jour."
        ]);
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur, veuillez réessayer"
        ]);
        exit;
    }
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Devenir partenaire – BigZone</title>
<meta name="viewport" content="width=device-width, initial-scale=1">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

<style>
body{
  font-family:Inter,sans-serif;
  background:#f6f8fc;
}
.navbar {
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      background: white !important;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
    }
.hero{
  background:linear-gradient(135deg,#667eea,#764ba2);
  color:#fff;
  padding:5rem 1rem;
}
.hero h1{
  font-weight:700;
}
.benefit-card{
  background:#fff;
  border-radius:16px;
  box-shadow:0 6px 20px rgba(0,0,0,.08);
}
.form-card{
  background:#fff;
  border-radius:18px;
  box-shadow:0 10px 30px rgba(0,0,0,.12);
}
.form-control, .form-select{
  border-radius:12px;
  padding:.75rem 1rem;
}
.btn-main{
  background:#667eea;
  border:none;
  border-radius:50px;
  font-weight:600;
  padding:.75rem 2.5rem;
}
footer{
  background:#fff;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand text-primary" href="<?= BASE_URL ?>home">BigZone</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>home">Accueil</a></li>
            <li class="nav-item"><a class="nav-link active" href="#">Devenir Partenaire</a></li>
        </ul>
      </div>
    </div>
  </nav>

<!-- HERO -->
<section class="hero text-center mt-5 pt-5">
  <div class="container">
    <h1>Devenez partenaire BigZone</h1>
    <p class="lead opacity-75 mt-3">
      Publiez vos offres, gagnez en visibilité et développez votre activité
      avec une plateforme de confiance.
    </p>
  </div>
</section>

<!-- AVANTAGES -->
<div class="container my-5">
  <div class="row g-4 text-center">

    <div class="col-md-4">
      <div class="benefit-card p-4 h-100">
        <i class="bi bi-megaphone-fill text-primary fs-1 mb-3"></i>
        <h5 class="fw-bold">Visibilité maximale</h5>
        <p class="text-muted">
          Vos annonces sont mises en avant auprès de milliers de visiteurs.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="benefit-card p-4 h-100">
        <i class="bi bi-shield-check text-success fs-1 mb-3"></i>
        <h5 class="fw-bold">Badge partenaire vérifié</h5>
        <p class="text-muted">
          Inspirez confiance avec un statut officiel BigZone.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="benefit-card p-4 h-100">
        <i class="bi bi-graph-up-arrow text-warning fs-1 mb-3"></i>
        <h5 class="fw-bold">Croissance rapide</h5>
        <p class="text-muted">
          Attirez plus de clients et augmentez vos ventes.
        </p>
      </div>
    </div>

  </div>
</div>

<!-- FORM -->
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="form-card p-5">

        <h3 class="fw-bold mb-4 text-center">Formulaire de partenariat</h3>

        <div id="alertBox"></div>

        <form id="partnerForm">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Entreprise</label>
              <input type="text" class="form-control" name="entreprise" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Secteur</label>
              <select class="form-select" name="secteur" required>
                <option value="">Choisir...</option>
                <option>E-commerce</option>
                <option>Immobilier</option>
                <option>Événementiel</option>
                <option>Autre</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Responsable</label>
              <input type="text" class="form-control" name="responsable" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Numéro whatsapp</label>
              <input type="tel" class="form-control" name="telephone" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="4" required></textarea>
            </div>
          </div>

          <div class="text-center mt-4">
            <button class="btn btn-main btn-lg">
              <i class="bi bi-send me-2"></i>Envoyer la demande
            </button>
          </div>

        </form>
    </div>
    </div>
    </div>
</div>

<script>
const form = document.getElementById("partnerForm");
const alertBox = document.getElementById("alertBox");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const btn = form.querySelector("button");
    btn.disabled = true;
    btn.innerHTML = "Envoi en cours...";

    const data = Object.fromEntries(new FormData(form));

    try {
        const response = await fetch(window.location.href, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            alertBox.innerHTML = `
              <div class="alert alert-success text-center">
                Demande envoyée avec succès 🎉
              </div>`;
            setTimeout(() => {
                window.location.href = "<?= BASE_URL ?>home";
            }, 1500);
        } else {
            alertBox.innerHTML = `
              <div class="alert alert-danger text-center">
                ${result.message}
              </div>`;
        }

    } catch (error) {
        alertBox.innerHTML = `
          <div class="alert alert-danger text-center">
            Erreur réseau
          </div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-send me-2"></i>Envoyer la demande`;
    }
});
</script>


<!-- FOOTER -->
<footer class="text-center py-4 text-muted">
  © <?= date('Y') ?> BigZone – Partenariats professionnels
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 800, once: true });
  </script>
</body>
</html>

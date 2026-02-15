<?php
require_once 'controls/bd.php';
session_start();

/* =========================
   PROTECTION
========================= */
if (!isset($_SESSION['user_id'])) {
  header("Location: " . BASE_URL . "login");
  exit;
}

$full_name = $_SESSION['full_name'] ?? 'Utilisateur';
$code_unique = $_SESSION['code_unique'] ?? null;

/* =========================
   SUPPRESSION D'ANNONCE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_annonce_id'])) {
  $annonce_id = (int) $_POST['delete_annonce_id'];

  // Vérification annonce appartenant au partenaire
  $stmt = $pdo->prepare("
        SELECT images, video 
        FROM annonces 
        WHERE id = ? AND code_unique = ?
    ");
  $stmt->execute([$annonce_id, $code_unique]);
  $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($annonce) {
    // Suppression images
    $images = json_decode($annonce['images'], true);
    if (is_array($images)) {
      foreach ($images as $img) {
        if (file_exists($img)) {
          unlink($img);
        }
      }
    }

    // Suppression vidéo
    if (!empty($annonce['video']) && file_exists($annonce['video'])) {
      unlink($annonce['video']);
    }

    // Suppression en base
    $stmt = $pdo->prepare("
            DELETE FROM annonces 
            WHERE id = ? AND code_unique = ?
        ");
    $stmt->execute([$annonce_id, $code_unique]);
  }

  header("Location: dashboard");
  exit;
}

/* =========================
   PARTENAIRE
========================= */
$stmt = $pdo->prepare("
    SELECT entreprise, secteur, logo 
    FROM partenaire 
    WHERE code_unique = ?
");
$stmt->execute([$code_unique]);
$partenaire = $stmt->fetch(PDO::FETCH_ASSOC);

// Logo par défaut
$logoUrl = (!empty($partenaire['logo']) && file_exists($partenaire['logo']))
  ? $partenaire['logo']
  : "https://via.placeholder.com/80x80?text=Logo";

/* =========================
   ANNONCES DU PARTENAIRE
========================= */
$stmt = $pdo->prepare("
    SELECT *
    FROM annonces
    WHERE code_unique = ?
    ORDER BY date_pub DESC
");
$stmt->execute([$code_unique]);
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>BigZone - Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: Inter, sans-serif;
      background: #f4f6fb;
    }

    .navbar {
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
    }

    .carousel-item img,
    .carousel-item video {
      height: 220px;
      width: 100%;
      object-fit: cover;
    }

    .stat-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .btn-create {
      background: #667eea;
      color: white;
      border-radius: 50px;
      padding: .8rem 2.5rem;
      font-weight: 600;
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary" href="<?= BASE_URL ?>dashboard">BigZone</a>

      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a style="color:red; font-weight:bold;" class="nav-link"
              href="<?= BASE_URL ?>home">Fermer le Dashboard</a></li>
          <li class="nav-item"><a class="nav-link active" href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>annonces">Publier une annonce</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>profil_partenaire">Mon profil</a></li>
        </ul>

        <div class="d-flex align-items-center">
          <span class="me-3"><?= htmlspecialchars($full_name) ?></span>
          <a href="<?= BASE_URL ?>logout" class="btn btn-outline-danger rounded-pill">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- CONTENU -->
  <div class="container pt-5 mt-5">

    <!-- BANNIÈRE -->
    <div class="card mb-5 p-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1"><?= htmlspecialchars($partenaire['entreprise'] ?? '') ?></h2>
          <p class="mb-0 text-muted">Secteur : <?= htmlspecialchars($partenaire['secteur'] ?? '') ?></p>
        </div>
        <img src="<?= $logoUrl ?>" class="stat-icon">
      </div>
    </div>

    <!-- BOUTON -->
    <div class="text-center mb-5">
      <a href="<?= BASE_URL ?>annonces" class="btn btn-create btn-lg">
        <i class="bi bi-plus-circle me-2"></i>Publier une annonce
      </a>
    </div>

    <!-- ANNONCES -->
    <h4 class="mb-4">Vos annonces</h4>

    <div class="row g-4">

      <?php if (empty($annonces)): ?>
        <div class="col-12">
          <div class="alert alert-info text-center">
            Aucune annonce publiée pour le moment.
          </div>
        </div>
      <?php endif; ?>

      <?php foreach ($annonces as $annonce): ?>
        <?php
        $images = json_decode($annonce['images'], true);
        if (!is_array($images))
          $images = [];
        ?>

        <div class="col-md-6 col-lg-4">
          <div class="card h-100 overflow-hidden">

            <!-- CARROUSEL -->
            <?php if (!empty($images) || !empty($annonce['video'])): ?>
              <div id="carousel<?= $annonce['id'] ?>" class="carousel slide">
                <div class="carousel-inner">

                  <?php foreach ($images as $i => $img): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                      <img src="<?= htmlspecialchars($img) ?>">
                    </div>
                  <?php endforeach; ?>

                  <?php if (!empty($annonce['video'])): ?>
                    <div class="carousel-item <?= empty($images) ? 'active' : '' ?>">
                      <video controls>
                        <source src="<?= htmlspecialchars($annonce['video']) ?>">
                      </video>
                    </div>
                  <?php endif; ?>

                </div>

                <?php if (count($images) + (!empty($annonce['video']) ? 1 : 0) > 1): ?>
                  <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $annonce['id'] ?>"
                    data-bs-slide="prev">
                    <span style="background-color: black; color: white; border: none;"
                      class="carousel-control-prev-icon"></span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $annonce['id'] ?>"
                    data-bs-slide="next">
                    <span style="background-color: black; color: white; border: none;"
                      class="carousel-control-next-icon"></span>
                  </button>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <div class="card-body">
              <h5><?= htmlspecialchars($annonce['titre']) ?></h5>
              <p class="small text-muted">
                <?= nl2br(htmlspecialchars(mb_strimwidth($annonce['donnees_specifiques'], 0, 100, '…'))) ?>
              </p>

              <div class="d-flex justify-content-between align-items-center">
                <small><?= date('d/m/Y', strtotime($annonce['date_pub'])) ?></small>

                <!-- BOUTON SUPPRIMER -->
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                  data-bs-target="#deleteModal<?= $annonce['id'] ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- MODAL SUPPRESSION -->
        <div class="modal fade" id="deleteModal<?= $annonce['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                Voulez-vous vraiment supprimer cette annonce ?
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="post">
                  <input type="hidden" name="delete_annonce_id" value="<?= $annonce['id'] ?>">
                  <button class="btn btn-danger">Oui, supprimer</button>
                </form>
              </div>
            </div>
          </div>
        </div>

      <?php endforeach; ?>

    </div>
  </div>

  <!-- Footer -->
  <footer style=" padding-top: 50px;" class="text-center">
    <div class="container">
      <p class="mb-0">© <?= date('Y') ?> BigZone - Tous droits réservés</p>
      <small><a href="<?= BASE_URL ?>privacy.html" class="text-light">Confidentialité</a> • <a
          href="<?= BASE_URL ?>cgu.html" class="text-light">CGU</a></small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
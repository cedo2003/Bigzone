<?php
require_once 'controls/bd.php';
session_start();

/* =========================
   PROTECTION
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$full_name   = $_SESSION['full_name'] ?? 'Utilisateur';
$code_unique = $_SESSION['code_unique'] ?? null;

/* =========================
   UTILISATEUR
========================= */
$stmt = $pdo->prepare("SELECT partenaire FROM users WHERE code_unique = ?");
$stmt->execute([$code_unique]);
$user = $stmt->fetch();
$partenaire_user = $user['partenaire'] ?? 0;

/* =========================
   ANNONCES PUBLIQUES
========================= */
$stmt = $pdo->query("
    SELECT 
        a.*, 
        p.entreprise, 
        p.logo
    FROM annonces a
    JOIN partenaire p ON p.code_unique = a.code_unique
    ORDER BY a.date_pub DESC
");
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>BigZone – Publications partenaires</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:Inter,sans-serif;
    background:#f6f8fc;
}
.navbar{
    background:white;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}
.hero{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
    padding:4rem 1rem;
}
.pub-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 24px rgba(0,0,0,.1);
    transition:.25s;
}
.pub-card:hover{
    transform:translateY(-8px);
}
.company{
    display:flex;
    align-items:center;
    gap:10px;
}
.company img{
    width:38px;
    height:38px;
    border-radius:50%;
    object-fit:cover;
}
.carousel-item img,
.carousel-item video{
    height:230px;
    width:100%;
    object-fit:cover;
}
.price{
    color:#667eea;
    font-weight:700;
}
.btn-interest{
    border-radius:50px;
    font-weight:600;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="<?= BASE_URL ?>home">BigZone</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Accueil</a></li>

        <?php if ($partenaire_user == 1): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>devenir_partenaire">Devenir partenaire</a></li>
        <?php endif; ?>
      </ul>

      <div class="d-flex align-items-center">
        <span class="me-3 text-muted"><?= htmlspecialchars($full_name) ?></span>
        <a href="<?= BASE_URL ?>logout" class="btn btn-outline-danger rounded-pill px-4">
          <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero text-center mt-5 pt-5">
  <h1 class="fw-bold">Publications de nos partenaires</h1>
  <p class="opacity-75">Des offres fiables proposées par des entreprises vérifiées</p>
</section>

<!-- ANNONCES -->
<div class="container my-5">
<div class="row g-4">

<?php if (empty($annonces)): ?>
<div class="col-12">
  <div class="alert alert-info text-center">
    Aucune publication disponible pour le moment.
  </div>
</div>
<?php endif; ?>

<?php foreach ($annonces as $annonce): ?>
<?php
$images = json_decode($annonce['images'], true);
if (!is_array($images)) $images = [];

$logo = (!empty($annonce['logo']) && file_exists($annonce['logo']))
    ? $annonce['logo']
    : "https://via.placeholder.com/60?text=Logo";
?>

<div class="col-md-6 col-lg-4">
<div class="card pub-card h-100">

<!-- CARROUSEL -->
<?php if (!empty($images) || !empty($annonce['video'])): ?>
<div id="carouselHome<?= $annonce['id'] ?>" class="carousel slide">

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
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselHome<?= $annonce['id'] ?>" data-bs-slide="prev">
    <span style="background-color: black; color: white; border: none;" class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselHome<?= $annonce['id'] ?>" data-bs-slide="next">
    <span style="background-color: black; color: white; border: none;" class="carousel-control-next-icon"></span>
  </button>
  <?php endif; ?>

</div>
<?php endif; ?>

<!-- CONTENU -->
<div class="card-body d-flex flex-column">

  <div class="company mb-2">
    <img src="<?= $logo ?>">
    <strong><?= htmlspecialchars($annonce['entreprise']) ?></strong>
  </div>

  <h5><?= htmlspecialchars($annonce['titre']) ?></h5>

  <p class="text-muted small">
    <?= nl2br(htmlspecialchars(mb_strimwidth($annonce['caracteristiques'],0,110,'…'))) ?>
  </p>

  <div class="mt-auto">
    <a href="#" class="btn btn-primary btn-interest w-100">
      <i class="bi bi-hand-thumbs-up me-2"></i>Contactér l'entreprise
    </a>
  </div>

</div>

</div>
</div>

<?php endforeach; ?>

</div>
</div>

<!-- FOOTER -->
<footer class="text-center py-4 bg-white text-muted">
© <?= date('Y') ?> BigZone – Publications partenaires
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

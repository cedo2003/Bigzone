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

$full_name = $_SESSION['full_name'] ?? 'Utilisateur';
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
        p.logo,
        p.telephone
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
    body {
      font-family: Inter, sans-serif;
      background: #f6f8fc;
    }

    .navbar {
      background: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
    }

    .hero {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 4rem 1rem;
    }

    .pub-card {
      border: none;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
      transition: .25s;
      cursor: pointer;
    }

    .pub-card:hover {
      transform: translateY(-8px);
    }

    .company {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .company img {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      object-fit: cover;
    }

    .carousel-item img,
    .carousel-item video {
      height: 350px;
      width: 100%;
      object-fit: contain;
      background-color: #f0f0f0;
    }

    .price {
      color: #667eea;
      font-weight: 700;
    }

    .btn-interest {
      border-radius: 50px;
      font-weight: 600;
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

    <!-- FITLRES -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
      <button class="btn btn-outline-primary rounded-pill active filter-btn" data-filter="all">Tout</button>
      <button class="btn btn-outline-primary rounded-pill filter-btn" data-filter="E-commerce">E-commerce</button>
      <button class="btn btn-outline-primary rounded-pill filter-btn" data-filter="Immobiliers">Immobiliers</button>
      <button class="btn btn-outline-primary rounded-pill filter-btn" data-filter="Événementiel">Événementiel</button>
      <button class="btn btn-outline-primary rounded-pill filter-btn" data-filter="Propulseur d’Activité">Propulseur
        d’Activité</button>
      <button class="btn btn-outline-primary rounded-pill filter-btn" data-filter="Autre">Autre</button>
    </div>

    <div class="row g-3">

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
        if (!is_array($images))
          $images = [];

        $logo = (!empty($annonce['logo']) && file_exists($annonce['logo']))
          ? $annonce['logo']
          : "https://via.placeholder.com/60?text=Logo";

        $mainImage = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x230?text=No+Image';

        // Encode for JS
        $annonceJson = htmlspecialchars(json_encode($annonce), ENT_QUOTES, 'UTF-8');

        $numero = $annonce["telephone"];
        $message = "Je suis intéressé par votre article " . $annonce['titre'];
        $link = "https://wa.me/+229$numero?text=" . urlencode($message);
        ?>

        <div class="col-6 col-md-6 col-lg-4 announcement-item"
          data-secteur="<?= htmlspecialchars($annonce['secteur']) ?>">
          <div class="card pub-card h-100" onclick="openDetailModal(<?= $annonceJson ?>)">

            <img src="<?= $mainImage ?>" style="height:230px; object-fit:cover;" class="card-img-top">

            <!-- CONTENU SIMPLIFIÉ -->
            <div class="card-body d-flex flex-column">

              <div class="company mb-2">
                <img src="<?= $logo ?>">
                <strong><?= htmlspecialchars($annonce['entreprise']) ?></strong>
              </div>

              <h5 class="mb-1"><?= htmlspecialchars($annonce['titre']) ?></h5>
              <span class="badge bg-primary align-self-start mb-3"><?= htmlspecialchars($annonce['secteur']) ?></span>

              <div class="mt-auto text-center">
                <a href="<?= $link ?>" class="btn btn-success btn-sm rounded-pill px-3" onclick="event.stopPropagation()">
                  <i class="bi bi-chat-text-fill me-2"></i>
                  Contacter l'entreprise
                </a>
              </div>

            </div>

          </div>
        </div>

      <?php endforeach; ?>

    </div>
  </div>

  <!-- MODAL DETAILS -->
  <div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <img id="detailLogo" src="" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
            <div>
              <h5 class="modal-title mb-0" id="detailTitre"></h5>
              <small class="text-muted" id="detailEntreprise"></small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Carousel -->
          <div id="detailCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
            <div class="carousel-inner" id="detailCarouselInner"></div>
            <button class="carousel-control-prev" type="button" data-bs-target="#detailCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-dark rounded p-2" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#detailCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-dark rounded p-2" aria-hidden="true"></span>
            </button>
          </div>

          <p id="detailDescription" class="text-muted"></p>

          <hr>
          <h6>Détails spécifiques :</h6>
          <ul id="detailSpecs" class="list-group list-group-flush mb-3"></ul>

          <div class="text-center">
            <a href="<?= $link ?>" id="modalBtnContact" class="btn btn-success btn-lg rounded-pill px-5"
              target="_blank">
              <i class="bi bi-chat-text-fill me-2"></i>
              Contacter l'entreprise
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="text-center py-4 bg-white text-muted">
    © <?= date('Y') ?> BigZone – Publications partenaires
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Field mappings (same as dashboard to decode keys)
    const fieldDefs = {
      'E-commerce': [
        { label: 'Catégorie', name: 'categorie' },
        { label: 'Prix (FCFA)', name: 'prix' },
        { label: 'Quantité stock', name: 'quantite' },
        { label: 'Poids / Dimensions', name: 'poids_dimensions' },
        { label: 'Livraison disponible ?', name: 'livraison_disponible' }
      ],
      'Immobilier': [
        { label: 'Type de bien', name: 'type_bien' },
        { label: 'Statut', name: 'statut' },
        { label: 'Prix', name: 'prix' },
        { label: 'Localisation', name: 'localisation' },
        { label: 'Surface (m²)', name: 'surface' },
        { label: 'Nombre de pièces', name: 'pieces' },
        { label: 'Capacité', name: 'capacite' },
        { label: 'Équipements', name: 'equipements' }
      ],
      'Événementiel': [
        { label: 'Type d\'événement', name: 'type_evenement' },
        { label: 'Date & Heure', name: 'date_heure' },
        { label: 'Lieu', name: 'lieu' },
        { label: 'Prix Entrée', name: 'prix' },
        { label: 'Contact Info', name: 'contact' }
      ],
      'Propulseur d’activité': [
        { label: 'Services proposés', name: 'services' },
        { label: 'Adresse / Siège', name: 'adresse' },
        { label: 'Horaires d\'ouverture', name: 'horaires' },
        { label: 'Contacts', name: 'contacts' },
        { label: 'Références', name: 'references' }
      ]
    };

    function openDetailModal(annonce) {
      document.getElementById('detailTitre').textContent = annonce.titre;
      document.getElementById('detailEntreprise').textContent = annonce.entreprise;
      document.getElementById('detailDescription').textContent = annonce.description;

      // Logo
      const logo = annonce.logo && annonce.logo !== '' ? annonce.logo : "https://via.placeholder.com/60?text=Logo";
      document.getElementById('detailLogo').src = logo;

      // Carousel
      const carouselInner = document.getElementById('detailCarouselInner');
      carouselInner.innerHTML = '';
      let images = [];
      try { images = JSON.parse(annonce.images); } catch (e) { }

      if (images.length > 0 || annonce.video) {
        images.forEach((img, idx) => {
          const active = idx === 0 ? 'active' : '';
          carouselInner.innerHTML += `<div class="carousel-item ${active}"><img src="${img}" class="d-block w-100" style="height:350px; object-fit:contain; background:#f0f0f0;"></div>`;
        });
        if (annonce.video) {
          const active = images.length === 0 ? 'active' : '';
          carouselInner.innerHTML += `<div class="carousel-item ${active}"><video controls class="d-block w-100" style="height:350px; background:#000;"><source src="${annonce.video}"></video></div>`;
        }
      } else {
        carouselInner.innerHTML = `<div class="carousel-item active"><div class="d-flex align-items-center justify-content-center bg-light" style="height:350px;">Pas d'image</div></div>`;
      }

      // Specific Specs
      const list = document.getElementById('detailSpecs');
      list.innerHTML = '';
      let specs = {};
      try { specs = JSON.parse(annonce.donnees_specifiques); } catch (e) { }

      for (const [key, value] of Object.entries(specs)) {
        // Try to find a readable label
        let label = key;
        const fieldGroup = fieldDefs[annonce.secteur];
        if (fieldGroup) {
          const def = fieldGroup.find(f => f.name === key);
          if (def) label = def.label;
        }
        let displayValue = value === true ? 'Oui' : (value === false ? 'Non' : value);
        if (displayValue) {
          list.innerHTML += `<li class="list-group-item d-flex justify-content-between"><span>${label}</span> <strong>${displayValue}</strong></li>`;
        }
      }

      // Contact Button (WhatsApp)
      const tel = annonce.telephone ? annonce.telephone.replace(/\s+/g, '') : '';
      const btn = document.getElementById('modalBtnContact');
      if (tel) {
        btn.href = `https://wa.me/${tel}?text=Bonjour, je suis intéressé par votre annonce : ${encodeURIComponent(annonce.titre)}`;
        btn.style.display = 'inline-block';
      } else {
        btn.style.display = 'none';
      }

      new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    // FILTRAGE
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        // Active class
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const value = this.getAttribute('data-filter');
        const items = document.querySelectorAll('.announcement-item');

        items.forEach(item => {
          if (value === 'all' || item.getAttribute('data-secteur') === value) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  </script>
</body>

</html>
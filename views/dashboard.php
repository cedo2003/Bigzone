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
   TRAITEMENT AJAX (UPDATE)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
  header('Content-Type: application/json');
  $input = json_decode(file_get_contents("php://input"), true);

  if (isset($input['action']) && $input['action'] === 'update') {
    $id = $input['id'] ?? null;
    if (!$id) {
      echo json_encode(['success' => false, 'message' => 'ID manquant']);
      exit;
    }

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM annonces WHERE id = ? AND code_unique = ?");
    $stmt->execute([$id, $code_unique]);
    if (!$stmt->fetch()) {
      echo json_encode(['success' => false, 'message' => 'Annonce introuvable ou non autorisée']);
      exit;
    }

    // Processing Images
    $finalImages = [];
    if (!empty($input['images'])) {
      if (!file_exists("uploads/annonces"))
        mkdir("uploads/annonces", 0777, true);

      foreach ($input['images'] as $index => $imgData) {
        // If it's a base64 string, save it
        if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
          $imgData = substr($imgData, strpos($imgData, ',') + 1);
          $type = strtolower($type[1]);
          if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $decoded = base64_decode($imgData);
            $imagePath = "uploads/annonces/{$code_unique}_" . time() . "_{$id}_{$index}." . $type;
            file_put_contents($imagePath, $decoded);
            $finalImages[] = $imagePath;
          }
        } else {
          // It's likely an existing path, keep it
          $finalImages[] = $imgData;
        }
      }
    }

    // Processing Video
    $videoPath = $input['video_existing'] ?? null; // Keep existing if not changed
    if (!empty($input['video'])) {
      $videoData = $input['video'];
      if (preg_match('/^data:video\/(\w+);base64,/', $videoData, $type)) {
        $videoData = substr($videoData, strpos($videoData, ',') + 1);
        $type = strtolower($type[1]);
        if (in_array($type, ['mp4', 'mov', 'avi', 'webm'])) {
          $decoded = base64_decode($videoData);
          if (!file_exists("uploads/videos"))
            mkdir("uploads/videos", 0777, true);
          $videoPath = "uploads/videos/{$code_unique}_" . time() . "_{$id}." . $type;
          file_put_contents($videoPath, $decoded);
        }
      }
    }

    // Specific Data
    $donnees_specifiques = isset($input['donnees_specifiques']) ? json_encode($input['donnees_specifiques'], JSON_UNESCAPED_UNICODE) : '{}';

    // Update DB
    try {
      $sql = "UPDATE annonces SET titre = ?, description = ?, secteur = ?, donnees_specifiques = ?, images = ?, video = ? WHERE id = ? AND code_unique = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        htmlspecialchars($input['titre']),
        htmlspecialchars($input['description']),
        htmlspecialchars($input['secteur']),
        $donnees_specifiques,
        json_encode($finalImages),
        $videoPath,
        $id,
        $code_unique
      ]);
      echo json_encode(['success' => true, 'message' => 'Annonce mise à jour avec succès']);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Erreur SQL: ' . $e->getMessage()]);
    }
    exit;
  }
}

/* =========================
   SUPPRESSION D'ANNONCE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_annonce_id'])) {
  $annonce_id = (int) $_POST['delete_annonce_id'];

  $stmt = $pdo->prepare("SELECT images, video FROM annonces WHERE id = ? AND code_unique = ?");
  $stmt->execute([$annonce_id, $code_unique]);
  $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($annonce) {
    // Delete files could be added here if needed, but often kept for backup or soft delete logic.
    // For now, strict delete from DB.
    $stmt = $pdo->prepare("DELETE FROM annonces WHERE id = ? AND code_unique = ?");
    $stmt->execute([$annonce_id, $code_unique]);
  }
  header("Location: dashboard");
  exit;
}

/* =========================
   DATA FETCHING
========================= */
// Partner Info
$stmt = $pdo->prepare("SELECT entreprise, secteur, logo FROM partenaire WHERE code_unique = ?");
$stmt->execute([$code_unique]);
$partenaire = $stmt->fetch(PDO::FETCH_ASSOC);
$logoUrl = (!empty($partenaire['logo']) && file_exists($partenaire['logo'])) ? $partenaire['logo'] : "https://via.placeholder.com/80x80?text=Logo";

// Annonces
$stmt = $pdo->prepare("SELECT * FROM annonces WHERE code_unique = ? ORDER BY date_pub DESC");
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
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .annonce-img {
      height: 200px;
      width: 100%;
      object-fit: cover;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
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

    .view-details {
      cursor: pointer;
    }

    /* Edit Modal Styles */
    .drop-zone {
      border: 2px dashed #667eea;
      border-radius: 12px;
      padding: 1rem;
      text-align: center;
      cursor: pointer;
      background: #f9f9fb;
    }

    .preview-container img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      margin: 5px;
    }

    .preview-container div {
      display: inline-block;
      position: relative;
    }

    .preview-container .remove-btn {
      position: absolute;
      top: -5px;
      right: -5px;
      background: red;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      text-align: center;
      line-height: 20px;
      cursor: pointer;
      font-size: 12px;
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary" href="<?= BASE_URL ?>dashboard">BigZone</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span
          class="navbar-toggler-icon"></span></button>
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
          <a href="<?= BASE_URL ?>logout" class="btn btn-outline-danger rounded-pill"><i
              class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </nav>

  <!-- CONTENT -->
  <div class="container pt-5 mt-5" style="padding-bottom: 100px;">

    <!-- HEADER -->
    <div class="card mb-5 p-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1"><?= htmlspecialchars($partenaire['entreprise'] ?? '') ?></h2>
          <p class="mb-0 text-muted">Secteur : <?= htmlspecialchars($partenaire['secteur'] ?? '') ?></p>
        </div>
        <img src="<?= $logoUrl ?>" class="stat-icon">
      </div>
    </div>

    <div class="text-center mb-5">
      <a href="<?= BASE_URL ?>annonces" class="btn btn-create btn-lg"><i class="bi bi-plus-circle me-2"></i>Publier une
        annonce</a>
    </div>

    <h4 class="mb-4">Vos annonces</h4>

    <div class="row g-4">
      <?php if (empty($annonces)): ?>
        <div class="col-12">
          <div class="alert alert-info text-center">Aucune annonce publiée pour le moment.</div>
        </div>
      <?php endif; ?>

      <?php foreach ($annonces as $annonce):
        $images = json_decode($annonce['images'], true) ?? [];
        $mainImage = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x200?text=No+Image';
        // Encode full data for JS
        $annonceJson = htmlspecialchars(json_encode($annonce), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 view-details" onclick="openViewModal(<?= $annonceJson ?>)">
            <img src="<?= $mainImage ?>" class="annonce-img" alt="Annonce">
            <div class="card-body">
              <h5 class="card-title text-truncate"><?= htmlspecialchars($annonce['titre']) ?></h5>
              <p class="card-text text-muted small"><i class="bi bi-calendar3"></i>
                <?= date('d/m/Y', strtotime($annonce['date_pub'])) ?></p>
              <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($annonce['secteur']) ?></span>
            </div>
            <div class="card-footer bg-white border-0 text-center">
              <button class="btn btn-outline-primary btn-sm rounded-pill px-4">Voir détails</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- VIEW MODAL -->
  <div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewTitle">Détails de l'annonce</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="viewCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
            <div class="carousel-inner" id="viewCarouselInner"></div>
            <button class="carousel-control-prev" type="button" data-bs-target="#viewCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-dark rounded p-2" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#viewCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-dark rounded p-2" aria-hidden="true"></span>
            </button>
          </div>

          <h4 id="viewTitre" class="mb-3"></h4>
          <span id="viewSecteur" class="badge bg-secondary mb-3"></span>
          <p id="viewDescription" class="text-muted"></p>

          <hr>
          <h5>Informations spécifiques</h5>
          <ul id="viewSpecs" class="list-group list-group-flush"></ul>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" id="btnDelete"><i class="bi bi-trash"></i> Supprimer</button>
          <div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            <button type="button" class="btn btn-primary" id="btnEdit"><i class="bi bi-pencil"></i> Modifier</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- EDIT MODAL -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modifier l'annonce</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" name="id" id="editId">

            <div class="mb-3">
              <label class="form-label">Secteur</label>
              <input type="text" class="form-control" name="secteur" id="editSecteur" readonly>
              <small class="text-muted">Le secteur ne peut pas être modifié.</small>
            </div>

            <div class="mb-3">
              <label class="form-label" id="editTitreLabel">Titre</label>
              <input type="text" name="titre" id="editTitre" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" id="editDescription" class="form-control" rows="4" required></textarea>
            </div>

            <!-- Dynamic Fields Container -->
            <div id="editDynamicFields" class="p-3 bg-light rounded mb-3"></div>

            <!-- Images -->
            <div class="mb-3">
              <label class="form-label">Images</label>
              <div class="drop-zone" id="editImageDrop">
                <i class="bi bi-cloud-upload"></i> Ajouter des images
                <input type="file" id="editImageInput" multiple accept="image/*" hidden>
              </div>
              <div id="editImagesPreview" class="preview-container mt-2"></div>
            </div>

            <!-- Video -->
            <div class="mb-3">
              <label class="form-label">Vidéo</label>
              <div class="drop-zone" id="editVideoDrop">
                <i class="bi bi-file-play"></i> Changer la vidéo
                <input type="file" id="editVideoInput" accept="video/*" hidden>
              </div>
              <div id="editVideoPreview" class="mt-2"></div>
            </div>

            <div id="editResult" class="text-center my-2"></div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-success" id="btnSaveEdit">Enregistrer les modifications</button>
        </div>
      </div>
    </div>
  </div>

  <!-- DELETE CONFIRM FORM (Hidden) -->
  <form id="deleteForm" method="POST" style="display:none">
    <input type="hidden" name="delete_annonce_id" id="deleteIdInput">
  </form>


  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentAnnonce = null;
    let editImages = []; // Stores strings (base64 or paths)
    let editVideo = null; // Stores string (base64 or path) or null

    // Field Definitions (Mirroring annonces.php)
    const fieldDefs = {
      'E-commerce': [
        { label: 'Catégorie', name: 'categorie', type: 'text' },
        { label: 'Prix (FCFA)', name: 'prix', type: 'number' },
        { label: 'Quantité stock', name: 'quantite', type: 'number' },
        { label: 'Poids / Dimensions', name: 'poids_dimensions', type: 'text' },
        { label: 'Livraison disponible ?', name: 'livraison_disponible', type: 'checkbox' }
      ],
      'Immobilier': [
        { label: 'Type de bien', name: 'type_bien', type: 'select', options: ['Appartement', 'Maison', 'Terrain', 'Bureau', 'Autre'] },
        { label: 'Statut', name: 'statut', type: 'select', options: ['A vendre', 'A louer'] },
        { label: 'Prix', name: 'prix', type: 'number' },
        { label: 'Localisation', name: 'localisation', type: 'text' },
        { label: 'Surface (m²)', name: 'surface', type: 'number' },
        { label: 'Nombre de pièces', name: 'pieces', type: 'number' },
        { label: 'Capacité', name: 'capacite', type: 'number' },
        { label: 'Équipements', name: 'equipements', type: 'text' }
      ],
      'Événementiel': [
        { label: 'Type d\'événement', name: 'type_evenement', type: 'text' },
        { label: 'Date & Heure', name: 'date_heure', type: 'datetime-local' },
        { label: 'Lieu', name: 'lieu', type: 'text' },
        { label: 'Prix Entrée', name: 'prix', type: 'number' },
        { label: 'Contact Info', name: 'contact', type: 'text' }
      ],
      'Propulseur d’activité': [
        { label: 'Services proposés', name: 'services', type: 'textarea' },
        { label: 'Adresse / Siège', name: 'adresse', type: 'text' },
        { label: 'Horaires d\'ouverture', name: 'horaires', type: 'text' },
        { label: 'Contacts', name: 'contacts', type: 'text' },
        { label: 'Références', name: 'references', type: 'text' }
      ]
    };

    function openViewModal(annonce) {
      currentAnnonce = annonce;

      // Populate View Modal
      document.getElementById('viewTitle').textContent = annonce.titre;
      document.getElementById('viewSecteur').textContent = annonce.secteur;
      document.getElementById('viewDescription').textContent = annonce.description;

      // Images Carousel
      const carouselInner = document.getElementById('viewCarouselInner');
      carouselInner.innerHTML = '';
      let images = [];
      try { images = JSON.parse(annonce.images); } catch (e) { }

      if (images.length > 0 || annonce.video) {
        images.forEach((img, idx) => {
          const active = idx === 0 ? 'active' : '';
          carouselInner.innerHTML += `<div class="carousel-item ${active}"><img src="${img}" class="d-block w-100" style="height:300px; object-fit:contain; background:#000;"></div>`;
        });
        if (annonce.video) {
          const active = images.length === 0 ? 'active' : '';
          carouselInner.innerHTML += `<div class="carousel-item ${active}"><video controls class="d-block w-100" style="height:300px; background:#000;"><source src="${annonce.video}"></video></div>`;
        }
      } else {
        carouselInner.innerHTML = `<div class="carousel-item active"><div class="d-flex align-items-center justify-content-center bg-light" style="height:300px;">Pas d'image</div></div>`;
      }

      // Specific Specs
      const list = document.getElementById('viewSpecs');
      list.innerHTML = '';
      let specs = {};
      try { specs = JSON.parse(annonce.donnees_specifiques); } catch (e) { }

      for (const [key, value] of Object.entries(specs)) {
        // Try to find a readable label if possible
        let label = key;
        const fieldGroup = fieldDefs[annonce.secteur];
        if (fieldGroup) {
          const def = fieldGroup.find(f => f.name === key);
          if (def) label = def.label;
        }
        let displayValue = value === true ? 'Oui' : (value === false ? 'Non' : value);
        list.innerHTML += `<li class="list-group-item d-flex justify-content-between"><strong>${label}</strong> <span>${displayValue}</span></li>`;
      }

      // Setup Buttons
      document.getElementById('btnDelete').onclick = () => {
        if (confirm('Voulez-vous vraiment supprimer cette annonce ?')) {
          document.getElementById('deleteIdInput').value = annonce.id;
          document.getElementById('deleteForm').submit();
        }
      };
      document.getElementById('btnEdit').onclick = () => {
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewModal'));
        viewModal.hide();
        openEditModal(annonce);
      };

      new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    function openEditModal(annonce) {
      currentAnnonce = annonce;

      // Basic Fields
      document.getElementById('editId').value = annonce.id;
      document.getElementById('editSecteur').value = annonce.secteur;
      document.getElementById('editTitre').value = annonce.titre;
      document.getElementById('editDescription').value = annonce.description;

      // Dynamic Fields Render
      const container = document.getElementById('editDynamicFields');
      container.innerHTML = '';
      const defs = fieldDefs[annonce.secteur] || [];
      let specs = {};
      try { specs = JSON.parse(annonce.donnees_specifiques); } catch (e) { }

      defs.forEach(field => {
        let val = specs[field.name] || '';
        let html = `<div class="mb-3">`;

        if (field.type === 'checkbox') {
          const checked = val ? 'checked' : '';
          html += `
                        <div class="form-check">
                            <input class="form-check-input spec-field" type="checkbox" data-name="${field.name}" id="field_${field.name}" ${checked}>
                            <label class="form-check-label" for="field_${field.name}">${field.label}</label>
                        </div>`;
        } else if (field.type === 'select') {
          html += `<label class="form-label">${field.label}</label>
                             <select class="form-control spec-field" data-name="${field.name}">`;
          field.options.forEach(opt => {
            const selected = val === opt ? 'selected' : '';
            html += `<option value="${opt}" ${selected}>${opt}</option>`;
          });
          html += `</select>`;
        } else if (field.type === 'textarea') {
          html += `<label class="form-label">${field.label}</label>
                             <textarea class="form-control spec-field" data-name="${field.name}">${val}</textarea>`;
        } else {
          html += `<label class="form-label">${field.label}</label>
                              <input type="${field.type}" class="form-control spec-field" data-name="${field.name}" value="${val}">`;
        }
        html += `</div>`;
        container.innerHTML += html;
      });

      // Images initialization
      editImages = [];
      try { editImages = JSON.parse(annonce.images); } catch (e) { }
      if (!Array.isArray(editImages)) editImages = [];
      refreshEditImages();

      // Video initialization
      editVideo = annonce.video;
      refreshEditVideo();

      new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    /* EDIT: IMAGES LIGOC */
    function refreshEditImages() {
      const container = document.getElementById('editImagesPreview');
      container.innerHTML = '';
      editImages.forEach((img, idx) => {
        const div = document.createElement('div');
        div.innerHTML = `<img src="${img}"><div class="remove-btn" onclick="removeEditImage(${idx})">x</div>`;
        container.appendChild(div);
      });
    }
    function removeEditImage(idx) {
      editImages.splice(idx, 1);
      refreshEditImages();
    }
    document.getElementById('editImageDrop').onclick = () => document.getElementById('editImageInput').click();
    document.getElementById('editImageInput').onchange = function () {
      Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
          if (editImages.length < 5) {
            editImages.push(e.target.result);
            refreshEditImages();
          } else { alert('Max 5 images'); }
        };
        reader.readAsDataURL(file);
      });
      this.value = '';
    };

    /* EDIT: VIDEO LOGIC */
    function refreshEditVideo() {
      const container = document.getElementById('editVideoPreview');
      container.innerHTML = '';
      if (editVideo) {
        container.innerHTML = `<video src="${editVideo}" controls style="max-height:100px; display:block; margin-bottom:5px;"></video>
                                       <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEditVideo()">Supprimer vidéo</button>`;
      }
    }
    function removeEditVideo() { editVideo = null; refreshEditVideo(); }
    document.getElementById('editVideoDrop').onclick = () => document.getElementById('editVideoInput').click();
    document.getElementById('editVideoInput').onchange = function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => { editVideo = e.target.result; refreshEditVideo(); };
        reader.readAsDataURL(file);
      }
      this.value = '';
    };

    /* SAVE EDIT */
    document.getElementById('btnSaveEdit').onclick = async function () {
      const btn = this;
      const id = document.getElementById('editId').value;
      const titre = document.getElementById('editTitre').value;
      const description = document.getElementById('editDescription').value;
      const secteur = document.getElementById('editSecteur').value;

      // Collect Specs
      const specs = {};
      document.querySelectorAll('#editDynamicFields .spec-field').forEach(field => {
        const key = field.dataset.name;
        if (field.type === 'checkbox') specs[key] = field.checked;
        else specs[key] = field.value;
      });

      const payload = {
        action: 'update',
        id: id,
        titre: titre,
        description: description,
        secteur: secteur,
        donnees_specifiques: specs,
        images: editImages,
        video: editVideo && editVideo.startsWith('data:') ? editVideo : null, // Only send base64 if new
        video_existing: editVideo && !editVideo.startsWith('data:') ? editVideo : null // Keep path if existing
      };

      btn.disabled = true;
      btn.innerHTML = 'Enregistrement...';

      try {
        const res = await fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
          alert('Modification enregistrée !');
          location.reload();
        } else {
          document.getElementById('editResult').innerHTML = `<div class="text-danger">${json.message}</div>`;
        }
      } catch (e) {
        console.error(e);
        alert('Erreur réseau');
      } finally {
        btn.disabled = false;
        btn.innerHTML = 'Enregistrer les modifications';
      }
    };
  </script>
</body>

</html>
<?php
session_start();
require_once 'controls/bd.php';

$code_unique = $_SESSION['code_unique'] ?? null;
if (!$code_unique) {
    header("Location: login");
    exit;
}

// TRAITEMENT AJAX JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Données invalides"]);
        exit;
    }

    $required = ['titre', 'description', 'images', 'secteur'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(["success" => false, "message" => "Tous les champs obligatoires sont manquants ($field)"]);
            exit;
        }
    }
    if (empty($data['images']) || count($data['images']) == 0) {
        echo json_encode(["success" => false, "message" => "Au moins une image est obligatoire"]);
        exit;
    }

    // Gestion images
    $imagePaths = [];
    if (!empty($data['images'])) {
        if (!file_exists("uploads/annonces"))
            mkdir("uploads/annonces", 0777, true);
        foreach ($data['images'] as $index => $imgData) {
            // Check if it's base64
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo json_encode(["success" => false, "message" => "Format image non supporté"]);
                    exit;
                }
                $imgData = base64_decode($imgData);
                $imagePath = "uploads/annonces/{$code_unique}_" . time() . "_$index." . $type;
                file_put_contents($imagePath, $imgData);
                $imagePaths[] = $imagePath;
            }
        }
    }

    // Gestion vidéo optionnelle
    $videoPath = null;
    if (!empty($data['video'])) {
        $videoData = $data['video'];
        if (preg_match('/^data:video\/(\w+);base64,/', $videoData, $type)) {
            $videoData = substr($videoData, strpos($videoData, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['mp4', 'mov', 'avi', 'webm'])) {
                echo json_encode(["success" => false, "message" => "Format vidéo non supporté"]);
                exit;
            }
            $videoData = base64_decode($videoData);
            if (!file_exists("uploads/videos"))
                mkdir("uploads/videos", 0777, true);
            $videoPath = "uploads/videos/{$code_unique}_" . time() . "." . $type;
            file_put_contents($videoPath, $videoData);
        }
    }

    // Donnees specifiques
    $donnees_specifiques = isset($data['donnees_specifiques']) ? json_encode($data['donnees_specifiques'], JSON_UNESCAPED_UNICODE) : '{}';

    // Insert DB
    // Note: 'caracteristiques' was replaced by 'description' to match user schema
    try {
        $stmt = $pdo->prepare("INSERT INTO annonces (code_unique, secteur, titre, description, donnees_specifiques, images, video) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $code_unique,
            htmlspecialchars($data['secteur']),
            htmlspecialchars($data['titre']),
            htmlspecialchars($data['description']),
            $donnees_specifiques,
            json_encode($imagePaths),
            $videoPath
        ]);
        echo json_encode(["success" => true, "message" => "Annonce publiée avec succès"]);
        exit;
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Erreur serveur: " . $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Publier une annonce – BigZone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            background: white !important;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .form-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
        }

        .form-control,
        textarea,
        select {
            border-radius: 12px;
            padding: .7rem .9rem;
        }

        .drop-zone {
            border: 2px dashed #667eea;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: .2s;
            background: #f9f9fb;
        }

        .drop-zone.dragover {
            background: #e0e7ff;
        }

        .drop-zone i {
            font-size: 2rem;
            color: #667eea;
        }

        .preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .preview img,
        .preview video {
            max-width: 120px;
            max-height: 120px;
            border-radius: 12px;
        }

        .remove-btn {
            cursor: pointer;
            color: red;
            font-size: .9rem;
            text-align: center;
        }

        .sector-fields {
            display: none;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="<?= BASE_URL ?>dashboard">BigZone</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span
                    class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a style="color:red; font-weight:bold;" class="nav-link"
                            href="<?= BASE_URL ?>home">Fermer le Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?= BASE_URL ?>annonces">Publier une
                            annonce</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>profil_partenaire">Mon Profil</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="<?= BASE_URL ?>logout" class="btn btn-outline-danger rounded-pill px-4"><i
                            class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
                </div>
            </div>
        </div>
    </nav><br><br>
    <div class="container my-5">
        <div class="form-card mx-auto" style="max-width:800px;">
            <h3 class="mb-4 text-center">Publier une annonce</h3>
            <div id="result"></div>
            <form id="annonceForm">

                <!-- Secteur Selection (Moved to top as requested) -->
                <div class="mb-3">
                    <label class="form-label">Secteur d'activité <span class="text-danger">*</span></label>
                    <select name="secteur" id="secteurSelect" class="form-control" required>
                        <option value="">-- Sélectionnez un secteur --</option>
                        <option value="E-commerce">E-commerce</option>
                        <option value="Immobilier">Immobilier</option>
                        <option value="Événementiel">Événementiel</option>
                        <option value="Propulseur d’activité">Propulseur d’activité</option>
                    </select>
                </div>

                <!-- Common Fields -->
                <div class="mb-3">
                    <label class="form-label" id="titreLabel">Titre / Nom <span class="text-danger">*</span></label>
                    <input type="text" name="titre" class="form-control" required
                        placeholder="Ex: iPhone 13, Appartement T3...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required
                        placeholder="Décrivez votre annonce en détail..."></textarea>
                </div>

                <!-- Dynamic Fields: E-commerce -->
                <div id="fields-E-commerce" class="sector-fields">
                    <h5 class="mb-3 text-primary"><i class="bi bi-cart"></i> Détails E-commerce</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie</label>
                            <input type="text" class="form-control spec-field" data-name="categorie">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix (FCFA)</label>
                            <input type="number" class="form-control spec-field" data-name="prix">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantité stock</label>
                            <input type="number" class="form-control spec-field" data-name="quantite">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Poids / Dimensions</label>
                            <input type="text" class="form-control spec-field" data-name="poids_dimensions"
                                placeholder="Ex: 2kg, 10x20cm">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input spec-field" type="checkbox"
                                    data-name="livraison_disponible" id="livraisonCheck">
                                <label class="form-check-label" for="livraisonCheck">Livraison disponible ?</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Fields: Immobilier -->
                <div id="fields-Immobilier" class="sector-fields">
                    <h5 class="mb-3 text-primary"><i class="bi bi-house"></i> Détails Immobilier</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de bien</label>
                            <select class="form-control spec-field" data-name="type_bien">
                                <option value="Appartement">Appartement</option>
                                <option value="Maison">Maison</option>
                                <option value="Terrain">Terrain</option>
                                <option value="Bureau">Bureau</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-control spec-field" data-name="statut">
                                <option value="A vendre">A vendre</option>
                                <option value="A louer">A louer</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix (Loyer/Vente)</label>
                            <input type="number" class="form-control spec-field" data-name="prix">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Localisation</label>
                            <input type="text" class="form-control spec-field" data-name="localisation">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Surface (m²)</label>
                            <input type="number" class="form-control spec-field" data-name="surface">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nombre de pièces</label>
                            <input type="number" class="form-control spec-field" data-name="pieces">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Capacité (pers)</label>
                            <input type="number" class="form-control spec-field" data-name="capacite">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Équipements (séparés par virgule)</label>
                            <input type="text" class="form-control spec-field" data-name="equipements"
                                placeholder="Wifi, Piscine, Garage...">
                        </div>
                    </div>
                </div>

                <!-- Dynamic Fields: Événementiel -->
                <div id="fields-Événementiel" class="sector-fields">
                    <h5 class="mb-3 text-primary"><i class="bi bi-calendar-event"></i> Détails Événementiel</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type d'événement</label>
                            <input type="text" class="form-control spec-field" data-name="type_evenement"
                                placeholder="Concert, Conférence...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date & Heure</label>
                            <input type="datetime-local" class="form-control spec-field" data-name="date_heure">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control spec-field" data-name="lieu">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix Entrée</label>
                            <input type="number" class="form-control spec-field" data-name="prix">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Contact Info</label>
                            <input type="text" class="form-control spec-field" data-name="contact">
                        </div>
                    </div>
                </div>

                <!-- Dynamic Fields: Propulseur d’activité -->
                <div id="fields-Propulseur d’activité" class="sector-fields">
                    <h5 class="mb-3 text-primary"><i class="bi bi-rocket"></i> Détails Propulseur d’activité</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Services proposés</label>
                            <textarea class="form-control spec-field" rows="3" data-name="services"
                                placeholder="Liste des services..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adresse / Siège</label>
                            <input type="text" class="form-control spec-field" data-name="adresse">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Horaires d'ouverture</label>
                            <input type="text" class="form-control spec-field" data-name="horaires"
                                placeholder="Lun-Ven: 8h-18h">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contacts (Tél/Email)</label>
                            <input type="text" class="form-control spec-field" data-name="contacts">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Références / Site Web</label>
                            <input type="text" class="form-control spec-field" data-name="references">
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-3">
                    <label class="form-label">Images (max 5) <span class="text-danger">*</span></label>
                    <div class="drop-zone" id="imageDrop">
                        <i class="bi bi-image"></i>
                        <p>Glissez-déposez jusqu'à 5 images ou cliquez pour sélectionner</p>
                        <input type="file" id="imageInput" accept="image/*" multiple style="display:none;">
                    </div>
                    <div class="preview" id="imagePreviewContainer"></div>
                </div>

                <!-- Vidéo -->
                <div class="mb-3">
                    <label class="form-label">Vidéo (optionnelle, max 1)</label>
                    <div class="drop-zone" id="videoDrop">
                        <i class="bi bi-camera-video"></i>
                        <p>Glissez-déposez une vidéo ou cliquez pour sélectionner</p>
                        <input type="file" id="videoInput" accept="video/*" style="display:none;">
                    </div>
                    <div class="preview" id="videoPreviewContainer"></div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <i class="bi bi-send me-2"></i>Publier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const imageDrop = document.getElementById('imageDrop');
        const videoDrop = document.getElementById('videoDrop');
        const imageInput = document.getElementById('imageInput');
        const videoInput = document.getElementById('videoInput');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const videoPreviewContainer = document.getElementById('videoPreviewContainer');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('annonceForm');
        const result = document.getElementById('result');
        const secteurSelect = document.getElementById('secteurSelect');
        const titreLabel = document.getElementById('titreLabel');

        let imagesData = [];
        let videoData = null;

        // IDs of all sector containers
        const sectorIds = [
            'fields-E-commerce',
            'fields-Immobilier',
            'fields-Événementiel',
            'fields-Propulseur d’activité'
        ];

        // Handle Sector Change
        secteurSelect.addEventListener('change', function () {
            const selected = this.value;

            // Updates labels based on sector logic
            if (selected === 'E-commerce') titreLabel.innerHTML = 'Nom du produit <span class="text-danger">*</span>';
            else if (selected === 'Immobilier') titreLabel.innerHTML = 'Nom du bien <span class="text-danger">*</span>';
            else if (selected === 'Événementiel') titreLabel.innerHTML = 'Titre de l\'événement <span class="text-danger">*</span>';
            else if (selected === 'Propulseur d’activité') titreLabel.innerHTML = 'Nom de l\'activité <span class="text-danger">*</span>';
            else titreLabel.innerHTML = 'Titre / Nom <span class="text-danger">*</span>';

            // Show/Hide specific fields
            sectorIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    if (id === 'fields-' + selected) {
                        el.style.display = 'block';
                    } else {
                        el.style.display = 'none';
                    }
                }
            });
        });

        /* =========================
           IMAGES
        ========================= */
        function refreshImagesPreview() {
            imagePreviewContainer.innerHTML = '';
            imagesData.forEach((img, index) => {
                const div = document.createElement('div');
                div.innerHTML = `
            <img src="${img}">
            <div class="remove-btn" onclick="removeImage(${index})">Supprimer</div>
        `;
                imagePreviewContainer.appendChild(div);
            });
            toggleSubmit();
        }

        function addImage(file) {
            if (imagesData.length >= 5) {
                alert("Maximum 5 images autorisées");
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                imagesData.push(e.target.result);
                refreshImagesPreview();
            };
            reader.readAsDataURL(file);
        }

        function removeImage(index) {
            imagesData.splice(index, 1);
            refreshImagesPreview();
        }

        function toggleSubmit() {
            // Optional: validation logic here to enable/disable button
        }

        /* Drag & drop images */
        imageDrop.addEventListener('click', () => imageInput.click());
        imageDrop.addEventListener('dragover', e => {
            e.preventDefault(); imageDrop.classList.add('dragover');
        });
        imageDrop.addEventListener('dragleave', () => imageDrop.classList.remove('dragover'));
        imageDrop.addEventListener('drop', e => {
            e.preventDefault(); imageDrop.classList.remove('dragover');
            [...e.dataTransfer.files].forEach(file => { if (file.type.startsWith('image/')) addImage(file); });
        });
        imageInput.addEventListener('change', () => {
            [...imageInput.files].forEach(file => { if (file.type.startsWith('image/')) addImage(file); });
            imageInput.value = '';
        });

        /* =========================
           VIDÉO (1 SEULE)
        ========================= */
        function createVideoPreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                videoPreviewContainer.innerHTML = `
            <video src="${e.target.result}" controls></video>
            <div class="remove-btn" onclick="removeVideo()">Supprimer</div>
        `;
                videoData = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function removeVideo() {
            videoPreviewContainer.innerHTML = '';
            videoInput.value = '';
            videoData = null;
        }

        videoDrop.addEventListener('click', () => videoInput.click());
        videoDrop.addEventListener('drop', e => {
            e.preventDefault();
            if (videoData) { alert("Une seule vidéo autorisée"); return; }
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('video/')) createVideoPreview(file);
        });
        videoInput.addEventListener('change', () => {
            if (!videoData && videoInput.files[0]) createVideoPreview(videoInput.files[0]);
        });

        /* =========================
           SOUMISSION
        ========================= */
        form.addEventListener('submit', async e => {
            e.preventDefault();

            if (imagesData.length === 0) {
                alert("Au moins une image est obligatoire");
                return;
            }

            const secteur = form.secteur.value;
            if (!secteur) {
                alert("Veuillez sélectionner un secteur");
                return;
            }

            // Collect common data
            const payload = {
                titre: form.titre.value,
                description: form.description.value,
                secteur: secteur,
                images: imagesData,
                video: videoData,
                donnees_specifiques: {}
            };

            // Collect specific data
            const activeContainer = document.getElementById('fields-' + secteur);
            if (activeContainer) {
                const inputs = activeContainer.querySelectorAll('.spec-field');
                inputs.forEach(input => {
                    const key = input.dataset.name;
                    let val = input.value;
                    if (input.type === 'checkbox') {
                        val = input.checked;
                    }
                    if (val) {
                        payload.donnees_specifiques[key] = val;
                    }
                });
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = "Publication...";

            try {
                const res = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();

                if (json.success) {
                    result.innerHTML = `<div class="text-success">${json.message}</div>`;
                    form.reset();
                    imagesData = [];
                    videoData = null;
                    imagePreviewContainer.innerHTML = '';
                    videoPreviewContainer.innerHTML = '';
                    // Reset fields visibility
                    sectorIds.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.style.display = 'none';
                    });
                    // Set title label back to default if needed, or trigger change event to reset
                    titreLabel.innerHTML = 'Titre <span class="text-danger">*</span>';
                } else {
                    result.innerHTML = `<div class="text-danger">${json.message}</div>`;
                }
            } catch (err) {
                console.error(err);
                result.innerHTML = `<div class="text-danger">Erreur réseau</div>`;
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Publier';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
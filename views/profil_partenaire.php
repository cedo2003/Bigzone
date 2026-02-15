<?php
session_start();
require_once 'controls/bd.php';

/* =====================================
   TRAITEMENT AJAX (UPDATE PARTENAIRE)
===================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
    header('Content-Type: application/json');

    $code_unique = $_SESSION['code_unique'] ?? null;
    if (!$code_unique) {
        echo json_encode(["success" => false, "message" => "Non autorisé"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Données invalides"]);
        exit;
    }

    $required = ['entreprise','secteur','responsable','telephone','email','description'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(["success"=>false,"message"=>"Tous les champs sont obligatoires"]);
            exit;
        }
    }

    // Gestion logo
    $logoPath = null;
    if (!empty($data['logo'])) {
        $logoData = $data['logo'];
        if (preg_match('/^data:image\/(\w+);base64,/', $logoData, $type)) {
            $logoData = substr($logoData, strpos($logoData, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg','jpeg','png','gif'])) {
                echo json_encode(["success"=>false,"message"=>"Format de logo non supporté"]);
                exit;
            }
            $logoData = base64_decode($logoData);
            if (!file_exists("uploads/logos")) mkdir("uploads/logos",0777,true);
            $logoPath = "uploads/logos/{$code_unique}.".$type;
            file_put_contents($logoPath, $logoData);
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE partenaire SET
                entreprise=?, secteur=?, responsable=?, telephone=?, email=?, description=?,
                logo=COALESCE(?, logo)
            WHERE code_unique=?
        ");

        $stmt->execute([
            htmlspecialchars(trim($data['entreprise'])),
            htmlspecialchars(trim($data['secteur'])),
            htmlspecialchars(trim($data['responsable'])),
            htmlspecialchars(trim($data['telephone'])),
            htmlspecialchars(trim($data['email'])),
            htmlspecialchars(trim($data['description'])),
            $logoPath,
            $code_unique
        ]);

        echo json_encode(["success"=>true,"message"=>"Profil mis à jour avec succès"]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["success"=>false,"message"=>"Erreur serveur"]);
        exit;
    }
}

/* =====================================
   AFFICHAGE PROFIL PARTENAIRE
===================================== */
$code_unique = $_SESSION['code_unique'] ?? null;
if (!$code_unique) {
    header("Location: login");
    exit;
}

$stmt = $pdo->prepare("SELECT entreprise, secteur, responsable, telephone, email, description, logo FROM partenaire WHERE code_unique = ?");
$stmt->execute([$code_unique]);
$partenaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$partenaire) {
    header("Location: home");
    exit;
}

// Logo par défaut si aucun logo
$logoUrl = $partenaire['logo'] && file_exists($partenaire['logo']) ? $partenaire['logo'] : "https://via.placeholder.com/120?text=Logo";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Profil Partenaire – BigZone</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body{background:#f4f6fb;font-family:'Inter',sans-serif;}
.navbar{box-shadow:0 2px 10px rgba(0,0,0,0.08);background:white !important;}
.navbar-brand{font-weight:700;font-size:1.5rem;}
.profile-header{background:linear-gradient(135deg,#4f46e5,#6366f1);border-radius:20px;padding:2rem;color:#fff;margin-bottom:2rem;display:flex;align-items:center;gap:1rem;}
.company-avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #fff;}
.profile-card{background:#fff;border-radius:20px;box-shadow:0 15px 40px rgba(0,0,0,.12);padding:2rem;}
.form-label{font-weight:500;color:#374151;}
.form-control, textarea, select{border-radius:12px;padding:.7rem .9rem;}
input[disabled], textarea[disabled], select[disabled]{background:#f8f9fa;cursor:not-allowed;}
.edit-btn{cursor:pointer;transition:.2s;}
.edit-btn:hover{transform:scale(1.1);}
.btn-save{background:#4f46e5;border:none;border-radius:50px;padding:.65rem 2.5rem;font-weight:600;}
#logoInput{display:none;}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand text-primary" href="<?= BASE_URL ?>dashboard">BigZone</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a style="color:red; font-weight:bold;" class="nav-link" href="<?= BASE_URL ?>home">Fermer le Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>annonces">Publier une annonce</a></li>
          <li class="nav-item"><a class="nav-link active" href="<?= BASE_URL ?>profil_partenaire">Mon Profil</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <a href="<?= BASE_URL ?>logout" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
        </div>
      </div>
    </div>
</nav>
<br><br>

<div class="container my-5">
    <div class="profile-header">
        <img src="<?= $logoUrl ?>" id="logoPreview" class="company-avatar">
        <div>
            <h4 class="fw-bold mb-0"><?= htmlspecialchars($partenaire['entreprise']) ?></h4>
            <small class="opacity-75"><?= htmlspecialchars($partenaire['secteur']) ?></small>
        </div>
        <i class="bi bi-pencil-square fs-3 ms-auto edit-btn" id="editBtn"></i>
    </div>

    <div class="profile-card">
        <form id="partnerForm">
            <div class="mb-3 text-center d-none" id="logoInputZone">
                <label class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-upload me-2"></i>Changer le logo
                    <input type="file" id="logoInput" accept="image/*">
                </label>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Entreprise</label>
                    <input class="form-control" name="entreprise" value="<?= htmlspecialchars($partenaire['entreprise']) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secteur</label>
                    <select class="form-select" name="secteur" disabled>
                        <option value="E-commerce" <?= $partenaire['secteur']=='E-commerce'?'selected':'' ?>>E-commerce</option>
                        <option value="Immobiliers" <?= $partenaire['secteur']=='Immobiliers'?'selected':'' ?>>Immobiliers</option>
                        <option value="Propulseur d’Activité" <?= $partenaire['secteur']=='Propulseur d’Activité'?'selected':'' ?>>Propulseur d’Activité</option>
                        <option value="Événementielle" <?= $partenaire['secteur']=='Événementielle'?'selected':'' ?>>Événementielle</option>
                        <option value="Autre" <?= $partenaire['secteur']=='Autre'?'selected':'' ?>>Autre</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Responsable</label>
                    <input class="form-control" name="responsable" value="<?= htmlspecialchars($partenaire['responsable']) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input class="form-control" name="telephone" value="<?= htmlspecialchars($partenaire['telephone']) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($partenaire['email']) ?>" disabled>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="4" name="description" disabled><?= htmlspecialchars($partenaire['description']) ?></textarea>
                </div>
            </div>

            <div class="text-center mt-4 d-none" id="saveZone">
                <button type="submit" class="btn btn-save btn-lg">
                    <i class="bi bi-check-circle me-2"></i>Enregistrer
                </button>
            </div>
        </form>
        <div id="result" class="text-center mt-3"></div>
    </div>
</div>

<script>
const editBtn = document.getElementById('editBtn');
const fields = document.querySelectorAll('#partnerForm input, #partnerForm textarea, #partnerForm select');
const saveZone = document.getElementById('saveZone');
const logoInputZone = document.getElementById('logoInputZone');
const logoInput = document.getElementById('logoInput');
const logoPreview = document.getElementById('logoPreview');
const result = document.getElementById('result');

editBtn.addEventListener('click', ()=>{
    fields.forEach(f=>f.disabled=false);
    saveZone.classList.remove('d-none');
    logoInputZone.classList.remove('d-none');
});

// Aperçu du logo
logoInput.addEventListener('change', ()=>{
    const file = logoInput.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = e => logoPreview.src = e.target.result;
        reader.readAsDataURL(file);
    }
});

document.getElementById('partnerForm').addEventListener('submit', async e=>{
    e.preventDefault();
    const data = {};
    fields.forEach(f=>data[f.name]=f.value);

    if(logoInput.files[0]){
        const file = logoInput.files[0];
        const reader = new FileReader();
        reader.onload = async e=>{
            data.logo = e.target.result;
            await sendData(data);
        };
        reader.readAsDataURL(file);
    }else{
        data.logo = null;
        await sendData(data);
    }
});

async function sendData(data){
    const btn = saveZone.querySelector('button');
    btn.disabled=true;
    btn.innerHTML="Enregistrement...";
    try{
        const res = await fetch('',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify(data)
        });
        const json = await res.json();
        if(json.success){
            result.innerHTML='<span class="text-success">'+json.message+'</span>';
            fields.forEach(f=>f.disabled=true);
            saveZone.classList.add('d-none');
            logoInputZone.classList.add('d-none');
        }else{
            result.innerHTML='<span class="text-danger">'+json.message+'</span>';
        }
    }catch(e){
        result.innerHTML='<span class="text-danger">Erreur réseau</span>';
    }finally{
        btn.disabled=false;
        btn.innerHTML='<i class="bi bi-check-circle me-2"></i>Enregistrer';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

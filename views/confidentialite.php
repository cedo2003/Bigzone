<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Politique de Confidentialité - BigZone</title>
  <style>
    :root {
      --primary: #1e3a8a;
      --secondary: #0f172a;
      --accent: #38bdf8;
      --bg: #f8fafc;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #64748b;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background: linear-gradient(180deg, #e0f2fe 0%, var(--bg) 40%);
      color: var(--text);
    }

    header {
      background: linear-gradient(135deg, var(--primary), #2563eb);
      color: white;
      padding: 3rem 1rem 3rem;
      text-align: center;
      position: relative;
    }

    header h1 {
      margin: 0;
      font-size: 2.1rem;
      letter-spacing: 0.4px;
    }

    header p {
      margin-top: 0.75rem;
      opacity: 0.9;
    }

    .close-btn {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: rgba(255,255,255,0.15);
      border: none;
      color: white;
      font-size: 1.2rem;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .close-btn:hover {
      background: rgba(255,255,255,0.3);
    }

    .container {
      max-width: 900px;
      margin: 1.5rem auto 3rem;
      padding: 0 1rem;
    }

    .card {
      background: var(--card);
      border-radius: 1.25rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      padding: 2rem 2rem 1.5rem;
      margin-bottom: 1.5rem;
    }

    .section {
      margin-bottom: 1.75rem;
    }

    .section h2 {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 1.2rem;
      margin: 0 0 0.75rem;
      color: var(--primary);
      cursor: pointer;
    }

    .badge {
      background: var(--accent);
      color: #082f49;
      font-weight: 600;
      border-radius: 999px;
      padding: 0.15rem 0.6rem;
      font-size: 0.75rem;
    }

    .section-content {
      line-height: 1.6;
      color: var(--secondary);
      margin-left: 0.25rem;
    }

    .section-content ul {
      margin: 0.5rem 0 0.75rem 1.2rem;
    }

    .section-content li {
      margin: 0.35rem 0;
    }

    .muted {
      color: var(--muted);
      font-size: 0.95rem;
    }

    .footer {
      text-align: center;
      color: var(--muted);
      font-size: 0.9rem;
      margin-top: 2rem;
    }

    .toggle-icon {
      margin-left: auto;
      transition: transform 0.25s ease;
      font-weight: bold;
      color: var(--muted);
    }

    .collapsed .toggle-icon {
      transform: rotate(-90deg);
    }

    .collapsed .section-content {
      display: none;
    }
  </style>
</head>
<body>
  <header>
    <button class="close-btn" onclick="closePage()">✕</button>
    <h1>Politique de Confidentialité</h1>
    <p>Plateforme BigZone — Protection de vos données</p>
  </header>

  <div class="container">
    <div class="card">

      <div class="section">
        <h2 onclick="toggleSection(this)">1. Collecte des données <span class="badge">BigZone</span><span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p><strong>BigZone</strong> collecte uniquement les informations nécessaires au bon fonctionnement de la plateforme :</p>
          <ul>
            <li>Nom ou pseudonyme</li>
            <li>Numéro de téléphone et adresse email</li>
            <li>Informations de compte et de connexion</li>
            <li>Données liées aux annonces publiées</li>
            <li>Données de navigation (vues)</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">2. Utilisation des données<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>Les données collectées servent à :</p>
          <ul>
            <li>Créer et gérer les comptes utilisateurs</li>
            <li>Permettre la mise en relation entre utilisateurs</li>
            <li>Afficher les statistiques de visibilité</li>
            <li>Améliorer les services de BigZone</li>
            <li>Assurer la sécurité de la plateforme</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">3. Partage des données<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p><strong>BigZone</strong> ne vend ni ne loue les données personnelles.</p>
          <p>Les données peuvent être partagées uniquement :</p>
          <ul>
            <li>Avec des prestataires techniques et de paiement sous contrat (ex. hébergement sécurisé)</li>
            <li>Si la loi l’exige</li>
          </ul>
          <p class="muted">Transferts internationaux : les données peuvent être hébergées dans d’autres pays, mais toujours avec des mesures de protection adéquates.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">4. Sécurité des données<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p><strong>BigZone</strong> met en œuvre des mesures techniques et organisationnelles pour protéger les données contre :</p>
          <ul>
            <li>L’accès non autorisé</li>
            <li>La perte ou la corruption</li>
            <li>La modification ou la divulgation abusive</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">5. Durée de conservation<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>Les données sont conservées :</p>
          <ul>
            <li>Tant que le compte est actif</li>
            <li>Ou selon les obligations légales applicables</li>
          </ul>
          <p>L’utilisateur peut demander la suppression de son compte et de ses données.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">6. Droits des utilisateurs<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>Chaque utilisateur dispose du droit :</p>
          <ul>
            <li>D’accès à ses données</li>
            <li>De rectification</li>
            <li>De suppression</li>
            <li>D’opposition à certains traitements</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">7. Cookies<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p><strong>BigZone</strong> utilise des cookies pour :</p>
          <ul>
            <li>Améliorer l’expérience utilisateur</li>
            <li>Mesurer la visibilité des annonces</li>
            <li>Assurer le bon fonctionnement du site</li>
          </ul>
          <p class="muted">L’utilisateur peut gérer les cookies via son navigateur.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">8. Modifications de la politique<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p><strong>BigZone</strong> peut modifier cette politique à tout moment.</p>
          <p>Les utilisateurs seront informés des mises à jour importantes via notification sur la plateforme ou email.</p>
        </div>
      </div>

    </div>

    <div class="footer">
      © <span id="year"></span> BigZone — Tous droits réservés
    </div>
  </div>

  <script>
    function toggleSection(titleEl) {
      const section = titleEl.parentElement;
      section.classList.toggle("collapsed");
    }

    function closePage() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = "/";
      }
    }

    document.getElementById("year").textContent = new Date().getFullYear();

    // Collapse all except first
    const sections = document.querySelectorAll(".section");
    sections.forEach((sec, index) => {
      if (index !== 0) sec.classList.add("collapsed");
    });
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Conditions d’Utilisation - BigZone</title>
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
      background: linear-gradient(135deg, var(--primary), #2563eb);
      color: white;
      padding: 3rem 1rem 4rem;
      text-align: center;
      position: relative;
    }

    header h1 {
      margin: 0;
      font-size: 2.2rem;
      letter-spacing: 0.5px;
    }

    header p {
      margin-top: 0.75rem;
      opacity: 0.9;
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

    .accept-box {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px dashed #cbd5f5;
    }

    .accept-box input {
      width: 18px;
      height: 18px;
      cursor: pointer;
    }

    .accept-btn {
      margin-left: auto;
      padding: 0.6rem 1.2rem;
      border-radius: 999px;
      border: none;
      background: linear-gradient(135deg, #38bdf8, #0ea5e9);
      color: #082f49;
      font-weight: 600;
      cursor: not-allowed;
      opacity: 0.6;
      transition: all 0.25s ease;
    }

    .accept-btn.enabled {
      cursor: pointer;
      opacity: 1;
      box-shadow: 0 6px 15px rgba(56,189,248,0.35);
    }

    .accept-btn.enabled:hover {
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
  <header>
    <button class="close-btn" onclick="closePage()">✕</button>
    <h1>Conditions d’Utilisation</h1>
    <p>Plateforme BigZone — Version en vigueur</p>
  </header>

  <div class="container">
    <div class="card" id="termsCard">

      <div class="section">
        <h2 onclick="toggleSection(this)">1. Présentation de la plateforme <span class="badge">BigZone</span><span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p><strong>BigZone</strong> est une plateforme digitale multi-services permettant la publication et la consultation d’annonces dans plusieurs catégories :</p>
          <ul>
            <li>e-commerce</li>
            <li>immobilier (location et vente)</li>
            <li>événements</li>
            <li>services et activités professionnelles</li>
          </ul>
          <p>BigZone agit exclusivement comme plateforme de mise en relation et n’est pas partie aux transactions entre utilisateurs.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">2. Acceptation des conditions<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>En créant un compte ou en utilisant BigZone, l’utilisateur reconnaît avoir lu, compris et accepté les présentes Conditions d’Utilisation.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">3. Comptes utilisateurs<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <ul>
            <li>L’utilisateur s’engage à fournir des informations exactes, complètes et à jour.</li>
            <li>Chaque utilisateur est responsable de la sécurité de son compte, identifiant et mot de passe.</li>
            <li>BigZone se réserve le droit de suspendre ou supprimer un compte en cas de non-respect des règles.</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">4. Publication des annonces<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>L’annonceur est seul responsable :</p>
          <ul>
            <li>du contenu de ses annonces (textes, images, prix, informations)</li>
            <li>de la légalité des produits, services ou biens proposés</li>
          </ul>
          <p>Sont strictement interdits :</p>
          <ul>
            <li>contenus frauduleux, mensongers ou trompeurs</li>
            <li>produits ou services illégaux</li>
            <li>contenus offensants, haineux ou contraires aux lois en vigueur</li>
          </ul>
          <p>BigZone peut modifier, masquer ou supprimer toute annonce ne respectant pas ces règles.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">5. Visibilité et statistiques<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>BigZone fournit des indicateurs de visibilité (nombre de vues).</p>
          <p class="muted">Ces statistiques sont à titre informatif uniquement et ne garantissent aucun résultat commercial.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">6. Paiements et abonnements<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <ul>
            <li>Certains services de BigZone seront payants (publication d’annonces, abonnements, option corporate sur demande).</li>
            <li>Les paiements sont non remboursables, sauf mention contraire.</li>
            <li>BigZone peut modifier ses tarifs à tout moment, avec information préalable.</li>
            <li>Les paiements sont sécurisés via des prestataires agréés et BigZone n’intervient pas dans les transactions financières entre utilisateurs.</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">7. Responsabilité<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <ul>
            <li>BigZone ne garantit pas la réussite des ventes ou locations.</li>
            <li>BigZone n’est pas responsable des litiges entre utilisateurs.</li>
            <li>BigZone n’intervient pas dans les transactions, paiements ou livraisons.</li>
          </ul>
          <p>Les utilisateurs doivent faire preuve de vigilance et respecter les droits des tiers (propriété intellectuelle, marques).</p>
          <p class="muted">Limitation de responsabilité : BigZone ne peut être tenu responsable des dommages directs ou indirects, pertes financières ou préjudices découlant de l’utilisation de la plateforme.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">8. Suspension et résiliation<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>BigZone peut suspendre ou résilier un compte sans préavis en cas :</p>
          <ul>
            <li>d’activité frauduleuse</li>
            <li>de violation des présentes conditions</li>
            <li>de comportement nuisible à la plateforme ou aux utilisateurs</li>
          </ul>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">9. Résolution des litiges<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>Tout litige entre un utilisateur et BigZone sera résolu à l’amiable, ou à défaut, devant les tribunaux compétents selon la loi du pays d’exploitation.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">10. Modifications des conditions<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>BigZone peut modifier les présentes conditions à tout moment.</p>
          <p>Les utilisateurs seront informés via notification sur la plateforme ou par email.</p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">11. Contact<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>Pour toute question, réclamation ou demande d’assistance :</p>
          <p><strong>contact@bigzone.com</strong></p>
        </div>
      </div>

      <div class="section">
        <h2 onclick="toggleSection(this)">12. Droit applicable<span class="toggle-icon">▾</span></h2>
        <div class="section-content">
          <p>Ces conditions sont régies par les lois du pays d’exploitation de BigZone.</p>
        </div>
      </div>

      <div class="accept-box">
        <input type="checkbox" id="acceptCheck" onchange="toggleAccept()" />
        <label for="acceptCheck">J’ai lu et j’accepte les Conditions d’Utilisation</label>
        <button id="acceptBtn" class="accept-btn">Continuer</button>
      </div>

    </div>

    <div class="footer">
      © <span id="year"></span> BigZone — Tous droits réservés
    </div>
  </div>

  <script>
    function closePage() {
      // Personnalise ici : fermer modal, retour page précédente ou redirection
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = "/";
      }
    }


    function toggleSection(titleEl) {
      const section = titleEl.parentElement;
      section.classList.toggle("collapsed");
    }

    function toggleAccept() {
      const check = document.getElementById("acceptCheck");
      const btn = document.getElementById("acceptBtn");
      if (check.checked) {
        btn.classList.add("enabled");
        btn.disabled = false;
        btn.style.cursor = "pointer";
      } else {
        btn.classList.remove("enabled");
        btn.disabled = true;
        btn.style.cursor = "not-allowed";
      }
    }

    document.getElementById("acceptBtn").addEventListener("click", () => {
      if (!document.getElementById("acceptCheck").checked) return;
      alert("Merci ! Vous avez accepté les Conditions d’Utilisation de BigZone.");
      // Exemple de redirection :
      // window.location.href = "/dashboard.html";
    });

    document.getElementById("year").textContent = new Date().getFullYear();

    // Collapse all except first
    const sections = document.querySelectorAll(".section");
    sections.forEach((sec, index) => {
      if (index !== 0) sec.classList.add("collapsed");
    });
  </script>
</body>
</html>

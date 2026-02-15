<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BigZone - Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { font-family: "Inter", sans-serif; background: #f8fafc; }
    .navbar { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
    .modal-success .modal-header { background: #d4edda; color: #155724; }
    .modal-error .modal-header   { background: #f8d7da; color: #721c24; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <main class="container my-5 pt-5">
    <h1 class="text-center mb-5" data-aos="fade-down">Inscription à BigZone</h1>
    <p class="text-center mb-5 col-lg-8 mx-auto">
      Créez un compte gratuit pour consulter, ou choisissez un abonnement pour publier dans nos zones.
    </p>

    <div class="row justify-content-center">
      <div class="col-lg-6" data-aos="fade-up">
        <div class="bg-white p-5 rounded shadow">
          <form id="registerForm">
            <div class="mb-3">
              <label class="form-label">Nom complet</label>
              <input type="text" name="full_name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Mot de passe</label>
              <input type="password" name="password" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Confirmez votre mot de passe</label>
              <input type="password" name="password_confirm" class="form-control" required />
            </div>
            <div class="form-check mb-3">
              <input type="checkbox" class="form-check-input" id="accept" required />
              <label class="form-check-label" for="accept">
                J'accepte les <a href="privacy.html">CGU et Politique de Confidentialité</a>
              </label>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-5 w-100" id="submitBtn">
              S'inscrire
            </button>
          </form>
          <p class="text-center mt-3">
            Ou inscrivez-vous avec <a href="#">Google</a> / <a href="#">Apple</a>
          </p>
          <p class="text-center">
            Déjà un compte ? <a href="<?= BASE_URL ?>login">Se connecter</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal de résultat -->
  <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resultModalLabel">Inscription</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center" id="modalBody">
          <!-- Le contenu sera injecté ici -->
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Fermer</button>
          <a href="<?= BASE_URL ?>login" class="btn btn-primary px-4" id="loginLink" style="display:none;">Se connecter</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true });

  const form = document.getElementById('registerForm');
  const formContainer = form.parentElement; // la div bg-white
  const modal = new bootstrap.Modal(document.getElementById('resultModal'));
  const modalBody = document.getElementById('modalBody');
  const loginLink = document.getElementById('loginLink');

  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inscription...';

    const formData = new FormData(form);

    try {
      const response = await fetch('<?= BASE_URL ?>models/register.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      submitBtn.disabled = false;
      submitBtn.innerHTML = 'S\'inscrire';

      const modalEl = document.getElementById('resultModal');

      if (result.success) {
        // Succès : on cache le formulaire entier
        formContainer.style.display = 'none';

        // Optionnel : ajouter un titre de confirmation au-dessus
        const confirmationTitle = document.createElement('h3');
        confirmationTitle.className = 'text-center text-success mb-4';
        confirmationTitle.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Inscription réussie !';
        formContainer.parentElement.insertBefore(confirmationTitle, formContainer.nextSibling);

        // Vider tout le contenu principal sauf le modal
        document.querySelector('main').innerHTML = `
          <div class="text-center my-5">
            <h2 class="text-success">Inscription terminée !</h2>
            <p>Votre compte est créé. Cliquez sur le bouton ci-dessous pour vous connecter.</p>
            <a href="<?= BASE_URL ?>login" class="btn btn-primary btn-lg px-5 py-3 mt-4">
              <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter maintenant
            </a>
          </div>
        `;

        modalEl.classList.remove('modal-error');
        modalEl.classList.add('modal-success');
        document.querySelector('.modal-header').style.background = '#d4edda';
        document.querySelector('.modal-header').style.color = '#155724';
        
        modalBody.innerHTML = `
          <div class="py-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-success">Compte créé avec succès !</h4>
            <p class="text-muted">Bienvenue chez BigZone ! Vous pouvez maintenant vous connecter.</p>
          </div>
        `;
        loginLink.style.display = 'inline-block';
      } else {
        // Erreur : on garde le formulaire visible
        modalEl.classList.remove('modal-success');
        modalEl.classList.add('modal-error');
        document.querySelector('.modal-header').style.background = '#f8d7da';
        document.querySelector('.modal-header').style.color = '#721c24';

        modalBody.innerHTML = `
          <div class="py-4">
            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-danger">Erreur</h4>
            <p>${result.message || 'Une erreur est survenue.'}</p>
          </div>
        `;
        loginLink.style.display = 'none';
      }

      modal.show();

    } catch (error) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = 'S\'inscrire';
      modalBody.innerHTML = `
        <div class="py-4">
          <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
          <h4 class="mt-3 text-danger">Erreur réseau</h4>
          <p>Impossible de contacter le serveur. Veuillez réessayer.</p>
        </div>
      `;
      modal.show();
    }
  });
</script>
</body>
</html>
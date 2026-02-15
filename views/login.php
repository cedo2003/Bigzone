<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BigZone - Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
  rel="stylesheet"
/>

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
    <h1 class="text-center mb-5" data-aos="fade-down">Connexion à BigZone</h1>
    <p class="text-center mb-5 col-lg-6 mx-auto">
      Connectez-vous pour accéder à votre dashboard, gérer vos annonces, ou consulter votre historique.
    </p>

    <div class="row justify-content-center">
      <div class="col-lg-5" data-aos="fade-up">
        <div class="bg-white p-5 rounded shadow" id="loginContainer">
          <form id="loginForm">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required />
            </div>
           <div class="mb-3">
  <label class="form-label">Mot de passe</label>

  <div class="input-group">
    <input
      type="password"
      name="password"
      id="password"
      class="form-control"
      required
    />

    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
      <i class="bi bi-eye"></i>
    </span>
  </div>
</div>

            <div class="form-check mb-3">
              <input type="checkbox" class="form-check-input" id="remember" name="remember" />
              <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-5 w-100" id="submitBtn">
              Se connecter
            </button>
          </form>
          <p class="text-center mt-3">
            Ou connectez-vous avec <a href="#">Google</a> / <a href="#">Apple</a>
          </p>
          <p class="text-center">
            Mot de passe oublié ? <a href="reset_password.php">Réinitialiser</a>
          </p>
          <p class="text-center">
            Pas de compte ? <a href="<?= BASE_URL ?>inscription">S'inscrire</a>
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
          <h5 class="modal-title" id="resultModalLabel">Connexion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center" id="modalBody"></div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>

    const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");
  const icon = togglePassword.querySelector("i");

  togglePassword.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password";

    passwordInput.type = isPassword ? "text" : "password";
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
  });

  
    AOS.init({ duration: 800, once: true });

    const form = document.getElementById('loginForm');
    const loginContainer = document.getElementById('loginContainer');
    const modal = new bootstrap.Modal(document.getElementById('resultModal'));
    const modalBody = document.getElementById('modalBody');

    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Connexion...';

      const formData = new FormData(form);

      try {
        const response = await fetch('<?= BASE_URL ?>models/login.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Se connecter';

        const modalEl = document.getElementById('resultModal');

        if (result.success) {
        
          setTimeout(() => {
            window.location.href = '<?= BASE_URL ?>home';
          }, 50);
        } else {
          // Erreur : formulaire reste visible
          modalEl.classList.remove('modal-success');
          modalEl.classList.add('modal-error');
          document.querySelector('.modal-header').style.background = '#f8d7da';
          document.querySelector('.modal-header').style.color = '#721c24';

          modalBody.innerHTML = `
            <div class="py-4">
              <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
              <h4 class="mt-3 text-danger">Erreur</h4>
              <p>${result.message || 'Identifiants incorrects.'}</p>
            </div>
          `;
        }

        modal.show();

      } catch (error) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Se connecter';
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
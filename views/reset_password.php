<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BigZone - Récupération de Mot de Passe</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: "Inter", sans-serif;
        background: #f8fafc;
      }
      .navbar {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }
    </style>
  </head>
  <body>
    <!-- Navigation (copier de index.html) -->
    <?php include 'header.php'; ?>
    <main class="container my-5 pt-5">
      <h1 class="text-center mb-5" data-aos="fade-down">
        Récupération de Mot de Passe
      </h1>
      <p class="text-center mb-5 col-lg-6 mx-auto">
        Entrez votre email pour recevoir un lien de réinitialisation. Le
        processus est sécurisé et rapide. Si vous n'avez pas reçu l'email,
        vérifiez vos spams.
      </p>

      <div class="row justify-content-center">
        <div class="col-lg-5" data-aos="fade-up">
          <div class="bg-white p-5 rounded shadow">
            <form>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" required />
              </div>
              <button
                type="submit"
                class="btn btn-primary rounded-pill px-5 w-100"
              >
                Envoyer le lien
              </button>
            </form>
            <p class="text-center mt-3">
              Retour à <a href="login.php">Connexion</a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer (coller le code footer ici) -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800, once: true });
    </script>
  </body>
</html>

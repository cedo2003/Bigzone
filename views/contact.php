<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BigZone - Contact</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/style.css" />
  </head>
  <body>
    <?php include 'header.php'; ?>

    <main class="container my-5 pt-5">
  <h1 class="text-center mb-5 fw-bold" data-aos="fade-down">Contactez-nous</h1>

  <div class="row g-5">
    <!-- Formulaire -->
    <div class="col-lg-6" data-aos="fade-right">
      <div class="bg-white p-5 rounded shadow-lg">
        <h3 class="mb-4 fw-semibold">Envoyez-nous un message</h3>
        <form id="contactForm">
          <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" class="form-control" placeholder="Votre nom" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="exemple@email.com" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Sujet</label>
            <input type="text" class="form-control" placeholder="Sujet de votre message" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Message</label>
            <textarea class="form-control" rows="6" placeholder="Écrivez votre message ici..." required></textarea>
          </div>
          <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
            Envoyer
          </button>
        </form>
      </div>
    </div>

    <!-- FAQ -->
    <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
      <div class="bg-white p-5 rounded shadow-lg">
        <h3 class="mb-4 fw-semibold">Questions fréquentes</h3>

        <div class="faq-item mb-3">
          <h5 class="bg-light p-3 rounded cursor-pointer">
            Est-ce gratuit de consulter les annonces ?
          </h5>
          <div class="faq-answer px-3 py-2" style="display:none;">
            <p>Oui ! La consultation est 100% gratuite et illimitée pour tous les visiteurs.</p>
          </div>
        </div>

        <div class="faq-item mb-3">
          <h5 class="bg-light p-3 rounded cursor-pointer">
            Comment publier une annonce ?
          </h5>
          <div class="faq-answer px-3 py-2" style="display:none;">
            <p>Il suffit de créer un compte et de souscrire à un abonnement mensuel (simple ou premium).</p>
          </div>
        </div>

        <div class="faq-item mb-3">
          <h5 class="bg-light p-3 rounded cursor-pointer">
            Prenez-vous des commissions sur les ventes ?
          </h5>
          <div class="faq-answer px-3 py-2" style="display:none;">
            <p>Non. BigZone ne prélève aucune commission. Les transactions se font directement entre vendeur et acheteur.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- JS pour les FAQ -->
<script>
  document.querySelectorAll('.faq-item h5').forEach((el) => {
    el.addEventListener('click', () => {
      const answer = el.nextElementSibling;
      answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
    });
  });
</script>

<!-- AOS animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true });
</script>


    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800, once: true });
    </script>
  </body>
</html>

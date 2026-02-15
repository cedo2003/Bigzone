<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BigZone - Accueil</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <?php include 'header.php'; ?>
    <section class="hero text-center" data-aos="fade-down">
      <div class="container">
        <h1 class="display-4 fw-bold mb-4">Bienvenue sur BigZone</h1>
        <p class="lead mb-5 col-lg-8 mx-auto">
          La plateforme multi-services simple et sans distraction pour acheter,
          vendre, louer et découvrir des événements.
        </p>
        <div class="col-lg-7 mx-auto">
          <div class="input-group input-group-lg shadow">
            <input
              type="text"
              class="form-control border-0"
              placeholder="Recherchez un produit, service, bien immobilier ou événement..."
            />
            <button class="btn btn-primary px-5" type="button">
              Rechercher
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Zones -->
    <section class="py-5">
      <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">Nos zones</h2>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3" data-aos="zoom-in">
            <div class="card zone-card h-100 shadow-sm border-0">
              <img
                src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                class="card-img-top"
                alt="E-commerce"
              />
              <div class="card-body text-center">
                <h5 class="card-title">E-commerce</h5>
                <p class="card-text">
                  Vendez ou achetez des produits physiques et digitaux en toute
                  simplicité
                </p>
                <a href="#" class="btn btn-primary rounded-pill">Découvrir</a>
              </div>
            </div>
          </div>
          <div
            class="col-md-6 col-lg-3"
            data-aos="zoom-in"
            data-aos-delay="200"
          >
            <div class="card zone-card h-100 shadow-sm border-0">
              <img
                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1973&q=80"
                class="card-img-top"
                alt="Immobilier"
              />
              <div class="card-body text-center">
                <h5 class="card-title">Immobilière & Parcelles</h5>
                <p class="card-text">
                  Maisons, appartements, parcelles et salles à louer ou à vendre
                </p>
                <a href="#" class="btn btn-primary rounded-pill">Découvrir</a>
              </div>
            </div>
          </div>
          <div
            class="col-md-6 col-lg-3"
            data-aos="zoom-in"
            data-aos-delay="400"
          >
            <div class="card zone-card h-100 shadow-sm border-0">
              <img
                src="https://images.unsplash.com/photo-1573164713714-d95e4361d6a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80"
                class="card-img-top"
                alt="Services"
              />
              <div class="card-body text-center">
                <h5 class="card-title">Propulseur d’Activité</h5>
                <p class="card-text">
                  Trouvez artisans, freelances, entreprises et services
                  professionnels
                </p>
                <a href="#" class="btn btn-primary rounded-pill">Découvrir</a>
              </div>
            </div>
          </div>
          <div
            class="col-md-6 col-lg-3"
            data-aos="zoom-in"
            data-aos-delay="600"
          >
            <div class="card zone-card h-100 shadow-sm border-0">
              <img
                src="https://images.unsplash.com/photo-1501281668755-7b8cbede6d4d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80"
                class="card-img-top"
                alt="Événements"
              />
              <div class="card-body text-center">
                <h5 class="card-title">Événementielle</h5>
                <p class="card-text">
                  Concerts, formations, foires et tous les événements près de
                  chez vous
                </p>
                <a href="#" class="btn btn-primary rounded-pill">Découvrir</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pourquoi BigZone -->
    <section class="py-5 bg-white">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6" data-aos="fade-right">
            <h2 class="fw-bold mb-4">Pourquoi choisir BigZone ?</h2>
            <p class="text-muted mb-4">
              Une seule plateforme, plusieurs opportunités. Simple, rapide et
              sans commission.
            </p>
            <div class="row g-3">
              <div class="col-6">
                <div class="p-3 shadow-sm rounded bg-light">
                  ✅ Sans commission
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 shadow-sm rounded bg-light">
                  🚀 Publication rapide
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 shadow-sm rounded bg-light">
                  🔒 Sécurisé & fiable
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 shadow-sm rounded bg-light">
                  🌍 Accessible partout
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 text-center" data-aos="fade-left">
            <img
              src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
              class="img-fluid rounded-4 shadow"
              alt="Plateforme digitale"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Statistiques -->
    <section class="py-5 text-center text-white" style="background: #2563eb">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-3" data-aos="zoom-in">
            <h2 class="fw-bold">+10k</h2>
            <p class="mb-0">Utilisateurs</p>
          </div>
          <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
            <h2 class="fw-bold">+5k</h2>
            <p class="mb-0">Annonces actives</p>
          </div>
          <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
            <h2 class="fw-bold">4 Zones</h2>
            <p class="mb-0">Multi-services</p>
          </div>
          <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
            <h2 class="fw-bold">0%</h2>
            <p class="mb-0">Commission</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Comment ça marche -->
    <section class="py-5">
      <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">Comment ça marche ?</h2>
        <div class="row g-4">
          <div class="col-md-4" data-aos="fade-up">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
              <h1 class="text-primary fw-bold">1</h1>
              <h5 class="mt-3">Créez un compte</h5>
              <p class="text-muted">Inscription rapide et gratuite</p>
            </div>
          </div>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
              <h1 class="text-primary fw-bold">2</h1>
              <h5 class="mt-3">Publiez ou recherchez</h5>
              <p class="text-muted">Produits, services, biens ou événements</p>
            </div>
          </div>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
              <h1 class="text-primary fw-bold">3</h1>
              <h5 class="mt-3">Contactez directement</h5>
              <p class="text-muted">Sans intermédiaire</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white text-center">
      <div class="container">
        <h2 class="fw-bold mb-3" data-aos="fade-down">
          Prêt à rejoindre BigZone ?
        </h2>
        <p class="mb-4" data-aos="fade-up">
          Publiez vos annonces et développez votre activité dès aujourd’hui.
        </p>
        <a
          href="<?= BASE_URL ?>inscription"
          class="btn btn-light btn-lg rounded-pill px-5"
          data-aos="zoom-in"
        >
          Commencer maintenant
        </a>
      </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800, once: true });
    </script>
  </body>
</html>

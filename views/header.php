<nav class="navbar navbar-expand-lg bg-white fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
      <img src="<?= BASE_URL ?>assets/images/bigzone.png" alt="BigZone" height="50" />
      <span>BigZone</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link <?= ($page==='accueil')?'active':'' ?> fw-medium" href="<?= BASE_URL ?>">Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($page==='contact')?'active':'' ?> fw-medium" href="<?= BASE_URL ?>contact">Contact</a>
        </li>
        <li class="nav-item ms-3">
          <a class="btn btn-primary rounded-pill px-4" href="<?= BASE_URL ?>inscription">S'inscrire</a>
        </li>
        <li class="nav-item ms-3">
          <a class="btn btn-secondary rounded-pill px-4" href="<?= BASE_URL ?>login">Se Connecter</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

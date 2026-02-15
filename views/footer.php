<footer style="text-color: #f8fafc;" class="bg-dark text-light pt-5">
    <div class="container">
        <div class="row g-4">

        <!-- Logo & description -->
        <div class="col-md-4">
            <h4 class="fw-bold text-primary"><img src="assets/images/bigzonee.png" alt="BigZone" height="200" /></h4>
            <p>
            BigZone est une plateforme digitale multi-services qui facilite
            la mise en relation sans commission.
            </p>
        </div>

        <!-- Liens rapides -->
        <div class="col-md-2">
            <h6 class="fw-semibold mb-3">Plateforme</h6>
            <ul class="list-unstyled">
            <li class="mb-2"><a href="<?= BASE_URL ?>" class="text-decoration-none">Accueil</a></li>
            <li class="mb-2"><a href="<?= BASE_URL ?>contact" class="text-decoration-none">Contact</a></li>
            <li class="mb-2"><a href="<?= BASE_URL ?>inscription" class="text-decoration-none">Inscription</a></li>
            </ul>
        </div>

        <!-- Zones -->
        <div class="col-md-3">
            <h6 class="fw-semibold mb-3">Nos zones</h6>
            <ul class="list-unstyled">
            <li class="mb-2"><span>E-commerce</span></li>
            <li class="mb-2"><span>Immobilier</span></li>
            <li class="mb-2"><span>Services</span></li>
            <li class="mb-2"><span>Événementiel</span></li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div class="col-md-3">
            <h6 class="fw-semibold mb-3">Restez informé</h6>
            <p class=" small">
            Recevez les nouvelles annonces et opportunités.
            </p>
            <div class="input-group">
            <input type="email" class="form-control" placeholder="Votre email">
            <button class="btn btn-primary">OK</button>
            </div>
        </div>

        </div>

        <hr class="border-secondary my-4">

        <!-- Bas footer -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pb-3">
        <p class="mb-2 mb-md-0 ">
            © 2025 BigZone. Tous droits réservés.
        </p>
        <div class="d-flex gap-3">
            <a href="<?= BASE_URL ?>conditions" class=" text-decoration-none">Conditions</a>
            <a href="<?= BASE_URL ?>confidentialite" class=" text-decoration-none">Confidentialité</a>
        </div>
        </div>
    </div>
    </footer>
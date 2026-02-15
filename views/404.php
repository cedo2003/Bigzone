<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Page introuvable</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f8fafc;
    }
    .error-container {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      flex-direction: column;
      padding: 20px;
    }
    .error-container h1 {
      font-size: 10rem;
      font-weight: 700;
      color: #2563eb;
    }
    .error-container h3 {
      font-size: 2rem;
      margin-bottom: 20px;
    }
    .error-container p {
      font-size: 1.1rem;
      color: #6b7280;
      margin-bottom: 30px;
    }
    .error-container img {
      max-width: 400px;
      width: 100%;
      margin-top: 30px;
    }
    .btn-home {
      padding: 12px 30px;
      font-size: 1.1rem;
      border-radius: 50px;
    }
    @media (max-width: 576px) {
      .error-container h1 {
        font-size: 6rem;
      }
      .error-container h3 {
        font-size: 1.5rem;
      }
      .error-container img {
        max-width: 300px;
      }
    }
  </style>
</head>
<body>

  <div class="error-container">
    <h1 data-aos="fade-down">404</h1>
    <h3 data-aos="fade-up" data-aos-delay="100">Oups ! Page introuvable</h3>
    <p data-aos="fade-up" data-aos-delay="200">
      La page que vous cherchez n’existe pas ou a été déplacée.
    </p>
    <a href="<?= BASE_URL ?>" class="btn btn-primary btn-home" data-aos="fade-up" data-aos-delay="300">Retour à l'accueil</a>
    <img src="https://images.unsplash.com/photo-1581091870621-2e3adbb3c9c8?auto=format&fit=crop&w=800&q=80" 
         alt="Page 404" data-aos="zoom-in" data-aos-delay="400">
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 800, once: true });
  </script>
</body>
</html>

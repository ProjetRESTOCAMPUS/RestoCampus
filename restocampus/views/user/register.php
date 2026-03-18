<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Créer un compte - RestoCampus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4 col-md-5">
    <h3 class="text-center mb-4 text-primary">Créer un compte</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=registerUser">
      <div class="mb-3">
        <label class="form-label">Nom d’utilisateur</label>
        <input type="text" class="form-control" name="login" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" class="form-control" name="password" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
    </form>

    <p class="text-center mt-3">
      <a href="index.php?action=login" class="text-decoration-none">🔑 Déjà un compte ? Se connecter</a>
    </p>
  </div>
</div>

</body>
</html>

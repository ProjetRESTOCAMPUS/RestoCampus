<?php include('views/layout/header.php'); ?>

<div class="container mt-4">
    <h2>Importer des étudiants via un fichier CSV</h2>

    <p>Format attendu : <strong>login;nom;prenom;motdepasse</strong></p>

    <form action="index.php?action=importCSV" method="POST" enctype="multipart/form-data">
        <input type="file" name="csv" accept=".csv" class="form-control mb-3" required>
        <button class="btn btn-primary">Importer</button>
    </form>

    <p class="mt-3"><a href="index.php?action=listerUsers">Retour</a></p>
</div>

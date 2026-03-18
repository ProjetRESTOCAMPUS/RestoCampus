<?php include('views/layout/header.php'); ?>

<div class="container py-4">

    <h2 class="mb-4 text-primary"><i class="bi bi-bar-chart"></i> Statistiques</h2>

    <!-- Cartes statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h4><?= $stats['totalUsers'] ?></h4>
                <p class="text-muted">Utilisateurs</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h4><?= $stats['etudiants'] ?></h4>
                <p class="text-muted">Étudiants</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h4><?= $stats['admins'] ?></h4>
                <p class="text-muted">Admins</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h4><?= $stats['articles'] ?></h4>
                <p class="text-muted">Articles</p>
            </div>
        </div>
    </div>

    <!-- Graphique : Articles les plus réservés -->
    <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Articles les plus réservés</h5>
        <canvas id="chartArticles"></canvas>
    </div>

    <!-- Graphique : Réservations par jour -->
    <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Evolution des réservations</h5>
        <canvas id="chartJour"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ------- GRAPH 1 : Articles -------
    const articleLabels = <?= json_encode(array_column($totaux, "nom")) ?>;
    const articleData = <?= json_encode(array_column($totaux, "total")) ?>;

    new Chart(document.getElementById('chartArticles'), {
        type: 'bar',
        data: {
            labels: articleLabels,
            datasets: [{
                label: "Nombre de réservations",
                data: articleData
            }]
        }
    });

    // ------- GRAPH 2 : Réservations par jour -------
    const jourLabels = <?= json_encode(array_column($courbe, "jour")) ?>;
    const jourData = <?= json_encode(array_column($courbe, "total")) ?>;

    new Chart(document.getElementById('chartJour'), {
        type: 'line',
        data: {
            labels: jourLabels,
            datasets: [{
                label: "Réservations",
                data: jourData
            }]
        }
    });
</script>

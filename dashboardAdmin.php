<?php
session_start();
include 'pdo.php';  
  include 'header_admin.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require_once("connexion.php");
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Nombre total de jeux vendus
$query1 = $connexion->query("SELECT SUM(quantite) AS total_jeux_vendus FROM details_commande");
$result1 = $query1->fetch();
$totalJeuxVendus = $result1['total_jeux_vendus'] ?? 0;

// 2. Chiffre d'affaires total (tous les jeux)
$query2 = $connexion->query("SELECT SUM(montant) AS total_ca FROM commande");
$result2 = $query2->fetch();
$totalCA = $result2['total_ca'] ?? 0;

// 3. Chiffre d'affaires des jeux vendus (calculé avec `details_commande` et `jeux`)
$query3 = $connexion->query("
    SELECT SUM(dc.quantite * j.prix) AS ca_jeux_vendus
    FROM details_commande dc
    JOIN jeux j ON dc.id_jeu = j.id
");
$result3 = $query3->fetch();
$totalCAJeuxVendus = $result3['ca_jeux_vendus'] ?? 0;

// 4. Nombre de jeux par catégorie (depuis la table `jeux`)
$query4 = $connexion->query("
    SELECT categorie, COUNT(*) AS total_jeux
    FROM jeux
    GROUP BY categorie
");
$jeuxParCategorie = $query4->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        .card { background: #f5f5f5; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        h2 { color: #333; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dashboardAdmin.css">
</head>
<body>

<h1>Dashboard Administrateur</h1>

<div class="dashboard">

    <div class="card">
        <h2>🎮 Nombre total de jeux vendus :</h2>
        <p><?= $totalJeuxVendus ?></p>
        <canvas id="jeuxVendusChart" width="250" height="250"></canvas>
    </div>

    <div class="card">
        <h2>💰 Chiffre d'affaires total :</h2>
        <p><?= number_format($totalCA / 10, 2, ',', ' ') ?> €</p>
        <canvas id="caPie" width="250" height="250"></canvas>
    </div>

    <div class="card">
        <h2>📊 Nombre de jeux par catégorie :</h2>
        <canvas id="categorieChart" width="250" height="250"></canvas>
    </div>

    <!-- Nouveau graphique pour comparer CA total et CA jeux vendus -->
    <div class="card">
        <h2>💰 Comparaison Chiffre d'Affaires Total vs Chiffre d'Affaires des Jeux Vendus :</h2>
        <canvas id="caComparisonChart" width="250" height="250"></canvas>
    </div>

</div>

<script>
    // Graphique des jeux par catégorie
    const labels = <?= json_encode(array_column($jeuxParCategorie, 'categorie')) ?>;
    const data = <?= json_encode(array_column($jeuxParCategorie, 'total_jeux')) ?>;

    const ctxCategorie = document.getElementById('categorieChart').getContext('2d');
    const categorieChart = new Chart(ctxCategorie, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre de jeux',
                data: data,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Jeux par catégorie' }
            },
            scales: {
                y: { beginAtZero: true, precision: 0 }
            }
        }
    });

    // Graphique du chiffre d'affaires total vs objectif
    const caTotal = <?= json_encode((float)$totalCA) ?>;
    const objectif = 10000; // Changer l'objectif si nécessaire

    const ctxCA = document.getElementById('caPie').getContext('2d');
    const caPie = new Chart(ctxCA, {
        type: 'doughnut',
        data: {
            labels: ['Chiffre d\'affaires', 'Reste pour objectif'],
            datasets: [{
                data: [caTotal, Math.max(0, objectif - caTotal)],
                backgroundColor: ['#4CAF50', '#E0E0E0']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Chiffre d\'affaires total vs objectif' }
            }
        }
    });

    // Graphique du chiffre d'affaires des jeux vendus vs objectif
    const caVendus = <?= json_encode((float)$totalCAJeuxVendus) ?>;
    const objectifJeux = 5000; // Objectif pour les jeux vendus, adapte-le selon ton besoin

    const ctxJeux = document.getElementById('jeuxVendusChart').getContext('2d');
    const jeuxChart = new Chart(ctxJeux, {
        type: 'doughnut',
        data: {
            labels: ['Jeux vendus', 'Reste pour objectif'],
            datasets: [{
                data: [caVendus, Math.max(0, objectifJeux - caVendus)],
                backgroundColor: ['#FF6384', '#E0E0E0']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Jeux vendus vs objectif' },
                legend: { position: 'bottom' }
            }
        }
    });

    // Nouveau graphique pour comparer le chiffre d'affaires total et le chiffre d'affaires des jeux vendus
    const caVendusTotal = <?= json_encode((float)$totalCAJeuxVendus) ?>;
    const caTotalComparaison = <?= json_encode((float)$totalCA) ?>;

    const ctxComparison = document.getElementById('caComparisonChart').getContext('2d');
    const caComparisonChart = new Chart(ctxComparison, {
        type: 'bar',
        data: {
            labels: ['Chiffre d\'Affaires Total', 'Chiffre d\'Affaires des Jeux Vendus'],
            datasets: [{
                label: 'Chiffre d\'Affaires (€)',
                data: [caTotalComparaison, caVendusTotal],
                backgroundColor: ['#4CAF50', '#FF6384'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Comparaison Chiffre d\'Affaires Total vs Jeux Vendus' }
            },
            scales: {
                y: { beginAtZero: true, precision: 0 }
            }
        }
    });
</script>

</body>
</html>

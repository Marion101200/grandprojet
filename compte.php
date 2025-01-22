<?php include 'header.php';
    include 'pdo.php';
    ?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['nom'])) {
    header('Location: login.php');
    exit();
}

$nom = $_SESSION['nom']; // Récupération du nom de l'utilisateur connecté

try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur existe
    $stmt = $connexion->prepare("SELECT id, nom, prenom, email FROM clients WHERE nom = :nom");
    $stmt->bindParam(':nom', $nom);
    $stmt->execute();
    $clients = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clients) {
        echo "Utilisateur introuvable.";
        exit();
    }

    // Gestion du formulaire de mise à jour
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newClientsNom = htmlspecialchars(trim($_POST['nom']));
        $newEmail = htmlspecialchars(trim($_POST['email']));
        $newMdp = !empty($_POST['mdp']) ? password_hash($_POST['mdp'], PASSWORD_DEFAULT) : null;

        try {
            // Préparation de la requête SQL
            $sql = "UPDATE clients SET nom = :nom, email = :email";
            if ($newMdp) {
                $sql .= ", mdp = :mdp";
            }
            $sql .= " WHERE id = :id";

            $stmt = $connexion->prepare($sql);
            $stmt->bindParam(':nom', $newClientsNom);
            $stmt->bindParam(':email', $newEmail);
            if ($newMdp) {
                $stmt->bindParam(':mdp', $newMdp);
            }
            $stmt->bindParam(':id', $clients['id']);
            $stmt->execute();

            // Mettre à jour la session avec les nouvelles informations
            $_SESSION['nom'] = $newClientsNom;

            echo "<p style='color: green;font-size: 50px; text-align: center;'>Informations mises à jour avec succès.</p>";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}

$connexion = null; // Fermeture de la connexion
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Compte</title>
    <link rel="stylesheet" href="compte.css">
</head>

<body style="background-color: rgb(179, 238, 248)">
    <div class="profile-container">
        <div class="profil-changement">
            <img src="img/DALL·E 2024-12-05 10.32.17 - A stylish and playful cartoon-style logo for a profile change feature on a website. The logo features a cheerful cartoon character's face inside a cir.webp" alt="logo-changement-profil">
        </div>
            <div class="title">
                <h2 class="title_compte" style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bx-game'></i> &nbsp;Mon Profil &nbsp; <i class='bx bx-game'></i></h2>
            
            <form action="compte.php" method="POST">
                <label for="nom">Nom d'utilisateur : </label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($clients['nom']); ?>" required>
                <br>
                <label for="email">Email : </label>
                <input type="email" name="email" value="<?= htmlspecialchars($clients['email']); ?>" required>
                <br>
                <label for="password">Nouveau mot de passe :</label>
                <input type="password" id="mdp" name="mdp">
                <br>
                <input type="submit" value="Mettre à jour">
            </form>
        </div>
        
    </div>

</body>

</html>
<?php
include 'pdo.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['nom'])) {
    header('Location: login.php');
    exit();
}

$nom = $_SESSION['nom'];

try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $connexion->prepare("SELECT id, nom, prenom, email FROM clients WHERE nom = :nom");
    $stmt->bindParam(':nom', $nom);
    $stmt->execute();
    $clients = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clients) {
        echo "Utilisateur introuvable.";
        exit();
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete_account'])) {
            try {
                $connexion->prepare("DELETE FROM commande WHERE id_clients = :id")->execute(['id' => $clients['id']]);
                $connexion->prepare("DELETE FROM user_favorites WHERE clients_id = :id")->execute(['id' => $clients['id']]);

                $stmt = $connexion->prepare("DELETE FROM clients WHERE id = :id");
                $stmt->bindParam(':id', $clients['id']);
                $stmt->execute();

                // Détruit la session
                session_unset();
                session_destroy();

                echo "<p style='color: red; font-size: 30px; text-align: center;'>Votre compte a été supprimé.</p>";

                // Redirige vers la page d'accueil après suppression
                header("Location: accueil.php");
                exit();
            } catch (PDOException $e) {
                echo "Erreur lors de la suppression : " . $e->getMessage();
            }
        }

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


            $_SESSION['nom'] = $newClientsNom;

            echo "<p style='color: green;font-size: 50px; text-align: center;'>Informations mises à jour avec succès.</p>";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}

$connexion = null;
include 'header.php';
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
            <h2 class="title_compte" style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px">
                <i class='bx bx-game'></i> &nbsp;Mon Profil &nbsp; <i class='bx bx-game'></i>
            </h2>

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
            </form>
            <form action="compte.php" method="POST" onsubmit="return confirmDelete();">
                <input type="hidden" name="delete_account" value="1">
                <button type="submit" class="delete_compte">Supprimer mon compte</button>
            </form>

            <input type="submit" value="Mettre à jour">
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible !");
        }
    </script>

</body>

</html>
<?php
include 'footer.php';
?>
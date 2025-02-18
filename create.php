<?php
session_start();
if (!isset($_SESSION['administrateur'])) {
    header("Location: admin.php");
    exit();
}

$table = ''; // Initialiser $table pour éviter les erreurs d'utilisation de variable non définie

try {
    require_once("connexion.php");
    if (isset($_GET['table'])) {
        $table = $_GET['table'];

        $connexion = getConnexion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $columns = $_POST['columns'];

            // Ajouter automatiquement la date d'ajout si elle n'est pas présente
            $columns['date_ajout'] = date('Y-m-d H:i:s'); // Date et heure actuelle

            // Vérifier si une image a été téléchargée
            if (isset($_FILES['columns']['name']['images']) && $_FILES['columns']['name']['images'] !== '') {
                $imageFile = $_FILES['columns']['tmp_name']['images'];
                $imageName = basename($_FILES['columns']['name']['images']);
                $targetDir = 'img/';
                $targetFile = $targetDir . $imageName;

                // Vérifier si le fichier est une image
                $check = getimagesize($imageFile);
                if ($check === false) {
                    throw new Exception("Le fichier téléchargé n'est pas une image.");
                }

                // Déplacer le fichier vers le dossier img
                if (!move_uploaded_file($imageFile, $targetFile)) {
                    throw new Exception("Erreur lors du téléchargement de l'image.");
                }

                // Enregistrer le chemin de l'image dans la base de données
                $columns['images'] = $targetFile; // Chemin complet vers l'image
            }

            // Préparer les colonnes et les valeurs pour l'insertion
            $columns_names = implode(", ", array_keys($columns));  // Noms des colonnes
            $placeholders = ":" . implode(", :", array_keys($columns)); // Placeholders pour les valeurs

            $stmt = $connexion->prepare("INSERT INTO $table ($columns_names) VALUES ($placeholders)");

            // Associer les valeurs aux placeholders
            foreach ($columns as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            // Exécuter l'insertion
            $stmt->execute();

            // Redirection après insertion
            header("Location: admin_dashbord.php");
            exit();
        }

        // Simuler une ligne vide pour le formulaire (nouvelle insertion)
        $row = array_fill_keys($connexion->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_COLUMN), '');
    } else {
        throw new Exception("Table non spécifiée dans la requête.");
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Nouvel enregistrement</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="edit.css">
</head>
<body>
<h2>Nouvel enregistrement dans la table "<?php echo htmlspecialchars($table); ?>"</h2>
    <form action="" method="post" enctype="multipart/form-data"> <!-- Modifié ici -->
        <?php if (!empty($row)): ?>
            <?php foreach ($row as $column => $value): ?>
                <?php if ($column !== "password" && $column !== "date_ajout"): ?> <!-- Ne pas afficher la colonne "date_ajout" dans le formulaire -->
                    <label for="<?php echo htmlspecialchars($column); ?>"><?php echo htmlspecialchars($column); ?>:</label>
                    <input type="text" name="columns[<?php echo htmlspecialchars($column); ?>]" value="<?php echo htmlspecialchars($value); ?>" id="<?php echo htmlspecialchars($column); ?>">
                    <br>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Impossible de récupérer les colonnes pour cette table.</p>
        <?php endif; ?>

        <!-- Input pour l'image -->
        <label for="images">Choisissez une image:</label>
        <input type="file" name="columns[images]" id="images" accept="image/*"> <!-- Accept uniquement les images -->
        <br>

        <input type="submit" value="Enregistrer les nouveaux éléments">
    </form>
</body>
</html>

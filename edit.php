<?php
session_start();
if (!isset($_SESSION['administrateur'])) {
    header("Location: admin.php");
    exit();
}

try {
    require_once("connexion.php");
    if (isset($_GET['table']) && isset($_GET['id'])) {
        $table = $_GET['table'];
        $id = $_GET['id'];

        $connexion = getConnexion();
        $stmt = $connexion->prepare("SELECT * FROM $table WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo "Aucun enregistrement trouvé avec cet ID.";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $columns = $_POST['columns'];

            // Vérifier si une nouvelle image a été téléchargée
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

                // Mettre à jour le chemin de l'image dans les colonnes
                $columns['images'] = $targetFile; // Chemin complet vers l'image
            }

            // Préparer le champ à mettre à jour, mais uniquement si la valeur a changé
            foreach ($columns as $column => $value) {
                if ($row[$column] !== $value && $column !== 'password') {
                    $stmt = $connexion->prepare("UPDATE $table SET $column = :value WHERE id = :id");
                    $stmt->bindParam(':value', $value);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                }
            }

            // Redirection après modification
            header("Location: admin_dashbord.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Modifier l'enregistrement</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="edit.css">
</head>
<body>
    <h2>Modifier l'enregistrement dans la table "<?php echo htmlspecialchars($table); ?>"</h2>
    <form action="" method="post" enctype="multipart/form-data"> <!-- Ajout de l'attribut enctype -->
        <?php foreach ($row as $column => $value): ?>
            <?php if ($column !== "password"): ?>
                <label for="<?php echo htmlspecialchars($column); ?>"><?php echo htmlspecialchars($column); ?>:</label>
                <input type="text" name="columns[<?php echo htmlspecialchars($column); ?>]" value="<?php echo htmlspecialchars($value); ?>" id="<?php echo htmlspecialchars($column); ?>">
                <br>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- Champ pour télécharger l'image -->
        <label for="images">Choisissez une nouvelle image:</label>
        <input type="file" name="columns[images]" id="images" accept="image/*"> <!-- Accept uniquement les images -->
        <br>

        <input type="submit" value="Enregistrer les modifications">
    </form>
</body>
</html>


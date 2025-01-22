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
        $stmt = $connexion->prepare("DELETE FROM $table WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_dashbord.php?table=$table&success=1");
        exit();
    } else {
        echo "Table ou ID manquant.";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

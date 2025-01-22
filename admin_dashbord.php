<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
  <title>Admin dashbord</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="dashbord.css">
</head>
<body>
<?php
  include 'header_admin.php';
  ?>
<?php
if(!isset($_SESSION['administrateur'])){
    header("Location: admin.php");
    exit();
}
try{
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $connexion->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);

}catch(PDOException $e){
    echo "Erreur : " . $e->getMessage();

}

?>
<div class="title_admin">
    <h1 style="font-size: 50px;"> <i class='bx bx-game'></i> &nbsp;Bienvenue Admin ! &nbsp;<i class='bx bx-game'></i></h1>
    </div>
<div class="sous_titre">
    <h2 style="font-size: 40px; color:blue">Listes des tables dans la base de données</h2>
</div>
<?php
try{
    require_once("connexion.php");
    if($tables){
        foreach($tables as $table){
            $tableName = $table[0];
            echo "<div class='title_table'>";
            echo"<h3>Table " . $tableName ."</h3>";
            echo "</div>";
            $connexion = getConnexion();
            $stmt = $connexion->prepare("SELECT * FROM $tableName");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows) {
                // La classe 'custom-table' est ajoutée à la balise <table>
                echo "<table class='custom-table'>";  // Début du tableau avec la classe 'custom-table'
                
                echo "<tr>";
                foreach(array_keys($rows[0]) as $column) {
                    echo "<th>$column</th>";  // Les en-têtes de colonnes seront centrées grâce à la classe custom-table
                }
                echo "<th>Actions</th>";
                echo "</tr>";
            
                foreach ($rows as $row) {
                    $idColumn = array_keys($rows[0])[0];
            
                    if ($row[$idColumn] == 0) {
                        continue;
                    }
                    echo "<tr>";
                    foreach ($row as $data) {
                        // Vérification si la cellule contient une image
                        if (is_string($data) && preg_match('/\.(jpg|jpeg|png|gif|webp|avif|jfif)$/i', $data)) {
                            echo "<td><img src='$data' alt='Image' style='max-width:200px; max-height:200px;'></td>";
                        } else {
                        echo "<td>$data</td>";  // Les cellules des données seront centrées grâce à la classe custom-table
                    }
                }
                    echo "<td>";
                    echo "<div class='action'>";
                    echo "<a href='edit.php?table=$tableName&id={$row[$idColumn]}' class='edit'>Modifier</a>";
                    echo "<a href='delete.php?table=$tableName&id={$row[$idColumn]}' onclick='return confirm(\"Etes-vous sur de vouloir supprimer les éléments suivant?\");' class='delete'>Supprimer</a>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";  // Fin du tableau
            }else{
                echo "<p>Aucune donnée disponible dans la table $tableName.</p>";
            }
            echo "<p><a href= 'create.php?table=$tableName' class='add'>Ajouter un nouvel enregistrement</a></p>";
        }
    }else{
        echo "<p>Aucune table trouvée dans la base de donnée.</p>";
    }
}catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
</body>
</html>

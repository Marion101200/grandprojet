<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="jeux.css">
  <title>Panier</title>
</head>

<body>
  <?php
  session_start();
  require_once("connexion.php");
  $connexion = getConnexion();
  $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  date_default_timezone_set('Europe/Paris');

  // --- SUPPRESSION d'un avis ---
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_avis_id'])) {
    $avisId = (int)$_POST['supprimer_avis_id'];

    $stmt = $connexion->prepare("DELETE FROM avis WHERE id = :id AND nom = :nom");
    $stmt->execute([
      'id' => $avisId,
      'nom' => $_SESSION['nom'] ?? ''
    ]);

    if (isset($_POST['id'])) {
      header("Location: fiche_jeux.php?id=" . intval($_POST['id']));
      exit;
    } else {
      header("Location: historique_commandes.php");
      exit;
    }
  }

  // --- MODIFICATION d'un avis ---
  elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_avis_id'], $_POST['commentaire'], $_POST['note'])) {
    $avisId = (int)$_POST['modifier_avis_id'];
    $commentaire = trim($_POST['commentaire']);
    $note = intval($_POST['note']);
    $date_modif = date("Y-m-d H:i:s");

    if ($note < 1 || $note > 5) {
      echo "La note doit être comprise entre 1 et 5.";
      exit;
    }

    // Vérifier que l'avis appartient bien à l'utilisateur
    $stmtCheck = $connexion->prepare("SELECT * FROM avis WHERE id = :id AND nom = :nom");
    $stmtCheck->execute([
      'id' => $avisId,
      'nom' => $_SESSION['nom'] ?? ''
    ]);
    $avis = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$avis) {
      echo "Avis introuvable ou accès non autorisé.";
      exit;
    }

    // Mise à jour
    $stmt = $connexion->prepare("UPDATE avis SET commentaire = :commentaire, note = :note, date_ajout = :date_modif WHERE id = :id");
    $stmt->execute([
      'commentaire' => $commentaire,
      'note' => $note,
      'date_modif' => $date_modif,
      'id' => $avisId
    ]);

    if (isset($_POST['id'])) {
      header("Location: fiche_jeux.php?id=" . intval($_POST['id']));
      exit;
    } else {
      header("Location: historique_commandes.php");
      exit;
    }
  }

  // --- AJOUT d'un avis ---
  elseif (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['jeux_titre'], $_POST['commentaire'], $_POST['note'])
  ) {

    if (!isset($_SESSION['nom'])) {
      echo "Vous devez être connecté pour laisser un avis.";
      exit;
    }

    $jeux_titre = $_POST['jeux_titre'];
    $nom = $_SESSION['nom'];
    $commentaire = trim($_POST['commentaire']);
    $note = intval($_POST['note']);
    $date_ajout = date("Y-m-d H:i:s");

    if ($note < 1 || $note > 5) {
      echo "La note doit être comprise entre 1 et 5.";
      exit;
    }

    $stmt = $connexion->prepare("INSERT INTO avis (jeux_titre, nom, commentaire, note, date_ajout) 
                                 VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$jeux_titre, $nom, $commentaire, $note, $date_ajout]);

    header("Location: fiche_jeux.php?id=" . $_POST['id']);
    exit;
  }
  ?>
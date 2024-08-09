<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'ID du contrat est passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_contrat = intval($_GET['id']); // Assurez-vous que l'ID est un entier

    // Récupération des données du contrat
    $sql = "SELECT * FROM contrat WHERE id_contrat = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("i", $id_contrat);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "Contrat non trouvé.";
        exit;
    }

    // Gestion de la soumission du formulaire
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupération des données du formulaire
        $id_contrat = $_POST['id_contrat'];
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $responsable = $_POST['responsable'];
        $affectation = $_POST['affectation'];
        $projet = $_POST['projet'];
        $statut = $_POST['statut'];

        // Mise à jour des données du contrat
        $sql_update = "UPDATE contrat SET date_debut = ?, date_fin = ?, responsable = ?, affectation = ?, projet = ?, statut = ? WHERE id_contrat = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            die("Erreur de préparation de la requête de mise à jour : " . $conn->error);
        }

        $stmt_update->bind_param("ssssssi", $date_debut, $date_fin, $responsable, $affectation, $projet, $statut, $id_contrat);

        if ($stmt_update->execute()) {
            header("Location: index.view.php"); // Redirection après mise à jour
            exit;
        } else {
            echo "Erreur de mise à jour : " . $stmt_update->error;
        }

        $stmt_update->close();
    }
} else {
    echo "ID de contrat non spécifié.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Contrat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Modifier le Contrat</h1>
<form action="edit_contrat.php?id=<?php echo $id_contrat; ?>" method="post">
    <input type="hidden" name="id_contrat" value="<?php echo htmlspecialchars($row['id_contrat']); ?>">

    <div class="form-group">
        <label for="date_debut">Date Début</label>
        <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($row['date_debut']); ?>" required>
    </div>

    <div class="form-group">
        <label for="date_fin">Date Fin</label>
        <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($row['date_fin']); ?>" required>
    </div>

    <div class="form-group">
        <label for="responsable">Responsable</label>
        <input type="text" id="responsable" name="responsable" value="<?php echo htmlspecialchars($row['responsable']); ?>" required>
    </div>

    <div class="form-group">
        <label for="affectation">Affectation</label>
        <input type="text" id="affectation" name="affectation" value="<?php echo htmlspecialchars($row['affectation']); ?>" required>
    </div>

    <div class="form-group">
        <label for="projet">Projet</label>
        <input type="text" id="projet" name="projet" value="<?php echo htmlspecialchars($row['projet']); ?>" required>
    </div>

    <div class="form-group">
        <label for="statut">Statut</label>
        <input type="text" id="statut" name="statut" value="<?php echo htmlspecialchars($row['statut']); ?>" required>
    </div>

    <div class="form-group">
        <input type="submit" value="Mettre à jour">
    </div>
</form>
</body>
</html>

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
    $sql = "SELECT * FROM contrat WHERE idcontrat = ?";
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
        $idcontrat = $_POST['idcontrat'];
        $idinterimaire = $_POST['idinterimaire'];
        $idsociete = $_POST['idsociete'];
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $departement = $_POST['departement'];

        // Mise à jour des données du contrat
        $sql_update = "UPDATE contrat SET idinterimaire = ?, idsociete = ?, date_debut = ?, date_fin = ?, departement = ? WHERE idcontrat = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            die("Erreur de préparation de la requête de mise à jour : " . $conn->error);
        }

        $stmt_update->bind_param("iisssi", $idinterimaire, $idsociete, $date_debut, $date_fin, $departement, $idcontrat);

        if ($stmt_update->execute()) {
            header("Location: index.view.php"); // Redirection après mise à jour
            exit;
        } else {
            echo "Erreur : " . $stmt_update->error;
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
        <div class="form-group">
            <label for="date_debut">Date Début</label>
            <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($row['date_debut']); ?>" required>
        </div>

        <div class="form-group">
            <label for="date_fin">Date Fin</label>
            <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($row['date_fin']); ?>" required>
        </div>

        <div class="form-group">
            <label for="departement">Département</label>
            <input type="text" id="departement" name="departement" value="<?php echo htmlspecialchars($row['departement']); ?>" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Mettre à jour">
        </div>
    </form>
</body>
</html>

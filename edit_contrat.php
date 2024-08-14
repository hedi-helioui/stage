<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'ID du contrat est passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_contrat = intval($_GET['id']);

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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background-color: #4371c5;
            width: 100%;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        header img {
            margin-left: 20px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }

        h1 {
            color: #ffffff;
            margin: 20px 0;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4371c5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #2c5c9e;
        }
    </style>
</head>
<body>
<header>
    <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
    <h1>Modifier le Contrat</h1>
    <nav>
        <a href="index.view.php">Retour</a>
    </nav>
</header>

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
        <select id="affectation" name="affectation" required>
            <option value="affectation1" <?php echo $row['affectation'] == 'affectation1' ? 'selected' : ''; ?>>Affectation 1</option>
            <option value="affectation2" <?php echo $row['affectation'] == 'affectation2' ? 'selected' : ''; ?>>Affectation 2</option>
            <option value="affectation3" <?php echo $row['affectation'] == 'affectation3' ? 'selected' : ''; ?>>Affectation 3</option>
        </select>
    </div>

    <div class="form-group">
        <label for="projet">Projet</label>
        <input type="text" id="projet" name="projet" value="<?php echo htmlspecialchars($row['projet']); ?>" required>
    </div>

    <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut" required>
            <option value="actif" <?php echo $row['statut'] == 'actif' ? 'selected' : ''; ?>>Actif</option>
            <option value="inactif" <?php echo $row['statut'] == 'inactif' ? 'selected' : ''; ?>>Inactif</option>
            <option value="terminer" <?php echo $row['statut'] == 'terminer' ? 'selected' : ''; ?>>Terminer</option>
        </select>
    </div>

    <div class="form-group">
        <input type="submit" value="Mettre à jour">
    </div>
</form>
</body>
</html>

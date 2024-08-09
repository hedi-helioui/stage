<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'ID de la société est passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_societe = intval($_GET['id']); // Assurez-vous que l'ID est un entier

    // Récupération des données de la société
    $sql = "SELECT * FROM societes WHERE id_societe = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("i", $id_societe);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "Société non trouvée.";
        exit;
    }

    // Gestion de la soumission du formulaire
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupération des données du formulaire
        $id_societe = $_POST['id_societe'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];

        // Mise à jour des données de la société
        $sql_update = "UPDATE societes SET nom = ?, adresse = ?, tel = ?, email = ? WHERE id_societe = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            die("Erreur de préparation de la requête de mise à jour : " . $conn->error);
        }

        $stmt_update->bind_param("ssssi", $nom, $adresse, $tel, $email, $id_societe);

        if ($stmt_update->execute()) {
            header("Location: index.view.php"); // Redirection après mise à jour
            exit;
        } else {
            echo "Erreur de mise à jour : " . $stmt_update->error;
        }

        $stmt_update->close();
    }
} else {
    echo "ID de société non spécifié.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la Société</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Modifier la Société</h1>
<form action="edit_societe.php?id=<?php echo $id_societe; ?>" method="post">
    <input type="hidden" name="id_societe" value="<?php echo htmlspecialchars($row['id_societe']); ?>">

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($row['nom']); ?>" required>
    </div>

    <div class="form-group">
        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($row['adresse']); ?>" required>
    </div>

    <div class="form-group">
        <label for="tel">Téléphone</label>
        <input type="text" id="tel" name="tel" value="<?php echo htmlspecialchars($row['tel']); ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
    </div>

    <div class="form-group">
        <input type="submit" value="Mettre à jour">
    </div>
</form>
</body>
</html>

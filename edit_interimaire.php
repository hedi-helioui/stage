<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank'); // Enlever l'espace dans le nom de la base de données

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérification de l'ID passé en paramètre
if (isset($_GET['id'])) {
    $id_interimaire = intval($_GET['id']);

    // Récupération des détails de l'intérimaire
    $sql = "SELECT * FROM interimaire WHERE id_interimaire = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_interimaire);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("Intérimaire non trouvé.");
    }
} else {
    die("ID d'intérimaire non spécifié.");
}

// Traitement du formulaire de mise à jour
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_interimaire = intval($_POST['id_interimaire']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $tel = trim($_POST['tel']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $competences = trim($_POST['competences']);

    if ($email) {
        // Préparation de la requête de mise à jour
        $sql_update = "UPDATE interimaire SET nom = ?, prenom = ?, tel = ?, email = ?, competences = ? WHERE id_interimaire = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssi", $nom, $prenom, $tel, $email, $competences, $id_interimaire);

        if ($stmt_update->execute()) {
            header("Location: index.view.php"); // Redirection après mise à jour
            exit;
        } else {
            echo "Erreur lors de la mise à jour: " . $stmt_update->error;
        }

        $stmt_update->close();
    } else {
        echo "Adresse e-mail invalide.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'interimaire</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
    <nav>
        <a href="index.view.php">Retour</a>
    </nav>
</header>
<h1>Modifier l'Intérimaire</h1>
<form action="edit_interimaire.php?id=<?php echo $id_interimaire; ?>" method="post">
    <input type="hidden" name="id_interimaire" value="<?php echo $id_interimaire; ?>">
    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($row['nom']); ?>" required>
    </div>
    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($row['prenom']); ?>" required>
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
        <label for="competences">Compétences</label>
        <input type="text" id="competences" name="competences" value="<?php echo htmlspecialchars($row['competences']); ?>" required>
    </div>
    <div class="form-group">
        <input type="submit" value="Mettre à jour">
    </div>
</form>
</body>
</html>

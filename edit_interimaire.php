<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

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
        input[type="email"] {
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
    <h1>Modifier l'intérimaire</h1>
    <nav>
        <a href="index.view.php">Retour</a>
    </nav>
</header>



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

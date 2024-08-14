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
        $nom = trim($_POST['nom']);
        $adresse = trim($_POST['adresse']);
        $tel = trim($_POST['tel']);
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

        if ($email) {
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
        } else {
            echo "Adresse e-mail invalide.";
        }
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
    <h1>Modifier la Société</h1>
    <nav>
        <a href="index.view.php">Retour</a>
    </nav>
</header>
<div class="container">

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
            <input type="submit" value="Mettre à jour" class="btn btn-primary">
        </div>
    </form>
</div>
</body>
</html>

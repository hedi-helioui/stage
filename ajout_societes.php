<?php
// Inclusion du fichier de configuration
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Préparer la connexion à la base de données
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    // Démarrer la transaction
    $conn->begin_transaction();

    try {
        // Requête pour ajouter dans la table Sociétés
        $sql_societe = "INSERT INTO societes (nom, adresse, tel, email) VALUES (?, ?, ?, ?)";

        // Préparer la requête pour les Sociétés
        if ($stmt = $conn->prepare($sql_societe)) {
            $stmt->bind_param("ssss", $_POST['societe_nom'], $_POST['societe_adresse'], $_POST['societe_telephone'], $_POST['societe_email']);

            // Exécuter la requête
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'exécution de la requête Sociétés: " . $stmt->error);
            }

            // Fermer la déclaration préparée
            $stmt->close();

        } else {
            throw new Exception("Erreur lors de la préparation de la requête Sociétés: " . $conn->error);
        }

        // Si tout s'est bien passé, on confirme la transaction
        $conn->commit();

        // Redirection vers liste_societes.php
        header("Location: liste_societes.php");
        exit;

    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $conn->rollback();
        echo "Erreur: " . $e->getMessage();
    }

    // Fermer la connexion
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Société</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4371c5;
            color: white;
            padding: 15px;
            text-align: center;
        }

        header img {
            vertical-align: middle;
        }

        header h1 {
            display: inline;
            margin-left: 20px;
        }

        header nav {
            margin-top: 10px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            background-color: #3561a8;
        }

        header nav a:hover {
            background-color: #2a4d91;
        }

        .section {
            padding: 20px;
            margin: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }

        button[type="submit"] {
            background-color: #4371c5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #3561a8;
        }
    </style>
</head>
<body>
<header>
    <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
    <h1>Ajouter des données</h1>
    <nav>
        <a href="liste_societes.php">Retour</a>
    </nav>
</header>

<div class="section">
    <h2>Ajouter une nouvelle Société</h2>
    <form method="post" action="ajout_societes.php">
        <label for="societe_nom">Nom:</label>
        <input type="text" id="societe_nom" name="societe_nom" required>

        <label for="societe_adresse">Adresse:</label>
        <input type="text" id="societe_adresse" name="societe_adresse" required>

        <label for="societe_telephone">Téléphone:</label>
        <input type="text" id="societe_telephone" name="societe_telephone" required>

        <label for="societe_email">Email:</label>
        <input type="email" id="societe_email" name="societe_email" required>

        <button type="submit">Enregistrer</button>
    </form>
</div>
</body>
</html>

<?php
session_start();
include 'db_connection.php'; // Fichier pour la connexion à la base de données

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = $_POST['matricule'];
    $password = $_POST['mdp'];

    // Connexion à la base de données
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Vérifier la connexion admin
    if ($matricule === '100000' && $password === 'hedy1234') {
        // Authentification réussie pour admin
        $_SESSION['loggedin'] = true;
        $_SESSION['matricule'] = $matricule;
        $_SESSION['email'] = 'heliouihedy@gmail.com'; // Peut être ajusté selon les besoins
        header("Location: admin.php");
        exit;
    }

    // Préparation de la requête SQL pour les connexions normales
    $sql = "SELECT matricule, email, mdp, authorization FROM employer WHERE matricule = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("i", $matricule);
    if (!$stmt->execute()) {
        die("Erreur d'exécution de la requête: " . $stmt->error);
    }

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_matricule, $email, $stored_password, $authorization);
        $stmt->fetch();

        // Comparaison du mot de passe
        if (password_verify($password, $stored_password)) {
            if ($authorization) {
                // Authentification réussie
                $_SESSION['loggedin'] = true;
                $_SESSION['matricule'] = $stored_matricule;
                $_SESSION['email'] = $email;
                header("Location: index.view.php");
                exit;
            } else {
                echo "Votre compte n'est pas autorisé.";
            }
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun compte trouvé avec cette matricule.";
    }

    $stmt->close();
    $conn->close();
}
?>

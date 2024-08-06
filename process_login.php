<?php
session_start();
include 'db_connection.php'; // Fichier pour la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['mdp'];

    // Connexion à la base de données
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Préparation de la requête
    $sql = "SELECT id, email, mdp FROM utilisateur WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $email, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Authentification réussie
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['email'] = $email;
            header("Location: accueil.html");
            exit;
        } else {
            // Mot de passe incorrect
            echo "Invalid password.";
        }
    } else {
        // Email incorrect
        echo "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

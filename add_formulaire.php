<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bh bank";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement du formulaire d'ajout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $competences = $_POST['competences'];

    // Préparez et exécutez la requête SQL
    $stmt = $conn->prepare("INSERT INTO interimaire (nom, prenom, tel, email, competences) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nom, $prenom, $tel, $email, $competences);

    if ($stmt->execute()) {
        echo "Nouvel intérimaire ajouté avec succès";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

// Fermer la connexion
$conn->close();
?>
<?php
// Inclure le fichier de connexion à la base de données
session_start();
include('db_connection.php');

// Afficher les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = [];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecter et valider les données du formulaire
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mdp = trim($_POST['mdp']);
    $tel = trim($_POST['tel']);
    $departement = trim($_POST['departement']);

    // Vérifications des champs
    if (empty($nom)) {
        $errors[] = "Le champ Nom est requis.";
    }
    if (empty($prenom)) {
        $errors[] = "Le champ Prénom est requis.";
    }
    if (empty($email)) {
        $errors[] = "Le champ Email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email fourni n'est pas valide.";
    }
    if (empty($mdp)) {
        $errors[] = "Le champ Mot de Passe est requis.";
    } elseif (strlen($mdp) < 8) {
        $errors[] = "Le mot de passe doit comporter au moins 8 caractères.";
    }
    if (empty($tel)) {
        $errors[] = "Le champ Numéro de Téléphone est requis.";
    } elseif (!preg_match("/^\d{1,8}$/", $tel)) {
        $errors[] = "Le numéro de téléphone doit contenir entre 1 et 8 chiffres.";
    }
    if (empty($departement)) {
        $errors[] = "Le champ Département est requis.";
    }

    // Si des erreurs existent, les afficher et arrêter le script
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        exit;
    }

    // Hashage du mot de passe pour la sécurité
    $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

    // Connexion à la base de données
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Préparer la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mdp, tel, departement) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Erreur de préparation : " . $conn->error);
    }
    $stmt->bind_param("ssssss", $nom, $prenom, $email, $mdp_hash, $tel, $departement);

    // Exécuter la requête
    if ($stmt->execute()) {
        // Rediriger vers une page de confirmation ou de succès
        header("Location: connexion.html");
        exit;
    } else {
        echo "Erreur lors de l'inscription : " . $stmt->error;
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();
} else {
    // Rediriger vers le formulaire si la requête n'est pas POST
    header("Location: register.html");
}
?>

<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'ID du contrat est passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_contrat = intval($_GET['id']); // Assurez-vous que l'ID est un entier

    // Préparer et exécuter la requête de suppression
    $sql = "DELETE FROM contrat WHERE idcontrat = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("i", $id_contrat);

    if ($stmt->execute()) {
        header("Location: index.view.php"); // Redirection après suppression
        exit;
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID de contrat non spécifié.";
    exit;
}

$conn->close();
?>

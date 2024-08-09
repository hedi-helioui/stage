<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérification de l'ID passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_interimaire = intval($_GET['id']); // Assurez-vous que l'ID est un entier

    // Préparation de la requête de suppression
    $sql_delete = "DELETE FROM interimaire WHERE id_interimaire = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_interimaire);

    if ($stmt_delete->execute()) {
        header("Location: index.view.php"); // Redirection vers la page principale après suppression
        exit;
    } else {
        echo "Erreur : " . $stmt_delete->error;
    }

    $stmt_delete->close();
} else {
    echo "ID d'intérimaire non spécifié.";
}

$conn->close();
?>

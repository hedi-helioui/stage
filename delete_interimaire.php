<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérification de l'ID de l'intérimaire passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_interimaire = intval($_GET['id']); // Assurez-vous que l'ID est un entier

    // Étape 1 : Récupérer le matricule de l'intérimaire
    $sql_matricule = "SELECT matricule FROM interimaire WHERE id_interimaire = ?";
    $stmt_matricule = $conn->prepare($sql_matricule);
    $stmt_matricule->bind_param("i", $id_interimaire);
    $stmt_matricule->execute();
    $stmt_matricule->bind_result($matricule);
    $stmt_matricule->fetch();
    $stmt_matricule->close();

    if ($matricule) {
        // Étape 2 : Supprimer les contrats liés au matricule
        $sql_delete_contrat = "DELETE FROM contrat WHERE matricule = ?";
        $stmt_delete_contrat = $conn->prepare($sql_delete_contrat);
        $stmt_delete_contrat->bind_param("s", $matricule);
        $stmt_delete_contrat->execute();
        $stmt_delete_contrat->close();
        // Étape 4 : Supprimer l'intérimaire
        $sql_delete_interimaire = "DELETE FROM interimaire WHERE id_interimaire = ?";
        $stmt_delete_interimaire = $conn->prepare($sql_delete_interimaire);
        $stmt_delete_interimaire->bind_param("i", $id_interimaire);

        if ($stmt_delete_interimaire->execute()) {
            header("Location: index.view.php"); // Redirection après suppression
            exit;
        } else {
            echo "Erreur lors de la suppression de l'intérimaire : " . $stmt_delete_interimaire->error;
        }

        $stmt_delete_interimaire->close();
    } else {
        echo "Matricule introuvable pour l'intérimaire spécifié.";
    }
} else {
    echo "ID d'intérimaire non spécifié.";
}

$conn->close();
?>

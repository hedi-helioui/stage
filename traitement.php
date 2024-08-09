<?php
// Inclusion du fichier de configuration
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Préparer les requêtes SQL pour chaque table
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Démarrer la transaction
    $conn->begin_transaction();

    try {
        $sql_societe = "INSERT INTO societes (nom, adresse, tel, email) VALUES (?, ?, ?, ?)";
        $sql_interimaire = "INSERT INTO interimaire (nom, prenom, tel, email, competences) VALUES (?, ?, ?, ?, ?)";
        $sql_contrat = "INSERT INTO contrat (date_debut, date_fin, responsable, affectation, projet, statut) VALUES (?, ?, ?, ?, ?, ?)";

        // Préparer la requête pour les Sociétés
        if ($stmt = $conn->prepare($sql_societe)) {
            $stmt->bind_param("ssss", $_POST['societe_nom'], $_POST['societe_adresse'], $_POST['societe_telephone'], $_POST['societe_email']);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Erreur lors de la préparation de la requête Sociétés: " . $conn->error);
        }

        // Préparer la requête pour les Intérimaires
        if ($stmt = $conn->prepare($sql_interimaire)) {
            $stmt->bind_param("sssss", $_POST['interimaire_nom'], $_POST['interimaire_prenom'], $_POST['interimaire_telephone'], $_POST['interimaire_email'], $_POST['interimaire_competence']);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Erreur lors de la préparation de la requête Intérimaires: " . $conn->error);
        }

        // Préparer la requête pour les Contrats
        if ($stmt = $conn->prepare($sql_contrat)) {
            $stmt->bind_param("ssssss", $_POST['contrat_date_debut'], $_POST['contrat_date_fin'], $_POST['contrat_responsable'], $_POST['contrat_affectation'], $_POST['contrat_projet'], $_POST['contrat_statut']);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Erreur lors de la préparation de la requête Contrats: " . $conn->error);
        }

        // Si tout s'est bien passé, on confirme la transaction
        $conn->commit();

        // Redirection vers index.view.php
        header("Location: index.view.php");
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

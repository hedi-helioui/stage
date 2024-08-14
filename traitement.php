<?php
// Inclusion du fichier de configuration
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Préparer les requêtes SQL pour chaque table
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Démarrer la transaction
    $conn->begin_transaction();

    try {
        // Vérifier l'unicité du matricule
        $matricule = $_POST['interimaire_matricule'];
        $sql_check_matricule = "SELECT COUNT(*) FROM interimaire WHERE matricule = ?";
        if ($stmt_check = $conn->prepare($sql_check_matricule)) {
            $stmt_check->bind_param("s", $matricule);
            $stmt_check->execute();
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($count > 0) {
                throw new Exception("Le matricule existe déjà.");
            }
        } else {
            throw new Exception("Erreur lors de la préparation de la requête de vérification du matricule: " . $conn->error);
        }

        $sql_societe = "INSERT INTO societes (nom, adresse, tel, email) VALUES (?, ?, ?, ?)";
        $sql_interimaire = "INSERT INTO interimaire (matricule, nom, prenom, tel, email, competences) VALUES (?, ?, ?, ?, ?, ?)";
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
            // Vérifier que le matricule a exactement 6 chiffres
            if (preg_match('/^\d{6}$/', $matricule)) {
                $stmt->bind_param("ssssss", $matricule, $_POST['interimaire_nom'], $_POST['interimaire_prenom'], $_POST['interimaire_telephone'], $_POST['interimaire_email'], $_POST['interimaire_competence']);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Le matricule doit être exactement 6 chiffres.");
            }
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

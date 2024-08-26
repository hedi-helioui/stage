<?php
// Inclusion du fichier de configuration
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Connexion à la base de données
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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

        // Récupérer le nom de la société sélectionnée
        $societe_nom = $_POST['societe_nom'];
        $sql_get_societe_nom = "SELECT nom FROM societes WHERE nom = ?";
        if ($stmt_societe = $conn->prepare($sql_get_societe_nom)) {
            $stmt_societe->bind_param("s", $societe_nom);
            $stmt_societe->execute();
            $stmt_societe->bind_result($nom_societe);
            $stmt_societe->fetch();
            $stmt_societe->close();

            if (!$nom_societe) {
                throw new Exception("Société non trouvée.");
            }
        } else {
            throw new Exception("Erreur lors de la préparation de la requête de récupération du nom de la société: " . $conn->error);
        }

        // Récupérer les dates
        $date_debut = $_POST['contrat_date_debut'];
        $date_fin = $_POST['contrat_date_fin'];
        $date_aujourd_hui = date('Y-m-d');

        // Vérifier que la date de début est égale ou supérieure à la date d'aujourd'hui
        if ($date_debut < $date_aujourd_hui) {
            throw new Exception("La date de début doit être égale ou supérieure à la date d'aujourd'hui.");
        }

        // Vérifier que la date de fin n'est pas inférieure à la date de début
        if ($date_fin < $date_debut) {
            throw new Exception("La date de fin ne peut pas être inférieure à la date de début.");
        }

        // Requête pour ajouter dans la table Intérimaires
        $sql_interimaire = "INSERT INTO interimaire (matricule, nom, prenom, tel, email, competences, nom_societe) VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Préparer la requête pour les Intérimaires
        if ($stmt_interimaire = $conn->prepare($sql_interimaire)) {
            if (preg_match('/^\d{6}$/', $matricule)) {
                $stmt_interimaire->bind_param("sssssss", $matricule, $_POST['interimaire_nom'], $_POST['interimaire_prenom'], $_POST['interimaire_telephone'], $_POST['interimaire_email'], $_POST['interimaire_competence'], $nom_societe);
                $stmt_interimaire->execute();
                $stmt_interimaire->close();
            } else {
                throw new Exception("Le matricule doit être exactement 6 chiffres.");
            }
        } else {
            throw new Exception("Erreur lors de la préparation de la requête Intérimaires: " . $conn->error);
        }

        // Requête pour ajouter dans la table Contrats
        $sql_contrat = "INSERT INTO contrat (date_debut, date_fin, responsable, affectation, projet, statut, matricule) VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Préparer la requête pour les Contrats
        if ($stmt_contrat = $conn->prepare($sql_contrat)) {
            $stmt_contrat->bind_param("sssssss", $date_debut, $date_fin, $_POST['contrat_responsable'], $_POST['contrat_affectation'], $_POST['contrat_projet'], $_POST['contrat_statut'], $matricule);
            $stmt_contrat->execute();
            $stmt_contrat->close();
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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter des données</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background-color: #4371c5;
            width: 100%;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        header img {
            margin-left: 20px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }

        h1 {
            color: #ffffff;
            margin: 20px 0;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        h2 {
            color: #000000;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #4371c5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #345a9a;
        }
    </style>
</head>
<body>
<header>
    <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
    <h1>Ajouter des données</h1>
    <nav>
        <a href="index.view.php">Retour</a>
    </nav>
</header>

<form method="post" action="traitement.php">
    <h2>Intérimaires</h2>
    <label for="interimaire_matricule">Matricule:</label>
    <input type="text" id="interimaire_matricule" name="interimaire_matricule" pattern="\d{6}" title="Le matricule doit être exactement 6 chiffres." required>

    <label for="interimaire_nom">Nom:</label>
    <input type="text" id="interimaire_nom" name="interimaire_nom" required>

    <label for="interimaire_prenom">Prénom:</label>
    <input type="text" id="interimaire_prenom" name="interimaire_prenom" required>

    <label for="interimaire_telephone">Téléphone:</label>
    <input type="text" id="interimaire_telephone" name="interimaire_telephone" required>

    <label for="interimaire_email">Email:</label>
    <input type="email" id="interimaire_email" name="interimaire_email" required>

    <label for="interimaire_competence">Compétence:</label>
    <input type="text" id="interimaire_competence" name="interimaire_competence" required>

    <h2>Contrats</h2>
    <label for="contrat_date_debut">Date Début:</label>
    <input type="date" id="contrat_date_debut" name="contrat_date_debut" required>

    <label for="contrat_date_fin">Date Fin:</label>
    <input type="date" id="contrat_date_fin" name="contrat_date_fin" required>

    <label for="contrat_responsable">Responsable:</label>
    <input type="text" id="contrat_responsable" name="contrat_responsable" required>

    <label for="contrat_affectation">Affectation:</label>
    <select id="contrat_affectation" name="contrat_affectation" required>
        <option value="affectation1">Affectation 1</option>
        <option value="affectation2">Affectation 2</option>
        <option value="affectation3">Affectation 3</option>
    </select>

    <label for="contrat_projet">Projet:</label>
    <input type="text" id="contrat_projet" name="contrat_projet" required>

    <label for="contrat_statut">Statut:</label>
    <select id="contrat_statut" name="contrat_statut" required>
        <option value="actif">Actif</option>
        <option value="inactif">Inactif</option>
        <option value="terminer">Terminer</option>
    </select>

    <h2>Sociétés</h2>
    <label for="societe_nom">Société:</label>
    <select id="societe_nom" name="societe_nom" required>
        <?php
        // Connexion à la base de données
        require_once 'db_connection.php';
        $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Requête pour récupérer les sociétés
        $sql = "SELECT nom FROM societes";
        $result = $conn->query($sql);

        // Boucle pour générer les options du choix des sociétés
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['nom']) . '">' . htmlspecialchars($row['nom']) . '</option>';
            }
        } else {
            echo '<option value="">Aucune société disponible</option>';
        }

        // Fermer la connexion
        $conn->close();
        ?>
    </select>

    <button type="submit">Enregistrer</button>
</form>
</body>
</html>

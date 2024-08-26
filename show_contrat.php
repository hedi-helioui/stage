<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    error_log("Erreur de connexion à la base de données : " . $conn->connect_error);
    die("Une erreur est survenue, veuillez réessayer plus tard.");
}

// Récupérer l'ID du contrat depuis l'URL
$id_contrat = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_contrat > 0) {
    // Requête pour récupérer les détails du contrat et de l'intérimaire associé
    $sql_details = "
        SELECT c.*, i.nom, i.prenom, i.tel, i.email, i.competences, s.nom AS societe_nom 
        FROM contrat c
        LEFT JOIN interimaire i ON c.matricule = i.matricule
        LEFT JOIN societes s ON i.nom_societe = s.nom
        WHERE c.id_contrat = ?
    ";

    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param("i", $id_contrat);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();

    if ($result_details && $result_details->num_rows > 0) {
        $details = $result_details->fetch_assoc();
    } else {
        die("Contrat non trouvé.");
    }

    $stmt_details->close();
} else {
    die("ID de contrat invalide.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Contrat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .section {
            padding: 20px;
            margin: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4371c5;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4371c5;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .actions {
            text-align: right;
            margin-top: 20px;
        }

        .actions .back, .actions .print {
            display: inline-block;
            background-color: #4371c5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-left: 10px;
        }

        .actions .back:hover, .actions .print:hover {
            background-color: #3561a8;
            transform: scale(1.05);
        }

        .actions .back:active, .actions .print:active {
            transform: scale(0.95);
        }
    </style>
    <script>
        function imprimerContrat() {
            window.print();
        }
    </script>
</head>
<body>
<!-- En-tête avec logo et navigation -->
<header>
    <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
    <h1>Gestion des Intérimaires et Contrats</h1>
    <nav>
        <a href="liste_societes.php">Sociétés</a>
        <a href="index.view.php">Retour</a>
    </nav>
</header>
<div class="section">
    <h2>Détails du Contrat</h2>

    <table>
        <tr>
            <th>Matricule Intérimaire</th>
            <td><?php echo htmlspecialchars($details['matricule']); ?></td>
        </tr>
        <tr>
            <th>Date Début</th>
            <td><?php echo htmlspecialchars($details['date_debut']); ?></td>
        </tr>
        <tr>
            <th>Date Fin</th>
            <td><?php echo htmlspecialchars($details['date_fin']); ?></td>
        </tr>
        <tr>
            <th>Responsable</th>
            <td><?php echo htmlspecialchars($details['responsable']); ?></td>
        </tr>
        <tr>
            <th>Affectation</th>
            <td><?php echo htmlspecialchars($details['affectation']); ?></td>
        </tr>
        <tr>
            <th>Projet</th>
            <td><?php echo htmlspecialchars($details['projet']); ?></td>
        </tr>
        <tr>
            <th>Statut</th>
            <td><?php echo htmlspecialchars($details['statut']); ?></td>
        </tr>
        <tr>
            <th>Nom de l'Intérimaire</th>
            <td><?php echo htmlspecialchars($details['nom']) . " " . htmlspecialchars($details['prenom']); ?></td>
        </tr>
        <tr>
            <th>Téléphone Intérimaire</th>
            <td><?php echo htmlspecialchars($details['tel']); ?></td>
        </tr>
        <tr>
            <th>Email Intérimaire</th>
            <td><?php echo htmlspecialchars($details['email']); ?></td>
        </tr>
        <tr>
            <th>Compétence</th>
            <td><?php echo htmlspecialchars($details['competences']); ?></td>
        </tr>
        <tr>
            <th>Nom de la Société</th>
            <td><?php echo htmlspecialchars($details['societe_nom']); ?></td>
        </tr>
    </table>

    <div class="actions">
        <a href="#" class="print" onclick="imprimerContrat()">Imprimer le Contrat</a>
    </div>
</div>
</body>
</html>

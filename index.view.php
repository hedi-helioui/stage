<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialisation de la variable de recherche
$search_matricule = isset($_GET['search_matricule']) ? $_GET['search_matricule'] : '';

// Préparation de la requête SQL avec filtre si une recherche est effectuée
$sql_interimaires = "SELECT * FROM interimaire";
if ($search_matricule) {
    $search_matricule = $conn->real_escape_string($search_matricule); // Protection contre les injections SQL
    $sql_interimaires .= " WHERE matricule LIKE '%$search_matricule%'";
}

// Exécution de la requête
$result_interimaires = $conn->query($sql_interimaires);

// Vérification des erreurs de requête
if (!$result_interimaires) {
    die("Erreur dans la requête SQL : " . $conn->error);
}

// Récupération des contrats avec le matricule d'intérimaire
$sql_contrats = "SELECT c.*, i.matricule 
                 FROM contrat c 
                 LEFT JOIN interimaire i ON c.matricule = i.matricule";
$result_contrats = $conn->query($sql_contrats);

// Récupération des sociétés avec le matricule d'intérimaire
$sql_societes = "SELECT s.*, i.matricule 
                 FROM societes s 
                 LEFT JOIN interimaire i ON s.matricule = i.matricule";
$result_societes = $conn->query($sql_societes);

// Fermeture de la connexion à la base de données
$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Intérimaires, Contrats et Sociétés</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4371c5;
            color: white;
            padding: 15px;
            text-align: center;
        }

        header img {
            vertical-align: middle;
        }

        header h1 {
            display: inline;
            margin-left: 20px;
        }

        header nav {
            margin-top: 10px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            background-color: #3561a8;
        }

        header nav a:hover {
            background-color: #2a4d91;
        }

        .section {
            padding: 20px;
            margin: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .actions {
            text-align: right;
            margin-bottom: 10px;
        }

        .actions .add {
            display: inline-block;
            background-color: #4371c5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .actions .add:hover {
            background-color: #3561a8;
            transform: scale(1.05);
        }

        .actions .add:active {
            transform: scale(0.95);
        }

        form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        form input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            flex: 1;
        }

        form button {
            background-color: #4371c5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #3561a8;
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

        tr:hover {
            background-color: #f1f1f1;
        }

        .edit, .delete {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }

        .edit {
            background-color: #4caf50;
        }

        .edit:hover {
            background-color: #45a049;
        }

        .delete {
            background-color: #f44336;
        }

        .delete:hover {
            background-color: #e53935;
        }

        .no-results {
            color: #555;
            font-style: italic;
        }
    </style>
</head>
<body>
<!-- En-tête avec logo et navigation -->
<header>
    <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
    <h1>Gestion des Intérimaires, Contrats et Sociétés</h1>
    <nav>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<!-- Intérimaires -->
<div class="section">
    <h2>Liste des Intérimaires</h2>
    <div class="actions">
        <a href="Formulaire.html" class="add">Remplir Formulaire</a>
    </div>

    <!-- Formulaire de recherche -->
    <form method="get" action="">
        <input type="text" name="search_matricule" placeholder="Rechercher par matricule" value="<?php echo isset($_GET['search_matricule']) ? htmlspecialchars($_GET['search_matricule']) : ''; ?>">
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($search_matricule && $result_interimaires->num_rows === 0): ?>
        <p class="no-results">Aucun intérimaire trouvé avec le matricule "<?php echo htmlspecialchars($search_matricule); ?>".</p>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Compétence</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result_interimaires && $result_interimaires->num_rows > 0) {
            while ($row = $result_interimaires->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['matricule']); ?></td>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($row['tel']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['competences']); ?></td>
                    <td class="actions">
                        <a href="edit_interimaire.php?id=<?php echo $row['id_interimaire']; ?>" class="edit">Modifier</a>
                        <a href="delete_interimaire.php?id=<?php echo $row['id_interimaire']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet intérimaire ?');">Supprimer</a>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="7">Aucun intérimaire trouvé.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Contrats -->
<div class="section">
    <h2>Liste des Contrats</h2>
    <table>
        <thead>
        <tr>
            <th>Date Début</th>
            <th>Date Fin</th>
            <th>Responsable</th>
            <th>Affectation</th>
            <th>Projet</th>
            <th>Statut</th>
            <th>Matricule Intérimaire</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result_contrats && $result_contrats->num_rows > 0) {
            while ($row = $result_contrats->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['date_debut']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_fin']); ?></td>
                    <td><?php echo htmlspecialchars($row['responsable']); ?></td>
                    <td><?php echo htmlspecialchars($row['affectation']); ?></td>
                    <td><?php echo htmlspecialchars($row['projet']); ?></td>
                    <td><?php echo htmlspecialchars($row['statut']); ?></td>
                    <td>
                        <?php
                        echo $row['matricule'] !== null ? htmlspecialchars($row['matricule']) : "Non attribué";
                        ?>
                    </td> <!-- Affichage du matricule ou "Non attribué" -->
                    <td class="actions">
                        <a href="edit_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="edit">Modifier</a>
                        <a href="delete_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?');">Supprimer</a>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="8">Aucun contrat trouvé.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>


<!-- Sociétés -->
<div class="section">
    <h2>Liste des Sociétés</h2>
    <table>
        <thead>
        <tr>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Matricule Intérimaire</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result_societes && $result_societes->num_rows > 0) {
            while ($row = $result_societes->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['adresse']); ?></td>
                    <td><?php echo htmlspecialchars($row['tel']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['matricule']); ?></td> <!-- Affichage du matricule -->
                    <td class="actions">
                        <a href="edit_societe.php?id=<?php echo $row['id_societe']; ?>" class="edit">Modifier</a>
                        <a href="delete_societe.php?id=<?php echo $row['id_societe']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette société ?');">Supprimer</a>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="6">Aucune société trouvée.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>


</body>
</html>

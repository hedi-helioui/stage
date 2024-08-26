<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    error_log("Erreur de connexion à la base de données : " . $conn->connect_error);
    die("Une erreur est survenue, veuillez réessayer plus tard.");
}

// Initialisation des variables de recherche des intérimaires
$search_matricule = isset($_GET['search_matricule']) ? $_GET['search_matricule'] : '';
$search_nom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';
$search_prenom = isset($_GET['search_prenom']) ? $_GET['search_prenom'] : '';

// Requête pour récupérer les informations des intérimaires avec le nom de la société
$sql_interimaires = "
    SELECT i.*, s.nom AS societe_nom 
    FROM interimaire i
    LEFT JOIN societes s ON i.nom_societe = s.nom
";

if ($search_matricule || $search_nom || $search_prenom) {
    $sql_interimaires .= " WHERE 1=1";
    if ($search_matricule) {
        $search_matricule = $conn->real_escape_string($search_matricule);
        $sql_interimaires .= " AND i.matricule LIKE ?";
    }
    if ($search_nom) {
        $search_nom = $conn->real_escape_string($search_nom);
        $sql_interimaires .= " AND i.nom LIKE ?";
    }
    if ($search_prenom) {
        $search_prenom = $conn->real_escape_string($search_prenom);
        $sql_interimaires .= " AND i.prenom LIKE ?";
    }
}

$stmt = $conn->prepare($sql_interimaires);
$params = [];

if ($search_matricule) {
    $params[] = "%$search_matricule%";
}
if ($search_nom) {
    $params[] = "%$search_nom%";
}
if ($search_prenom) {
    $params[] = "%$search_prenom%";
}

if ($params) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}

$stmt->execute();
$result_interimaires = $stmt->get_result();

// Initialisation des variables de recherche des contrats
$search_date_debut = isset($_GET['search_date_debut']) ? $_GET['search_date_debut'] : '';
$search_date_fin = isset($_GET['search_date_fin']) ? $_GET['search_date_fin'] : '';

// Requête pour récupérer les contrats avec les filtres de date
$sql_contrats = "
    SELECT c.*, i.matricule 
    FROM contrat c 
    LEFT JOIN interimaire i ON c.matricule = i.matricule
";

if ($search_date_debut || $search_date_fin) {
    $sql_contrats .= " WHERE 1=1";
    if ($search_date_debut) {
        $search_date_debut = $conn->real_escape_string($search_date_debut);
        $sql_contrats .= " AND c.date_debut >= ?";
    }
    if ($search_date_fin) {
        $search_date_fin = $conn->real_escape_string($search_date_fin);
        $sql_contrats .= " AND c.date_fin <= ?";
    }
}

$stmt_contrats = $conn->prepare($sql_contrats);
$params = [];

if ($search_date_debut) {
    $params[] = $search_date_debut;
}
if ($search_date_fin) {
    $params[] = $search_date_fin;
}

if ($params) {
    $stmt_contrats->bind_param(str_repeat("s", count($params)), ...$params);
}

$stmt_contrats->execute();
$result_contrats = $stmt_contrats->get_result();

// Fermeture des requêtes préparées et de la connexion
$stmt->close();
$stmt_contrats->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gestion des Intérimaires et Contrats</title>
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

        form input[type="text"], form input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            flex: 1;
            margin-right: 10px;
        }

        form button {
            background-color: #4371c5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
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
    <h1>Gestion des Intérimaires et Contrats</h1>
    <nav>
        <a href="liste_societes.php">Sociétés</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<!-- Intérimaires -->
<div class="section">
    <h2>Liste des Intérimaires</h2>
    <div class="actions">
        <a href="traitement.php" class="add">Remplir Formulaire</a>
    </div>

    <!-- Formulaire de recherche -->
    <form method="get" action="">
        <input type="text" name="search_matricule" id="search-matricule" placeholder="Rechercher par matricule" value="<?php echo htmlspecialchars($search_matricule); ?>">
        <input type="text" name="search_nom" id="search-nom" placeholder="Rechercher par nom" value="<?php echo htmlspecialchars($search_nom); ?>">
        <input type="text" name="search_prenom" id="search-prenom" placeholder="Rechercher par prénom" value="<?php echo htmlspecialchars($search_prenom); ?>">
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($search_matricule && $result_interimaires->num_rows === 0): ?>
        <p class="no-results">Aucun intérimaire trouvé avec le matricule "<?php echo htmlspecialchars($search_matricule); ?>".</p>
    <?php endif; ?>

    <table id="interimaires-table">
        <thead>
        <tr>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Compétence</th>
            <th>Nom de la Société</th> <!-- Nouvelle colonne pour le nom de la société -->
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
                    <td><?php echo htmlspecialchars($row['societe_nom']); ?></td> <!-- Affichage du nom de la société -->
                    <td class="actions">
                        <a href="edit_interimaire.php?id=<?php echo $row['id_interimaire']; ?>" class="edit">Modifier</a>
                        <a href="delete_interimaire.php?id=<?php echo $row['id_interimaire']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet intérimaire ?');">Supprimer</a>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="8">Aucun intérimaire trouvé.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Contrats -->
<div class="section">
    <h2>Liste des Contrats</h2>
    <!-- Formulaire de recherche des contrats -->
    <form method="get" action="">
        <input type="date" name="search_date_debut" id="search-date-debut" placeholder="Date de début" value="<?php echo htmlspecialchars($search_date_debut); ?>">
        <input type="date" name="search_date_fin" id="search-date-fin" placeholder="Date de fin" value="<?php echo htmlspecialchars($search_date_fin); ?>">

        <button type="button" class="reset-button" onclick="resetFilters()">X</button>
        <button type="submit">Rechercher</button>

    </form>

    <script>
        function resetFilters() {
            document.getElementById('search-date-debut').value = '';
            document.getElementById('search-date-fin').value = '';
            window.location.href = window.location.pathname; // Recharge la page sans les paramètres GET
        }
    </script>
    <style>
        .reset-button {
            background-color: #6f6f6f; /* Rouge */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 10px; /* Espacement par rapport au bouton Rechercher */
        }

        .reset-button:hover {
            background-color: #ffffff; /* Rouge plus foncé lors du survol */
        }
    </style>

    <table id="contrats-table">
        <thead>
        <tr>
            <th>Matricule Intérimaire</th>
            <th>Date Début</th>
            <th>Date Fin</th>
            <th>Responsable</th>
            <th>Affectation</th>
            <th>Projet</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result_contrats && $result_contrats->num_rows > 0) {
            while ($row = $result_contrats->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['matricule']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_debut']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_fin']); ?></td>
                    <td><?php echo htmlspecialchars($row['responsable']); ?></td>
                    <td><?php echo htmlspecialchars($row['affectation']); ?></td>
                    <td><?php echo htmlspecialchars($row['projet']); ?></td>
                    <td><?php echo htmlspecialchars($row['statut']); ?></td>

                    <td class="actions">
                        <a href="edit_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="edit">Modifier</a>
                        <a href="delete_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?');">Supprimer</a>
                        <a href="show_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="show">Afficher</a> <!-- Bouton Afficher -->

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

<script>
    function filterTable() {
        const matriculeFilter = document.getElementById('search-matricule').value.toLowerCase();
        const nomFilter = document.getElementById('search-nom').value.toLowerCase();
        const prenomFilter = document.getElementById('search-prenom').value.toLowerCase();
        const table = document.getElementById('interimaires-table');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const matricule = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const nom = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const prenom = row.querySelector('td:nth-child(3)').textContent.toLowerCase();

            const match = (matricule.includes(matriculeFilter) || matriculeFilter === '') &&
                (nom.includes(nomFilter) || nomFilter === '') &&
                (prenom.includes(prenomFilter) || prenomFilter === '');

            row.style.display = match ? '' : 'none';
        });
    }

    // Ajout des écouteurs d'événements pour la recherche dynamique
    document.getElementById('search-matricule').addEventListener('input', filterTable);
    document.getElementById('search-nom').addEventListener('input', filterTable);
    document.getElementById('search-prenom').addEventListener('input', filterTable);
</script>
</body>
</html>

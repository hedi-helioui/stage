<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête pour récupérer les sociétés
$sql_societes = "SELECT nom, adresse, tel, email, id_societe FROM societes";
$result_societes = $conn->query($sql_societes);

// Fermeture de la connexion à la base de données (à faire après avoir utilisé les résultats)
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
    <h1>Liste des Sociétés</h1>
    <nav>
        <a href="index.view.php">Intérimaires</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<!-- Sociétés -->
<div class="section">
    <h2>Liste des Sociétés</h2>

    <div class="actions">
        <a href="ajout_societes.php" class="add">Ajouter une société</a>

    </div>
    <div class="actions">
        <form id="search-form">
            <input type="text" id="search" placeholder="Rechercher une société...">
            <button type="button" onclick="searchSociete()">Rechercher</button>
        </form>
    </div>
    <script>
        function searchSociete() {
            // Récupère la valeur du champ de recherche
            let searchValue = document.getElementById('search').value.toLowerCase();

            // Récupère toutes les lignes du tableau des sociétés
            let rows = document.querySelectorAll('tbody tr');

            // Parcours de chaque ligne pour vérifier si elle correspond à la recherche
            rows.forEach(row => {
                let nom = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                if (nom.includes(searchValue)) {
                    row.style.display = '';  // Affiche la ligne si elle correspond
                } else {
                    row.style.display = 'none';  // Masque la ligne si elle ne correspond pas
                }
            });
        }

        // Ajout d'un écouteur d'événement pour une recherche dynamique en temps réel
        document.getElementById('search').addEventListener('keyup', searchSociete);
    </script>


    <table>
        <thead>
        <tr>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Téléphone</th>
            <th>Email</th>
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
                    <td class="actions">
                        <a href="edit_societe.php?id=<?php echo $row['id_societe']; ?>" class="edit">Modifier</a>
                        <a href="delete_societe.php?id=<?php echo $row['id_societe']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet intérimaire ?');">Supprimer</a>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="5" class="no-results">Aucune société trouvée.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php
// Fermeture de la connexion à la base de données
$conn->close();
?>

</body>
</html>

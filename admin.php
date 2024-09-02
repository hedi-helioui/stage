<?php
session_start();
include 'db_connection.php'; // Fichier pour la connexion à la base de données

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['loggedin']) || $_SESSION['matricule'] !== '00001') {
    header("Location: connexion.html"); // Rediriger vers la page de connexion si non autorisé
    exit;
}

// Connexion à la base de données
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Préparation de la requête SQL pour récupérer tous les employés
$sql = "SELECT matricule, nom, prenom, email, authorization FROM employer";
$result = $conn->query($sql);

// Récupérer la notification de la session
$notification = isset($_SESSION['notification']) ? $_SESSION['notification'] : '';
unset($_SESSION['notification']); // Effacer la notification après l'affichage

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Liste des Employés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        header {
            background-color: #4371c5;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4371c5;
            color: white;
        }
        .btn {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 2px 1px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-unauthorized {
            background-color: #f44336; /* Red */
        }
        .notification {
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .notification-error {
            background-color: #f44336; /* Red */
        }
        #search {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<header>
    <h1>Administration</h1>
</header>
<div class="container">
    <?php if ($notification): ?>
        <div class="notification <?php echo strpos($notification, 'Erreur') === false ? '' : 'notification-error'; ?>">
            <?php echo htmlspecialchars($notification); ?>
        </div>
    <?php endif; ?>
    <h2>Liste des Employés</h2>

    <input type="text" id="search" placeholder="Rechercher par matricule, nom ou prénom">

    <table id="employeesTable">
        <thead>
        <tr>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $authorization_status = $row['authorization'] ? 'Unauthorized' : 'Authorized';
                $btn_class = $row['authorization'] ? 'btn-unauthorized' : 'btn';
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['matricule']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td><form method='POST' action='admin_action.php'>
                          <input type='hidden' name='matricule' value='" . htmlspecialchars($row['matricule']) . "'>
                          <button type='submit' class='btn " . $btn_class . "' name='action' value='" . ($row['authorization'] ? 'unauthorize' : 'authorize') . "'>
                              " . $authorization_status . "
                          </button>
                      </form></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun employé trouvé</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        var searchTerm = this.value.toLowerCase();
        var rows = document.querySelectorAll('#employeesTable tbody tr');

        rows.forEach(function(row) {
            var cells = row.getElementsByTagName('td');
            var matched = Array.from(cells).some(function(cell) {
                return cell.textContent.toLowerCase().includes(searchTerm);
            });

            row.style.display = matched ? '' : 'none';
        });
    });
</script>

<?php
$conn->close();
?>
</body>
</html>

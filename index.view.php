<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bh bank');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupération des intérimaires
$sql_interimaires = "SELECT * FROM interimaire";
$result_interimaires = $conn->query($sql_interimaires);

// Récupération des contrats
$sql_contrats = "SELECT * FROM contrat";
$result_contrats = $conn->query($sql_contrats);

// Récupération des affectations
$sql_societes = "SELECT * FROM societes";
$result_societes = $conn->query($sql_societes);



// Fermeture de la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Intérimaires, Contrats et societes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- En-tête avec logo et navigation -->
    <header>
        <img src="bh-bank-new-rouge-removebg-preview.png" alt="Logo BH Bank" width="120" height="auto">
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <h1>Gestion des Intérimaires, Contrats et Societes</h1>

    <!-- Intérimaires -->
    <div class="section">
        <h2>Liste des Intérimaires</h2>
        <div class="actions">
            <a href="Formulaire.html" class="add">Remplir Formulaire</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Competence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_interimaires && $result_interimaires->num_rows > 0) {
                    while ($row = $result_interimaires->fetch_assoc()) { ?>
                        <tr>
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
                        <td colspan="6">Aucun intérimaire trouvé.</td>
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
                    <th>affectation</th>
                    <th>projet</th>
                    <th>statut</th>
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
                            <td class="actions">
                            <a href="edit_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="edit">Modifier</a>
                            <a href="delete_contrat.php?id=<?php echo $row['id_contrat']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet intérimaire ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="7">Aucun contrat trouvé.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- societes -->
    <div class="section">
        <h2>Liste des societes</h2>
        <table>
            <thead>
                <tr>

                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>telephone</th>
                    <th>email</th>
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
                            <a href="delete_societe.php?id=<?php echo $row['id_societe']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cete societe ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="7">Aucune affectation trouvée.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

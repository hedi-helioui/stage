<?php
include 'db_connection.php'; // Assurez-vous d'inclure le fichier de connexion à la base de données

$sql = "SELECT * FROM contrat";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['id_interimaire']) . "</td>";
        echo "<td>" . htmlspecialchars($row['id_societe']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date_debut']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date_fin']) . "</td>";
        echo "<td>" . htmlspecialchars($row['departement']) . "</td>";
        echo '<td>
                  <button class="btn btn-modifier" onclick="modifier(' . $row['id'] . ')">Modifier</button>
                  <button class="btn btn-supprimer" onclick="supprimer(' . $row['id'] . ')">Supprimer</button>
              </td>';
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Aucun contrat trouvé</td></tr>";
}

$conn->close();
?>

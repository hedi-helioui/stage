<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = $_POST['matricule'];
    $action = $_POST['action'];

    // Vérifier si le matricule est '00001'
    if ($matricule === '00001') {
        // Ne pas autoriser la modification de l'autorisation pour le matricule '00001'
        header("Location: admin.php"); // Rediriger vers la page admin
        exit;
    }

    // Déterminer le nouveau statut d'autorisation
    $new_status = ($action === 'authorize') ? 1 : 0;

    // Préparation de la requête SQL pour mettre à jour l'autorisation
    $sql = "UPDATE employer SET authorization = ? WHERE matricule = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("ii", $new_status, $matricule);
    if (!$stmt->execute()) {
        die("Erreur d'exécution de la requête: " . $stmt->error);
    }

    // Récupérer les informations de l'employé pour envoyer l'email
    $stmt->close();
    $sql = "SELECT email, nom, prenom FROM employer WHERE matricule = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("i", $matricule);
    $stmt->execute();
    $stmt->bind_result($email, $nom, $prenom);
    $stmt->fetch();
    $stmt->close();

    $mail = new PHPMailer(true);
    try {
        // Paramètres du serveur
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Hôte SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'heliouihedy@gmail.com'; // Votre adresse e-mail
        $mail->Password   = 'dsxb fihr hxsz qoqe'; // Utilisez le mot de passe d'application généré
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // Port pour STARTTLS

        // Activer le débogage SMTP
        $mail->SMTPDebug = 2; // Utiliser 2 pour le débogage détaillé

        // Expéditeur et destinataire
        $mail->setFrom('heliouihedy@gmail.com', 'Administration');
        $mail->addAddress($email, "$prenom $nom");

        // Contenu de l'email
        $mail->isHTML(false);
        $mail->Subject = $new_status ? "Autorisation Accordée" : "Autorisation Révoquée";
        $mail->Body    = "Bonjour $prenom $nom,\n\nVotre statut d'autorisation a été " . ($new_status ? "accordé" : "révoqué") . ".\nCordialement,\nL'équipe d'administration";

        // Envoyer l'email
        $mail->send();
        $_SESSION['notification'] = "L'email de notification a été envoyé avec succès.";
    } catch (Exception $e) {
        $_SESSION['notification'] = "Erreur lors de l'envoi de l'email. Erreur : " . $mail->ErrorInfo;
    }
}

$conn->close();
header("Location: admin.php"); // Rediriger vers la page admin après l'action
exit;
?>

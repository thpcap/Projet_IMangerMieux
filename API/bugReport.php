<?php
require_once('init_PDO.php'); // Make sure this file has email configurations or constants
require_once('usefullFunctions.php');

setHeaders(); // Définir les headers avant toute sortie
session_start();


switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':
        // Decode JSON data from the request
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if all required fields are present
        if (isset($data['name'], $data['email'], $data['subject'], $data['message'])) {
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);
            $subject = htmlspecialchars($data['subject']);
            $message = htmlspecialchars($data['message']);

            // Prepare the email content
            $emailContent = "Nom: $name\nEmail: $email\nSujet: $subject\nMessage:\n$message";

            // Send the email
            $to = _Contact_Email; // Define this in your init_PDO.php file
            $emailSubject = "Nouveau message de contact : $subject";
            $headers = "From: $email";

            if (mail($to, $emailSubject, $emailContent, $headers)) {
                http_response_code(200);
                echo json_encode(['success' => 'Message envoyé avec succès']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => "Échec de l'envoi de l'email"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Données du formulaire incomplètes']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}
?>

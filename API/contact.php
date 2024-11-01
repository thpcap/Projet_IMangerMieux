<?php
// Inclure la configuration de la base de données et les fonctions utiles
require_once('init_PDO.php');
require_once('usefullFunctions.php');

// Définir les en-têtes pour les réponses JSON
setHeaders();

// Vérifier si la méthode de requête est POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer et décoder les données JSON envoyées depuis le frontend
    $data = json_decode(file_get_contents("php://input"), true);

    // Vérifier si tous les champs requis sont présents
    if (isset($data['name']) && isset($data['email']) && isset($data['subject']) && isset($data['message'])) {
        $name = htmlspecialchars($data['name']);
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $subject = htmlspecialchars($data['subject']);
       

        // Gérer les pièces jointes si elles existent
        $attachmentPath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $attachment = $_FILES['attachment'];
            $attachmentPath = "uploads/" . basename($attachment["name"]);
            move_uploaded_file($attachment["tmp_name"], $attachmentPath);
        }

        try {
            // Insérer les données dans la table contact_messages
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, attachment) VALUES (:name, :email, :subject, :message, :attachment)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message,
                ':attachment' => $attachmentPath
            ]);

            // Envoi de l'email de notification
            $to = "yabenjane@gmail.com"; // Remplacer par l'adresse email de support
            $headers = "From: $email\r\n" .
                       "Reply-To: $email\r\n" .
                       "Content-Type: text/html; charset=UTF-8\r\n";
            $emailBody = "
                <h1>Nouveau message de contact</h1>
                <p><strong>Nom :</strong> $name</p>
                <p><strong>Email :</strong> $email</p>
                <p><strong>Sujet :</strong> $subject</p>
                <p><strong>Message :</strong><br>$message</p>";
            mail($to, "Nouveau message de contact : $subject", $emailBody, $headers);

            // Répondre au frontend en cas de succès
            http_response_code(201);
            echo json_encode(['message' => 'Votre message a été envoyé avec succès. Nous vous contacterons bientôt.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
        }
    } else {
        // Réponse d'erreur en cas de données manquantes
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes pour le formulaire de contact.']);
    }
} else {
    // Méthode non autorisée
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
}
?>

<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');

    setHeaders(); // Définir les headers avant toute sortie

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            // Préparer la requête SQL pour récupérer tous les niveaux de pratique
            $stmt = $pdo->prepare("SELECT NIVEAU_DE_PRATIQUE.ID_PRATIQUE, NIVEAU_DE_PRATIQUE.LIBELE_PRATIQUE FROM NIVEAU_DE_PRATIQUE;");
            try {
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats
            } catch (PDOException $e) {
                http_response_code(500); // Erreur serveur interne
                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                exit;
            }
            
            // Renvoyer la réponse JSON avec tous les niveaux de pratique
            http_response_code(200); // OK
            echo json_encode($data);
            break;

        default:
            // Si une méthode autre que GET est utilisée, renvoyer une erreur 405
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }

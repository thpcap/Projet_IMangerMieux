<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');

    setHeaders(); // Définir les headers avant toute sortie

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            // Préparer la requête SQL pour récupérer tous les sexes
            $stmt = $pdo->prepare("SELECT ALIMENT.ID_ALIMENT,ALIMENT.LABEL_ALIMENT,TYPE_ALIMENT.LIBELE_TYPE FROM ALIMENT INNER JOIN TYPE_ALIMENT ON ALIMENT.ID_TYPE=TYPE_ALIMENT.ID_TYPE;");
            try {
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats
            } catch (PDOException $e) {
                http_response_code(500); // Erreur serveur interne
                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                exit;
            }
            
            // Renvoyer la réponse JSON avec tous les sexes
            http_response_code(200); // OK
            echo json_encode($data);
            break;

        default:
            // Si une méthode autre que GET est utilisée, renvoyer une erreur 405
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }

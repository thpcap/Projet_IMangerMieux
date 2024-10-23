<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');

    // Définir les headers avant toute sortie
    setHeaders();
    session_start();

    // Vérification de la connexion
    if (!(isset($_SESSION['connected']) && $_SESSION['connected'])) {
        http_response_code(401); // Non autorisé
        echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
        exit;
    }

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            // Vérification que le login est présent
            if (isset($_GET['login'])) {
                $login = $_GET['login'];
                // Vérifier que le login correspond à celui de la session
                if($login !== $_SESSION['login']) {
                    http_response_code(403); // Interdit
                    echo json_encode(['error' => 'Accès interdit. Vous ne pouvez pas accéder aux informations d\'autres utilisateurs.']);
                    exit;
                }

                // Préparer la requête SQL pour récupérer les informations utilisateur
                $stmt = $pdo->prepare("
                    SELECT 
                        utilisateur.LOGIN, 
                        utilisateur.NOM, 
                        utilisateur.PRENOM, 
                        utilisateur.DATE_DE_NAISSANCE, 
                        utilisateur.MAIL, 
                        utilisateur.ID_SEXE, 
                        utilisateur.ID_AGE, 
                        utilisateur.ID_PRATIQUE 
                    FROM 
                        utilisateur 
                    WHERE 
                        utilisateur.LOGIN = :login;
                ");

                try {
                    // Exécuter la requête avec le login de la session
                    $stmt->execute([':login' => $_SESSION['login']]);
                    $dataRes = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats

                    // Vérification si l'utilisateur a été trouvé
                    if (empty($dataRes)) {
                        http_response_code(404); // Non trouvé
                        echo json_encode(['error' => 'Utilisateur non trouvé.']);
                        exit;
                    }

                    // Renvoyer la réponse JSON avec les informations utilisateur
                    http_response_code(200); // OK
                    echo json_encode($dataRes);

                } catch (PDOException $e) {
                    http_response_code(500); // Erreur serveur interne
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                    exit;
                }
            } else {
                // Si le login n'est pas fourni
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Le login est requis.']);
                exit;
            }
            break;

        default:
            // Si une méthode autre que GET est utilisée, renvoyer une erreur 405
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }

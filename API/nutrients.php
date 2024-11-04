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
        // Vérification que le login et la date sont présents
        if (isset($_GET['login']) && isset($_GET['date'])) {
            $login = $_GET['login'];
            $date = $_GET['date'];

            // Vérifier que le login correspond à celui de la session
            if ($login !== $_SESSION['login']) {
                http_response_code(403); // Interdit
                echo json_encode(['error' => 'Accès interdit. Vous ne pouvez pas accéder aux informations d\'autres utilisateurs.']);
                exit;
            }

            try {
                // Vérifier si l'utilisateur existe dans la base de données
                $stmtUserCheck = $pdo->prepare("SELECT LOGIN FROM UTILISATEUR WHERE LOGIN = :login");
                $stmtUserCheck->execute([':login' => $_SESSION['login']]);
                $userExists = $stmtUserCheck->fetch(PDO::FETCH_ASSOC);

                if (!$userExists) {
                    http_response_code(404); // Non trouvé
                    echo json_encode(['error' => 'Utilisateur non trouvé dans la base de données.']);
                    exit;
                }

                // Requête pour récupérer les nutriments consommés
                $stmtConsumed = $pdo->prepare("
                    SELECT 
                        NUTRIMENTS.ID_NUTRIMENT,
                        NUTRIMENTS.LIBELE_NUTRIMENT,
                        SUM(CONTIENT.RATIOS * REPAS.QUANTITE) AS TOTAL_CONSUMED
                    FROM 
                        REPAS
                    JOIN 
                        ALIMENT ON REPAS.ID_ALIMENT = ALIMENT.ID_ALIMENT
                    JOIN 
                        CONTIENT ON ALIMENT.ID_ALIMENT = CONTIENT.ID_ALIMENT
                    JOIN 
                        NUTRIMENTS ON CONTIENT.ID_NUTRIMENT = NUTRIMENTS.ID_NUTRIMENT
                    WHERE 
                        REPAS.LOGIN = :login AND DATE(REPAS.DATE) = :date
                    GROUP BY 
                        NUTRIMENTS.ID_NUTRIMENT, NUTRIMENTS.LIBELE_NUTRIMENT
                ");
                $stmtConsumed->execute([':login' => $_SESSION['login'], ':date' => $date]);
                $consumedData = $stmtConsumed->fetchAll(PDO::FETCH_ASSOC);

                // Requête pour récupérer les besoins de l'utilisateur
                $stmtNeeds = $pdo->prepare("
                    SELECT 
                        NUTRIMENTS.ID_NUTRIMENT,
                        NUTRIMENTS.LIBELE_NUTRIMENT,
                        A_BESOINS.BESOINS AS USER_NEED
                    FROM 
                        A_BESOINS
                    JOIN 
                        NUTRIMENTS ON A_BESOINS.ID_NUTRIMENT = NUTRIMENTS.ID_NUTRIMENT
                    WHERE 
                        A_BESOINS.LOGIN = :login
                ");
                $stmtNeeds->execute([':login' => $_SESSION['login']]);
                $needsData = $stmtNeeds->fetchAll(PDO::FETCH_ASSOC);

                // Association des données et calculs
                $result = [];
                foreach ($consumedData as $consumed) {
                    foreach ($needsData as $need) {
                        if ($consumed['ID_NUTRIMENT'] === $need['ID_NUTRIMENT']) {
                            $percentage = ($need['USER_NEED'] > 0) ? ($consumed['TOTAL_CONSUMED'] / $need['USER_NEED']) * 100 : 0;
                            $result[$consumed['LIBELE_NUTRIMENT']] = $percentage;
                        }
                    }
                }

                // Renvoyer la réponse JSON
                http_response_code(200); // OK
                echo json_encode($result);

            } catch (PDOException $e) {
                http_response_code(500); // Erreur serveur interne
                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                exit;
            }
        } else {
            http_response_code(400); // Mauvaise requête
            echo json_encode(['error' => 'Le login et la date sont requis.']);
            exit;
        }
        break;

    default:
        // Si une méthode autre est utilisée, renvoyer une erreur 405
        http_response_code(405); // Méthode non autorisée
        echo json_encode(['error' => 'Méthode non autorisée.']);
        break;
}

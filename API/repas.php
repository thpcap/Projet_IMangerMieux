<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');

    setHeaders(); // Définir les headers avant toute sortie
    session_start();

    if (!isset($_SESSION['connected']) || !$_SESSION['connected'] || !isset($_SESSION['login'])) {
        http_response_code(401); // Non autorisé
        echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
        exit;
    }

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            if (isset($_GET['login'])) {
                if ($_GET['login'] !== $_SESSION['login']) {
                    http_response_code(401); // Non autorisé
                    echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
                    exit;
                }

                // Préparer la requête SQL pour récupérer tous les repas des 7 derniers jours
                $stmt = $pdo->prepare(
                    "SELECT repas.ID_REPAS, repas.QUANTITE, repas.DATE, aliment.LABEL_ALIMENT 
                    FROM repas 
                    INNER JOIN aliment ON repas.ID_ALIMENT = aliment.ID_ALIMENT 
                    WHERE repas.LOGIN = :login AND repas.DATE >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
                );

                try {
                    $stmt->execute([':login' => $_SESSION['login']]);
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats
                } catch (PDOException $e) {
                    http_response_code(500); // Erreur serveur interne
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                    exit;
                }

                // Renvoyer la réponse JSON avec tous les repas
                http_response_code(200); // OK
                echo json_encode($data);
                break; // N'oubliez pas de sortir du switch
            } else {
                // Si le login n'est pas fourni
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Le login est requis.']);
                exit;
            }

        case 'POST':
            // Vérifier que les données sont présentes
            $data = json_decode(file_get_contents('php://input'), true); // Récupérer les données JSON
            
            if (isset($data['login']) && isset($data['quantite']) && isset($data['date']) && isset($data['id_aliment'])) {
                // Vérifier que le login dans la session correspond à celui dans les données
                if ($data['login'] !== $_SESSION['login']) {
                    http_response_code(401); // Non autorisé
                    echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
                    exit;
                }

                $quantite = $data['quantite'];
                $date = $data['date'];
                $id_aliment = $data['id_aliment'];

                // Vérifier que l'aliment existe dans la base de données
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM aliment WHERE ID_ALIMENT = :id_aliment");
                $stmt->execute([':id_aliment' => $id_aliment]);
                $alimentExists = $stmt->fetchColumn();

                if ($alimentExists) {
                    // Préparer la requête d'insertion
                    $stmt = $pdo->prepare(
                        "INSERT INTO repas (LOGIN, QUANTITE, DATE, ID_ALIMENT) VALUES (:login, :quantite, :date, :id_aliment)"
                    );

                    try {
                        // Exécuter l'insertion
                        $stmt->execute([
                            ':login' => $_SESSION['login'],
                            ':quantite' => $quantite,
                            ':date' => $date,
                            ':id_aliment' => $id_aliment
                        ]);
                        http_response_code(201); // Created
                        echo json_encode(['message' => 'Repas créé avec succès.']);
                    } catch (PDOException $e) {
                        http_response_code(500); // Erreur serveur interne
                        echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                    }
                } else {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'aliment spécifié n\'existe pas.']);
                }
            } else {
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Données manquantes pour la création du repas.']);
            }
            break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true); // Récupérer les données JSON
        
                if (isset($data['login']) && isset($data['id_repas']) && isset($data['quantite']) && isset($data['date']) && isset($data['id_aliment'])) {
                    $login = $data['login'];
                    $id_repas = $data['id_repas'];
                    $quantite = $data['quantite'];
                    $date = $data['date'];
                    $id_aliment = $data['id_aliment'];
        
                    // Vérifier que le login correspond à celui de l'utilisateur connecté
                    if ($login !== $_SESSION['login']) {
                        http_response_code(401); // Non autorisé
                        echo json_encode(['error' => 'Vous ne pouvez pas modifier ce repas.']);
                        exit;
                    }
        
                    // Vérifier que le repas existe pour l'utilisateur
                    $stmt = $pdo->prepare(
                        "SELECT LOGIN FROM repas WHERE ID_REPAS = :id_repas"
                    );
                    $stmt->execute([':id_repas' => $id_repas]);
                    $existingMeal = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    if ($existingMeal) {
                        // Vérifier que le login correspond à celui de l'utilisateur connecté
                        if ($existingMeal['LOGIN'] !== $login) {
                            http_response_code(401); // Non autorisé
                            echo json_encode(['error' => 'Vous ne pouvez pas modifier ce repas.']);
                            exit;
                        }
        
                        // Vérifier que l'aliment existe dans la base de données
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM aliment WHERE ID_ALIMENT = :id_aliment");
                        $stmt->execute([':id_aliment' => $id_aliment]);
                        $alimentExists = $stmt->fetchColumn();
        
                        if ($alimentExists) {
                            // Préparer la requête de mise à jour
                            $stmt = $pdo->prepare(
                                "UPDATE repas SET QUANTITE = :quantite, DATE = :date, ID_ALIMENT = :id_aliment WHERE ID_REPAS = :id_repas"
                            );
        
                            try {
                                // Exécuter la mise à jour
                                $stmt->execute([
                                    ':quantite' => $quantite,
                                    ':date' => $date,
                                    ':id_aliment' => $id_aliment,
                                    ':id_repas' => $id_repas
                                ]);
                                http_response_code(200); // OK
                                echo json_encode(['message' => 'Repas mis à jour avec succès.']);
                            } catch (PDOException $e) {
                                http_response_code(500); // Erreur serveur interne
                                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                            }
                        } else {
                            http_response_code(400); // Mauvaise requête
                            echo json_encode(['error' => 'L\'aliment spécifié n\'existe pas.']);
                        }
                    } else {
                        // Si le repas n'existe pas
                        http_response_code(404); // Non trouvé
                        echo json_encode(['error' => 'Repas non trouvé.']);
                    }
                } else {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'Données manquantes pour la mise à jour du repas.']);
                }
                break;

                case 'DELETE':
                    // Récupérer l'ID_REPAS et le login à partir de l'entrée JSON
                    $data = json_decode(file_get_contents('php://input'), true); // Récupérer les données JSON
            
                    if (isset($data['login']) && isset($data['id_repas'])) {
                        $login = $data['login'];
                        $id_repas = $data['id_repas'];
            
                        // Vérifier que le login correspond à celui de l'utilisateur connecté
                        if ($login !== $_SESSION['login']) {
                            http_response_code(401); // Non autorisé
                            echo json_encode(['error' => 'Vous ne pouvez pas supprimer ce repas.']);
                            exit;
                        }
            
                        // Vérifier que le repas existe pour l'utilisateur
                        $stmt = $pdo->prepare(
                            "SELECT LOGIN FROM repas WHERE ID_REPAS = :id_repas"
                        );
                        $stmt->execute([':id_repas' => $id_repas]);
                        $existingMeal = $stmt->fetch(PDO::FETCH_ASSOC);
            
                        if ($existingMeal) {
                            // Vérifier que le login correspond à celui de l'utilisateur connecté
                            if ($existingMeal['LOGIN'] !== $login) {
                                http_response_code(401); // Non autorisé
                                echo json_encode(['error' => 'Vous ne pouvez pas supprimer ce repas.']);
                                exit;
                            }
            
                            // Préparer la requête de suppression
                            $stmt = $pdo->prepare("DELETE FROM repas WHERE ID_REPAS = :id_repas");
                            try {
                                // Exécuter la suppression
                                $stmt->execute([':id_repas' => $id_repas]);
                                http_response_code(200); // OK
                                echo json_encode(['message' => 'Repas supprimé avec succès.']);
                            } catch (PDOException $e) {
                                http_response_code(500); // Erreur serveur interne
                                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                            }
                        } else {
                            // Si le repas n'existe pas
                            http_response_code(404); // Non trouvé
                            echo json_encode(['error' => 'Repas non trouvé.']);
                        }
                    } else {
                        http_response_code(400); // Mauvaise requête
                        echo json_encode(['error' => 'Données manquantes pour la suppression du repas.']);
                    }
                    break;

        default:
            // Méthode non autorisée
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }

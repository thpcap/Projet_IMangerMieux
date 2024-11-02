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

            $stmt = $pdo->prepare(
                "SELECT REPAS.ID_REPAS, REPAS.QUANTITE, REPAS.DATE, ALIMENT.LABEL_ALIMENT 
                FROM REPAS 
                INNER JOIN ALIMENT ON REPAS.ID_ALIMENT = ALIMENT.ID_ALIMENT 
                WHERE REPAS.LOGIN = :login AND REPAS.DATE >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            );

            try {
                $stmt->execute([':login' => $_SESSION['login']]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                exit;
            }

            http_response_code(200);
            echo json_encode($data);
            break;
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Le login est requis.']);
            exit;
        }

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['login']) && isset($data['quantite']) && isset($data['date']) && isset($data['id_aliment'])) {
            if ($data['login'] !== $_SESSION['login']) {
                http_response_code(401);
                echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
                exit;
            }

            $quantite = $data['quantite'];
            $date = $data['date'];
            $id_aliment = $data['id_aliment'];

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM ALEIMENT WHERE ID_ALIMENT = :id_aliment");
            $stmt->execute([':id_aliment' => $id_aliment]);
            $alimentExists = $stmt->fetchColumn();

            if ($alimentExists) {
                $stmt = $pdo->prepare(
                    "INSERT INTO REPAS (LOGIN, QUANTITE, DATE, ID_ALIMENT) VALUES (:login, :quantite, :date, :id_aliment)"
                );

                try {
                    $stmt->execute([
                        ':login' => $_SESSION['login'],
                        ':quantite' => $quantite,
                        ':date' => $date,
                        ':id_aliment' => $id_aliment
                    ]);
                    http_response_code(201);
                    echo json_encode(['message' => 'Repas créé avec succès.']);
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'L\'aliment spécifié n\'existe pas.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes pour la création du REPAS.']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['login']) || !isset($data['id_REPAS']) || !isset($data['quantite']) || !isset($data['date'])) {
            $missingFields = [];
            if (!isset($data['login'])) $missingFields[] = 'login';
            if (!isset($data['id_REPAS'])) $missingFields[] = 'id_REPAS';
            if (!isset($data['quantite'])) $missingFields[] = 'quantite';
            if (!isset($data['date'])) $missingFields[] = 'date';

            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes pour la mise à jour du REPAS.', 'missing_fields' => $missingFields]);
            exit;
        }

        $login = $data['login'];
        $id_REPAS = $data['id_REPAS'];
        $quantite = $data['quantite'];
        $date = $data['date'];
        $id_aliment = $data['id_aliment'];

        if ($login !== $_SESSION['login']) {
            http_response_code(401);
            echo json_encode(['error' => 'Vous ne pouvez pas modifier ce REPAS.']);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT LOGIN FROM REPAS WHERE ID_REPAS = :id_REPAS"
        );
        $stmt->execute([':id_REPAS' => $id_REPAS]);
        $existingMeal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingMeal && $existingMeal['LOGIN'] === $login) {
            $updateFields = [
                ':quantite' => $quantite,
                ':date' => $date,
                ':id_REPAS' => $id_REPAS
            ];
            $sql = "UPDATE REPAS SET QUANTITE = :quantite, DATE = :date";

            // Ajouter `ID_ALIMENT` dans la mise à jour uniquement si présent
            if ($id_aliment !== null) {
                $sql .= ", ID_ALIMENT = :id_aliment";
                $updateFields[':id_aliment'] = $id_aliment;

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM aliment WHERE ID_ALIMENT = :id_aliment");
                $stmt->execute([':id_aliment' => $id_aliment]);
                $alimentExists = $stmt->fetchColumn();

                if (!$alimentExists) {
                    http_response_code(400);
                    echo json_encode(['error' => 'L\'aliment spécifié n\'existe pas.']);
                    exit;
                }
            }

            $sql .= " WHERE ID_REPAS = :id_REPAS";
            $stmt = $pdo->prepare($sql);

            try {
                $stmt->execute($updateFields);
                http_response_code(200);
                echo json_encode(['message' => 'Repas mis à jour avec succès.']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Repas non trouvé ou accès non autorisé.']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['login']) && isset($data['id_REPAS'])) {
            $login = $data['login'];
            $id_REPAS = $data['id_REPAS'];

            if ($login !== $_SESSION['login']) {
                http_response_code(401);
                echo json_encode(['error' => 'Vous ne pouvez pas supprimer ce REPAS.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT LOGIN FROM REPAS WHERE ID_REPAS = :id_REPAS");
            $stmt->execute([':id_REPAS' => $id_REPAS]);
            $existingMeal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingMeal && $existingMeal['LOGIN'] === $login) {
                $stmt = $pdo->prepare("DELETE FROM REPAS WHERE ID_REPAS = :id_REPAS");
                try {
                    $stmt->execute([':id_REPAS' => $id_REPAS]);
                    http_response_code(200);
                    echo json_encode(['message' => 'Repas supprimé avec succès.']);
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Repas non trouvé ou accès non autorisé.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes pour la suppression du REPAS.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}

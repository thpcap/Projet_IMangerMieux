<?php
require_once('init_PDO.php');
setHeaders();

// Récupérer tous les repas (READ)
function getAllRepas($pdo) {
    $sql = "SELECT * FROM repas";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer un repas par ID (READ)
function getRepasById($pdo, $id) {
    $sql = "SELECT * FROM repas WHERE id = :id";
    $statement = $pdo->prepare($sql);
    $statement->execute(['id' => $id]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}

// Créer un repas (CREATE)
function createRepas($pdo, $quantite, $date) {
    $sql = "INSERT INTO repas (quantite, date) VALUES (:quantite, :date)";
    $statement = $pdo->prepare($sql);
    $statement->execute(['quantite' => $quantite, 'date' => $date]);
    return $pdo->lastInsertId(); // Retourne l'ID du nouveau repas
}

// Mettre à jour un repas (UPDATE)
function updateRepas($pdo, $id, $quantite, $date) {
    $sql = "UPDATE repas SET quantite = :quantite, date = :date WHERE id = :id";
    $statement = $pdo->prepare($sql);
    $statement->execute(['quantite' => $quantite, 'date' => $date, 'id' => $id]);
}

// Supprimer un repas (DELETE)
function deleteRepas($pdo, $id) {
    $sql = "DELETE FROM repas WHERE id = :id";
    $statement = $pdo->prepare($sql);
    $statement->execute(['id' => $id]);
}

// Gestion des requêtes HTTP
$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Récupérer un repas spécifique
            $repas = getRepasById($pdo, $_GET['id']);
            if ($repas) {
                echo json_encode($repas);
            } else {
                http_response_code(404);  // 404 Not Found
                echo "404 Not Found";
            }
        } else {
            // Récupérer tous les repas
            $repas = getAllRepas($pdo);
            echo json_encode($repas);
        }
        break;

    case 'POST':
        // Créer un nouveau repas
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['QUANTITE']) && isset($data['DATE'])) {
            $newRepasId = createRepas($pdo, $data['QUANTITE'], $data['DATE']);
            http_response_code(201);  // 201 Created
            echo json_encode(['id' => $newRepasId, 'message' => 'Repas created successfully']);
        } else {
            http_response_code(400);  // 400 Bad Request
            echo json_encode(['message' => 'Invalid input']);
        }
        break;

    case 'PUT':
        // Mettre à jour un repas existant
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id']) && isset($data['QUANTITE']) && isset($data['DATE'])) {
            updateRepas($pdo, $data['id'], $data['QUANTITE'], $data['DATE']);
            http_response_code(200);  // 200 OK
            echo json_encode(['message' => 'Repas updated successfully']);
        } else {
            http_response_code(400);  // 400 Bad Request
            echo json_encode(['message' => 'Invalid input']);
        }
        break;
    
    case 'DELETE':
        // Supprimer un repas
        if (isset($_GET['id'])) {
            deleteRepas($pdo, $_GET['id']);
            http_response_code(200);  // 200 OK
            echo json_encode(['message' => 'Repas deleted successfully']);
        } else {
            http_response_code(400);  // 400 Bad Request
            echo json_encode(['message' => 'Repas ID is required']);
        }
        break;

    default:
        http_response_code(405);  // 405 Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>

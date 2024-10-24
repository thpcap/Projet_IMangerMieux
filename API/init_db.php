<?php
require_once('ConfigAPI.php');

// Connexion à la base de données via PDO
function connectDB() {
    try {
        $connectionString = "mysql:host=" . _MYSQL_HOST . ";port=" . _MYSQL_PORT . ";dbname=" . _MYSQL_DBNAME;
        $pdo = new PDO($connectionString, _MYSQL_USER, _MYSQL_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

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

// Définir les en-têtes HTTP pour les réponses JSON
function setHeaders() {
    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json; charset=utf-8');
}
?>

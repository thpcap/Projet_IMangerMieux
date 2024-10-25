<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');

    setHeaders(); // Définir les headers avant toute sortie
    session_start();
    if(isset($_SESSION['connected'])&&$_SESSION['connected']&&isset($_SESSION['login'])){
        http_response_code(401); // Non autorisé
        echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
        exit;
    }
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            if(isset($_GET['login'])){
                if($_GET['login']!==$_SESSION['login']){
                    http_response_code(401); // Non autorisé
                    echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
                    exit;
                }
                // Préparer la requête SQL pour récupérer tous les niveaux de pratique
                $stmt = $pdo->prepare("SELECT repas.ID_REPAS, repas.QUANTITE, repas.DATE, aliment.LABEL_ALIMENT 
                                        FROM repas INNER JOIN aliment ON repas.ID_ALIMENT=aliment.ID_ALIMENT 
                                        WHERE repas.LOGIN=:login;");
                try {
                    $stmt->execute([':login'=>$_SESSION['login']]));
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats
                } catch (PDOException $e) {
                    http_response_code(500); // Erreur serveur interne
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                    exit;
                }
            }else{
                // Si le login n'est pas fourni
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Le login est requis.']);
                exit;
            }
            // Renvoyer la réponse JSON avec tous les niveaux de pratique
            http_response_code(200); // OK
            echo json_encode($data);
            break;
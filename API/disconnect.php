<?php
    require_once('usefullFunctions.php');

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            session_start();
            
            // Vider la session
            $_SESSION = [];
            
            // Détruire la session
            session_destroy();
            
            // Supprimer le cookie de connexion
            setcookie('login', '', time() - 3600, '/'); // Cookie expiré
            
            // Vous pouvez également supprimer d'autres cookies ici si nécessaire
            // setcookie('autre_cookie', '', time() - 3600, '/');

            http_response_code(200); // Succès
            echo json_encode(['success' => 'Utilisateur déconnecté avec succès.']);
            break; // Ajoutez ce break pour s'assurer qu'on sort du switch

        default:
            // Si une méthode autre est utilisée, renvoyer une erreur 405
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }

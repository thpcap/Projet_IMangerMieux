<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');
    setHeaders();

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            // Récupérer l'entrée JSON
            $data = json_decode(file_get_contents("php://input"));

            // Vérifier si le login et le mot de passe sont définis
            if (isset($data->login) && isset($data->motDePasse)) {
                // Échapper l'entrée pour prévenir l'injection SQL
                $login = escape_special_characters($data->login);
                $motDePasse = $data->motDePasse; // Récupération du mot de passe en clair pour comparaison

                // Préparer la requête SQL pour récupérer l'utilisateur et le mot de passe haché
                $stmt = $pdo->prepare("
                    SELECT UTILISATEUR.LOGIN, UTILISATEUR.MDP 
                    FROM UTILISATEUR 
                    WHERE UTILISATEUR.LOGIN = :login;
                ");

                try {
                    $stmt->execute([':login' => $login]);
                } catch (PDOException $e) {
                    http_response_code(500); // Erreur serveur interne
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                    exit;
                }

                // Récupérer l'utilisateur et son mot de passe haché
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    // Comparer le mot de passe saisi au mot de passe haché
                    if (password_verify($motDePasse, $user['MDP'])) {
                        // Connexion réussie
                        session_start();
                        $_SESSION['login'] = $login;
                        $_SESSION['connected'] = true;
                        setcookie('login', $login, 0, "/");
                        http_response_code(200); // OK
                        echo json_encode(['connected' => true]);
                    } else {
                        // Mot de passe incorrect
                        http_response_code(401); // Non autorisé
                        echo json_encode(['connected' => false, 'error' => 'Mot de passe incorrect.']);
                    }
                } else {
                    // Utilisateur non trouvé
                    http_response_code(401); // Non autorisé
                    echo json_encode(['connected' => false, 'error' => 'Utilisateur non trouvé.']);
                }
            } else {
                // Login ou mot de passe manquant
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Le login et le mot de passe sont requis.']);
            }
            break;

        default:
            // Méthode non autorisée
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }
?>

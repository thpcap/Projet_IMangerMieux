<?php
    require_once('init_PDO.php');
    require_once('usefullFunctions.php');

    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            // Récupérer l'entrée JSON
            $data = json_decode(file_get_contents("php://input"));
            $set= isset($data->login) && isset($data->motDePasse) && isset($data->email) && isset($data->nom) && isset($data->prenom) && isset($data->sexe) && isset($data->niveauPratique) && isset($data->date);

            // Vérifier que toutes les données requises sont présentes
            if ($set) {
                // Échapper l'entrée pour prévenir l'injection SQL
                $nom = escape_special_characters($data->nom);
                $prenom = escape_special_characters($data->prenom);
                $email = escape_special_characters($data->email);
                $date = escape_special_characters($data->date);
                $sexe = escape_special_characters($data->sexe);
                $niveauPratique = escape_special_characters($data->niveauPratique);
                $login = escape_special_characters($data->login);
                $motDePasse = $data->motDePasse;

                // Vérifier que la date est valide
                $dateTime = DateTime::createFromFormat('Y-m-d', $date);
                if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'La date fournie est invalide.']);
                    exit;
                }


                // Vérifier que l'adresse e-mail est valide
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'adresse e-mail est invalide.']);
                    exit;
                }

                // Vérifier que l'adresse e-mail n'existe pas déjà
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE MAIL = :email");
                $stmt->execute([':email' => $email]);
                $emailExists = $stmt->fetchColumn();

                if ($emailExists > 0) {
                    // L'e-mail existe déjà
                    http_response_code(409); // Conflit
                    echo json_encode(['error' => 'L\'adresse e-mail existe déjà.']);
                    exit;
                }

                // Vérifier que le login n'existe pas déjà
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE LOGIN = :login");
                $stmt->execute([':login' => $login]);
                $loginExists = $stmt->fetchColumn();

                if ($loginExists > 0) {
                    // Le login existe déjà
                    http_response_code(409); // Conflit
                    echo json_encode(['error' => 'Le login existe deja.']);
                    exit;
                }

                // Vérifier que l'ID du sexe existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM sexe WHERE ID_SEXE = :sexe");
                $stmt->execute([':sexe' => $sexe]);
                $sexeExists = $stmt->fetchColumn();

                if ($sexeExists == 0) {
                    // L'ID du sexe est invalide
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'ID du sexe est invalide.']);
                    exit;
                }

                // Vérifier que l'ID du niveau de pratique existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM niveau_de_pratique WHERE ID_PRATIQUE = :niveauPratique");
                $stmt->execute([':niveauPratique' => $niveauPratique]);
                $niveauPratiqueExists = $stmt->fetchColumn();

                if ($niveauPratiqueExists == 0) {
                    // L'ID du niveau de pratique est invalide
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'ID du niveau de pratique est invalide.']);
                    exit;
                }

                // Préparer la requête SQL pour l'insertion
                $stmt = $pdo->prepare("
                    INSERT INTO utilisateur (NOM, PRENOM, DATE_DE_NAISSANCE, MAIL, ID_SEXE, ID_PRATIQUE, LOGIN, MDP, ID_AGE) 
                    VALUES (
                        :nom,
                        :prenom,
                        :date,
                        :email,
                        :sexe,
                        :niveauPratique,
                        :login,
                        :motDePasse,
                        (SELECT tranches_d_age.ID_AGE 
                         FROM tranches_d_age 
                         WHERE :date BETWEEN 
                               COALESCE(DATE_SUB(NOW(), INTERVAL tranches_d_age.MAX_AGE YEAR), '1900-01-01') 
                         AND 
                               COALESCE(DATE_SUB(NOW(), INTERVAL tranches_d_age.MIN_AGE YEAR), NOW()))
                    );
                ");

                try {
                    // Exécuter la requête d'insertion
                    $stmt->execute([
                        ':nom' => $nom,
                        ':prenom' => $prenom,
                        ':date' => $date,
                        ':email' => $email,
                        ':sexe' => $sexe,
                        ':niveauPratique' => $niveauPratique,
                        ':login' => $login,
                        ':motDePasse' => $motDePasse
                    ]);
                } catch (PDOException $e) {
                    http_response_code(500); // Erreur serveur interne
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                    exit;
                }

                // Insertion réussie, retourner la réponse appropriée
                setHeaders();
                session_start();
                $_SESSION['login'] = $login; // Stocker le login échappé dans la session
                $_SESSION['connected'] = true;
                setcookie('login',$login, 0, "/");
                http_response_code(201); // Ressource créée
                echo json_encode(['connected' => true]);
            } else {
                // Informations manquantes dans la requête
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Toutes les informations sont requises.']);
            }
            break;

        default:
            // Méthode non autorisée
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }

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
                    echo json_encode(['error' => 'Accès interdit. Vous ne pouvez pas accéder aux informations d\'autres UTILISATEURs.']);
                    exit;
                }

                // Préparer la requête SQL pour récupérer les informations UTILISATEUR
                $stmt = $pdo->prepare("
                    SELECT 
                        UTILISATEUR.LOGIN, 
                        UTILISATEUR.NOM, 
                        UTILISATEUR.PRENOM, 
                        UTILISATEUR.DATE_DE_NAISSANCE, 
                        UTILISATEUR.MAIL, 
                        UTILISATEUR.ID_SEXE, 
                        UTILISATEUR.ID_AGE, 
                        UTILISATEUR.ID_PRATIQUE 
                    FROM 
                        UTILISATEUR 
                    WHERE 
                        UTILISATEUR.LOGIN = :login;
                ");

                try {
                    // Exécuter la requête avec le login de la session
                    $stmt->execute([':login' => $_SESSION['login']]);
                    $dataRes = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats

                    // Vérification si l'UTILISATEUR a été trouvé
                    if (empty($dataRes)) {
                        http_response_code(404); // Non trouvé
                        echo json_encode(['error' => 'Utilisateur non trouvé.']);
                        exit;
                    }
                    
                    $dateNaissance = $dataRes[0]['DATE_DE_NAISSANCE'];
                    // Calculer l'ID d'âge à partir de la date de naissance
                    $stmtAge = $pdo->prepare("
                        SELECT ID_AGE 
                        FROM TRANCHES_D_AGE 
                        WHERE :dateNaissance BETWEEN 
                            COALESCE(DATE_SUB(NOW(), INTERVAL MAX_AGE YEAR), '1900-01-01') 
                            AND 
                            COALESCE(DATE_SUB(NOW(), INTERVAL MIN_AGE YEAR), NOW())
                    ");
                    $stmtAge->execute([':dateNaissance' => $dateNaissance]);
                    $idAge = $stmtAge->fetchColumn();

                    if ($idAge === false) {
                        http_response_code(400); // Mauvaise requête
                        echo json_encode(['error' => 'L\'ID d\'âge ne peut pas être déterminé à partir de la date de naissance.']);
                        exit;
                    }

                    // Changer l'ID_AGE
                    if ($dataRes[0]['ID_AGE'] != $idAge) {
                        // Mettre à jour l'ID d'âge dans la base de données
                        $updateStmt = $pdo->prepare("
                            UPDATE UTILISATEUR 
                            SET ID_AGE = :idAge 
                            WHERE LOGIN = :login
                        ");
                        $updateStmt->execute([':idAge' => $idAge, ':login' => $_SESSION['login']]);
                    }

                    // Mettre à jour le tableau de réponse avec le nouvel ID d'âge
                    $dataRes[0]['ID_AGE'] = $idAge;

                    // Renvoyer la réponse JSON avec les informations UTILISATEUR
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
        case 'PUT':
            // Récupérer l'entrée JSON
            $data = json_decode(file_get_contents("php://input"));
            $set = isset($data->login) && isset($data->email) && isset($data->nom) && isset($data->prenom) && isset($data->sexe) && isset($data->niveauPratique) && isset($data->date);

            // Vérifier que toutes les données requises sont présentes
            if ($set) {
                // Échapper les données pour éviter l'injection SQL
                $login = escape_special_characters($data->login);
                $nom = escape_special_characters($data->nom);
                $prenom = escape_special_characters($data->prenom);
                $email = escape_special_characters($data->email);
                $date = escape_special_characters($data->date);
                $SEXE = escape_special_characters($data->sexe);
                $niveauPratique = escape_special_characters($data->niveauPratique);

                // Vérifier la validité de la date
                $dateTime = DateTime::createFromFormat('Y-m-d', $date);
                if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'La date fournie est invalide.']);
                    exit;
                }
                
                // Vérifier que la date de naissance est antérieure à la date actuelle
                if ($dateTime >= new DateTime()) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'La date doit être antérieure à la date actuelle.']);
                    exit;
                }

                // Vérifier que l'adresse e-mail est valide
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'adresse e-mail est invalide.']);
                    exit;
                }

                // Vérifier si l'UTILISATEUR avec le login existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM UTILISATEUR WHERE LOGIN = :login");
                $stmt->execute([':login' => $login]);
                $userExists = $stmt->fetchColumn();

                if ($userExists == 0) {
                    http_response_code(404); // Utilisateur non trouvé
                    echo json_encode(['error' => 'Utilisateur non trouvé.']);
                    exit;
                }

                // Vérifier que l'ID du SEXE existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM SEXE WHERE ID_SEXE = :SEXE");
                $stmt->execute([':SEXE' => $SEXE]);
                $SEXEExists = $stmt->fetchColumn();

                if ($SEXEExists == 0) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'ID du SEXE est invalide.']);
                    exit;
                }

                // Vérifier que l'ID du niveau de pratique existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM NIVEAU_DE_PRATIQUE WHERE ID_PRATIQUE = :niveauPratique");
                $stmt->execute([':niveauPratique' => $niveauPratique]);
                $niveauPratiqueExists = $stmt->fetchColumn();

                if ($niveauPratiqueExists == 0) {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'L\'ID du niveau de pratique est invalide.']);
                    exit;
                }

                // Mise à jour des données de l'UTILISATEUR
                $stmt = $pdo->prepare("
                    UPDATE UTILISATEUR
                    SET 
                        NOM = :nom,
                        PRENOM = :prenom,
                        DATE_DE_NAISSANCE = :date,
                        MAIL = :email,
                        ID_SEXE = :SEXE,
                        ID_PRATIQUE = :niveauPratique
                    WHERE LOGIN = :login
                ");

                try {
                    $stmt->execute([
                        ':nom' => $nom,
                        ':prenom' => $prenom,
                        ':date' => $date,
                        ':email' => $email,
                        ':SEXE' => $SEXE,
                        ':niveauPratique' => $niveauPratique,
                        ':login' => $login
                    ]);

                    http_response_code(200); // Succès
                    echo json_encode(['success' => 'Utilisateur mis à jour avec succès.']);

                } catch (PDOException $e) {
                    http_response_code(500); // Erreur serveur interne
                    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
                }

            } else {
                http_response_code(400); // Mauvaise requête
                echo json_encode(['error' => 'Toutes les informations sont requises.']);
            }
            break;
            case 'DELETE':
                // Supprimer un UTILISATEUR
                $data = json_decode(file_get_contents("php://input"));
            
                if (isset($data->login) && isset($data->motDePasse)) {
                    $login = escape_special_characters($data->login);
                    $password = $data->motDePasse; // Mot de passe brut pour la vérification
            
                    // Vérifier que l'UTILISATEUR est en train de supprimer son propre compte
                    if ($login !== $_SESSION['login']) {
                        http_response_code(403); // Interdit
                        echo json_encode(['error' => 'Accès interdit. Vous ne pouvez pas supprimer un autre compte UTILISATEUR.']);
                        exit;
                    }
            
                    // Rechercher l'UTILISATEUR et récupérer le mot de passe haché
                    $stmt = $pdo->prepare("SELECT UTILISATEUR.MDP FROM UTILISATEUR WHERE UTILISATEUR.LOGIN = :login");
                    $stmt->execute([':login' => $login]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if ($user) {
                        // Comparer le mot de passe fourni avec le hachage stocké
                        if (password_verify($password, $user['MDP'])) {
                            // Le mot de passe est correct, procéder à la suppression
                            $stmt = $pdo->prepare("DELETE FROM UTILISATEUR WHERE LOGIN = :login");
                            $stmt->execute([':login' => $login]);
            
                            // Fermer la session et envoyer une réponse de succès
                            $_SESSION = [];
                            session_destroy();
                            setcookie('login', '', time() - 3600, '/');
                            http_response_code(200); // Succès
                            echo json_encode(['success' => 'Utilisateur supprimé avec succès.']);
                        } else {
                            // Mot de passe incorrect
                            http_response_code(403); // Interdit
                            echo json_encode(['error' => 'Mot de passe incorrect.']);
                        }
                    } else {
                        // L'UTILISATEUR n'a pas été trouvé
                        http_response_code(404); // Non trouvé
                        echo json_encode(['error' => 'Utilisateur non trouvé.']);
                    }
            
                } else {
                    http_response_code(400); // Mauvaise requête
                    echo json_encode(['error' => 'Le login et le mot de passe sont requis.']);
                }
                break;            

        default:
            // Si une méthode autre  est utilisée, renvoyer une erreur 405
            http_response_code(405); // Méthode non autorisée
            echo json_encode(['error' => 'Méthode non autorisée.']);
            break;
    }

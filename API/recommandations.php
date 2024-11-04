<?php 
require_once('init_PDO.php');
require_once('usefullFunctions.php');

setHeaders();
session_start();

if (!isset($_SESSION['connected']) || !$_SESSION['connected'] || !isset($_SESSION['login'])) {
    http_response_code(401); // Non autorisé
    echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette ressource.']);
    exit;
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'GET':
        // Récupérer les informations utilisateur depuis la base de données
        $stmt = $pdo->prepare(
            "SELECT ID_SEXE, DATE_DE_NAISSANCE, ID_PRATIQUE FROM utilisateur WHERE LOGIN = :login"
        );

        try {
            $stmt->execute([':login' => $_SESSION['login']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userData) {
                http_response_code(404);
                echo json_encode(['error' => 'Utilisateur non trouvé']);
                exit;
            }
            
            $sexe = $userData['ID_SEXE'];
            $dateNaissance = $userData['DATE_DE_NAISSANCE']; 
            $niveauActivite = $userData['ID_PRATIQUE'];

            // Calculer l'âge et l'ID de tranche d'âge
            $age = calculerAge($dateNaissance);

            // Calculer les besoins nutritionnels
            $recommandations = calculRecommandations($age, $sexe, $niveauActivite);

            http_response_code(200);
            echo json_encode($recommandations);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}

// Fonction pour calculer l'âge
function calculerAge($dateNaissance) {
    $dateNaissance = new DateTime($dateNaissance);
    $aujourdhui = new DateTime();
    $age = $aujourdhui->diff($dateNaissance)->y;
    return $age;
}

// Fonction pour calculer les recommandations nutritionnelles
function calculRecommandations($age, $sexe, $niveauActivite) {
    $recommandations = [];

    // Calcul des recommandations
    $recommandations['eau'] = 2 + 0.1 * $niveauActivite;
    $recommandations['energie'] = ($sexe === 'homme') ? 2500 + ($niveauActivite * 200) : 2000 + ($niveauActivite * 150);
    $recommandations['proteines'] = $age;
    $recommandations['glucides'] = $recommandations['energie'] * 0.55 / 4;
    $recommandations['sel'] = 5 * $niveauActivite;

    return $recommandations;
}
?>

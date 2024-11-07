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

try {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            $inputData = json_decode(file_get_contents("php://input"), true);

            // Récupérer les données
            $sexe = $inputData['sexe'] ?? null;
            $poids = $inputData['poids'] ?? null;
            $taille = $inputData['taille'] ?? null;
            $age = $inputData['age'] ?? null;
            $niveauActivite = $inputData['niveauActivite'] ?? null;
            $nutrient = $inputData['nutrient'] ?? null;

            if (empty($sexe) || empty($poids) || empty($taille) || empty($age) || empty($niveauActivite) || empty($nutrient)) {
                http_response_code(400);
                echo json_encode(['error' => 'Données manquantes ou invalides']);
                exit;
            }

            if (!is_numeric($poids) || !is_numeric($taille) || !is_numeric($age) || !is_numeric($niveauActivite)) {
                http_response_code(400);
                echo json_encode(['error' => 'Les valeurs numériques doivent être valides']);
                exit;
            }

            // Calcul des besoins nutritionnels en fonction du nutriment sélectionné
            $recommandations = [];

            switch ($nutrient) {
                case 'eau':
                    // Selon l'OMS, l'apport en eau recommandé est d'environ 2 à 3 litres par jour pour les adultes.
                    // On considère un apport de base de 2,7 L/jour pour les femmes et 3,7 L/jour pour les hommes, ajusté selon l'activité.
                    $recommandations['Eau'] = ($sexe === 'homme') ? 3.7 : 2.7;
                    $recommandations['Eau'] += 0.1 * $niveauActivite; // Ajustement basé sur le niveau d'activité.
                    $recommandations['Eau']=$recommandations['Eau']/1000;
                    break;
                case 'energie':
                    // Apport énergétique basé sur les besoins moyens d'entretien selon l'âge, le sexe et l'activité physique.
                    // Exemple de formule simplifiée en kcal/jour :
                    if ($sexe === 'homme') {
                        $recommandations['Energie'] = 88.36 + (13.4 * $poids) + (4.8 * $taille) - (5.7 * $age);
                    } else {
                        $recommandations['Energie'] = 447.6 + (9.2 * $poids) + (3.1 * $taille) - (4.3 * $age);
                    }
                    $recommandations['Energie'] *= (1.2 + (0.1 * $niveauActivite)); // Facteur d'ajustement pour le niveau d'activité.
                    break;
                case 'proteines':
                    // Selon l'OMS, l'apport recommandé en protéines est de 0,8 g/kg de poids corporel pour les adultes.
                    $recommandations['Proteines'] = $poids * 0.8;
                    break;
                case 'glucides':
                    // L'OMS recommande que 55-75% de l'apport énergétique total provienne des glucides.
                    // Ici, nous prenons un ratio de 60% pour l'exemple :
                    $ratioGlucides = 0.60;
                    $recommandations['Glucides'] = ($recommandations['energie'] * $ratioGlucides) / 4; // 1 g de glucides = 4 kcal.
                    break;
                case 'sel':
                    // L'OMS recommande de limiter l'apport en sel à moins de 5 g/jour.
                    $recommandations['Sel chlorure de sodium'] = 5;
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Nutriment non valide']);
                    exit;
            }
            
            foreach ($recommandations as $nutrient => $besoin) {
                // Requête pour insérer ou mettre à jour chaque besoin de nutriment
                $sql = "INSERT INTO A_BESOINS (ID_NUTRIMENT, LOGIN, BESOINS)
                        VALUES ((SELECT ID_NUTRIMENT FROM NUTRIMENTS WHERE LIBELE_NUTRIMENT = :nutrient), :login, :besoin)
                        ON DUPLICATE KEY UPDATE BESOINS = :besoin";
            
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nutrient' => $nutrient,
                    ':login' => $_SESSION['login'],
                    ':besoin' => $besoin
                ]);
            }
            

            http_response_code(200);
            echo json_encode($recommandations);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
    exit;
}
?>

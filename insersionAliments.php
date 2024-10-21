<?php
define('_MYSQL_HOST', '127.0.0.1');
define('_MYSQL_PORT', 3306);
define('_MYSQL_DBNAME', 'imangermieux');
define('_MYSQL_USER', 'root');
define('_MYSQL_PASSWORD', 'root');

// Afficher les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connectionString = "mysql:host=" . _MYSQL_HOST;
if (defined('_MYSQL_PORT'))
    $connectionString .= ";port=" . _MYSQL_PORT;
$connectionString .= ";dbname=" . _MYSQL_DBNAME;
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
$pdo = NULL;
try {
    $pdo = new PDO($connectionString, _MYSQL_USER, _MYSQL_PASSWORD, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erreur) {
    echo 'Erreur : ' . $erreur->getMessage();
}


function addto($fichier,$querry,$pdo){
        // Vérifiez si le fichier existe
    if (!file_exists($fichier)) {
        die("Le fichier n'existe pas à ce chemin : $fichier");
    }

    // Ouvrir le fichier en mode lecture
    if (($handle = fopen($fichier, 'r')) !== false) {
        // Lire la première ligne du fichier CSV
        $tab = fgetcsv($handle, 1000, ";"); // Cela lit la ligne et renvoie un tableau

        // Préparer la requête SQL
        $stmt = $pdo->prepare($querry);

        foreach ($tab as $value) {
            // Exécutez la requête préparée avec la valeur
            $stmt->execute([':libele_type' => $value]);
        }

        // Fermer le fichier
        fclose($handle);
        echo "Data inputted successfully.";
    } else {
        echo "Erreur lors de l'ouverture du fichier.";
    }
}
// Chemin vers le fichier CSV
$fichier = 'Donnes_A_ajouter/types d\'aliments.csv';
$querry="INSERT INTO type_aliment (LIBELE_TYPE) VALUES (:libele_type)";
addto($fichier, $querry,$pdo);

$fichier = 'Donnes_A_ajouter/nutriments.csv';
$querry="INSERT INTO nutriments(LIBELE_NUTRIMENT) VALUES (:libele_type)";
addto($fichier, $querry,$pdo);

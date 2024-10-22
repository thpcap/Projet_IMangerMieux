<?php 
    require_once("init_PDO.php");
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

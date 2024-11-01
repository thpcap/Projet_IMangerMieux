<?php

    require_once("../init_PDO.php");

    function addto($fichier, $querry, $pdo) {
        if (!file_exists($fichier)) {
            die("Le fichier n'existe pas à ce chemin : $fichier");
        }

        if (($handle = fopen($fichier, 'r')) !== false) {
            $stmt = $pdo->prepare($querry);

            while (($tab = fgetcsv($handle, 1000, ";")) !== false) {
                foreach ($tab as $value) {
                    try {
                        $stmt->execute([':libele_type' => $value]);
                    } catch (PDOException $e) {
                        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
                    }
                }
            }

            fclose($handle);
            echo "Données insérées avec succès.\n";
        } else {
            echo "Erreur lors de l'ouverture du fichier.\n";
        }
    }

    // Insertion des types d'aliments
    $fichier = 'Donnes_A_ajouter/types d\'aliments.csv';
    $querry = "INSERT INTO TYPE_ALIMENT (LIBELE_TYPE) VALUES (:libele_type)";
    addto($fichier, $querry, $pdo);

    // Insertion des NUTRIMENTS
    $fichier = 'Donnes_A_ajouter/NUTRIMENTS.csv';
    $querry = "INSERT INTO NUTRIMENTS (LIBELE_NUTRIMENT) VALUES (:libele_type)";
    addto($fichier, $querry, $pdo);

    // Insertion des aliments
    $file = 'Donnes_A_ajouter/aliments.csv';
    if (($handle = fopen($file, 'r')) !== FALSE) {
        $header = fgetcsv($handle, 1000, ';'); // Lecture de la première ligne comme en-tête

        // Préparation des requêtes SQL
        $insertAlimentStmt = $pdo->prepare("
            INSERT INTO ALIMENT (ID_TYPE, LABEL_ALIMENT) 
            VALUES ((SELECT ID_TYPE FROM TYPE_ALIMENT WHERE LIBELE_TYPE = :libele_type), :label_aliment)
        ");
        $insertContientStmt = $pdo->prepare("
            INSERT INTO CONTIENT (ID_ALIMENT, ID_NUTRIMENT, RATIOS) 
            VALUES (:id_aliment, :id_nutriment, :ratios)
        ");

        // Parcourir chaque ligne du fichier
        while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
            // Extraction des deux premières colonnes : Type d'aliment et Nom de l'aliment
            $typeAliment = $row[0]; // Libellé du type d'aliment (ex: "Fruit")
            $nomAliment = $row[1];  // Nom d'aliment (ex: "Pomme")

            // Vérifier si le type d'aliment existe
            $stmt = $pdo->prepare("SELECT ID_TYPE FROM TYPE_ALIMENT WHERE LIBELE_TYPE = :libele_type");
            $stmt->execute([':libele_type' => $typeAliment]);
            $idType = $stmt->fetchColumn();

            if ($idType === false) {
                echo "Le type d'aliment '$typeAliment' n'existe pas dans la base de données.\n";
            } else {
                // Insertion de l'aliment en récupérant l'ID_TYPE depuis le libellé du type d'aliment
                try {
                    $insertAlimentStmt->execute([
                        ':libele_type' => $typeAliment,
                        ':label_aliment' => $nomAliment
                    ]);

                    $idAliment = $pdo->lastInsertId();  // Récupérer l'ID de l'aliment inséré

                    // Parcourir les NUTRIMENTS à partir de la 3ème colonne
                    foreach ($row as $index => $nutrientValue) {
                        if ($index > 1 && !empty($nutrientValue)) {
                            $nutrientName = $header[$index]; // Nom du nutriment depuis l'en-tête
                            $nutrientValue = floatval($nutrientValue);
                            if($nutrientName==""){
                                $nutrientName='null';
                            }
                            // Vérifier si le nutriment existe dans la base de données
                            $nutrientStmt = $pdo->prepare("SELECT ID_NUTRIMENT FROM NUTRIMENTS WHERE LIBELE_NUTRIMENT = :libele_nutriment");
                            $nutrientStmt->execute([':libele_nutriment' => $nutrientName]);
                            $idNutriment = $nutrientStmt->fetchColumn();

                            if ($idNutriment === false) {
                                echo "Le nutriment '$nutrientName' n'existe pas dans la base de données.\n";
                            } else {
                                // Insertion dans la table CONTIENT
                                try {
                                    $insertContientStmt->execute([
                                        ':id_aliment' => $idAliment,
                                        ':id_nutriment' => $idNutriment,
                                        ':ratios' => $nutrientValue
                                    ]);
                                } catch (PDOException $e) {
                                    echo "Erreur lors de l'insertion du nutriment '$nutrientName' pour l'aliment '$nomAliment': " . $e->getMessage() . "\n";
                                }
                            }
                        }
                    }
                } catch (PDOException $e) {
                    echo "Erreur lors de l'insertion de l'aliment '$nomAliment': " . $e->getMessage() . "\n";
                }
            }
        }

        fclose($handle);
        echo "Données d'aliments insérées avec succès.\n";
    } else {
        echo "Impossible d'ouvrir le fichier aliments.csv.";
    }

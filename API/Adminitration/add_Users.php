<?php
    require_once("C:\UwAmp\www\Projet_IMangerMieux\API\init_PDO.php"); // Assurez-vous d'avoir une connexion PDO

    // Fonction pour générer un âge aléatoire basé sur les tranches d'âge
    function getRandomAgeRange($pdo) {
        $stmt = $pdo->query("SELECT ID_AGE FROM TRANCHES_D_AGE");
        $ageRanges = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $ageRanges[array_rand($ageRanges)]; // Retourne une ID de tranche d'âge aléatoire
    }

    // Fonction pour générer une pratique sportive aléatoire
    function getRandomPractice($pdo) {
        $stmt = $pdo->query("SELECT ID_PRATIQUE FROM NIVEAU_DE_PRATIQUE");
        $practices = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $practices[array_rand($practices)]; // Retourne une ID de pratique aléatoire
    }

    // Fonction pour générer un mot de passe aléatoire
    function generateRandomPassword($length = 12) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }

    // Lecture du fichier CSV
    $fichier = 'C:\UwAmp\www\Projet_IMangerMieux\API\Adminitration\Donnes_A_ajouter\aliases.csv'; // Remplacez par le chemin de votre fichier CSV
    if (($handle = fopen($fichier, 'r')) !== FALSE) {
        $header = fgetcsv($handle, 1000, ","); // Lire l'en-tête
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $alias = $row[0];
            $name = $row[1];
            $sexe = $row[count($row) - 1]; // Dernière colonne
            $login = strtolower($name) . '-' . strtolower($alias); // Exemple de login
            $email = strtolower($login) . '@' . strtolower($alias) . '.com'; // Exemple d'email
            $pratique = getRandomPractice($pdo); // Pratique sportive aléatoire
            $age = getRandomAgeRange($pdo); // Tranche d'âge aléatoire
            
            // Génération de mot de passe (par exemple, mot de passe par défaut)
            $mdp = generateRandomPassword(); // Assurez-vous de stocker les mots de passe de manière sécurisée

            // Préparation de l'insertion dans la table UTILISATEUR
            $query = "INSERT INTO UTILISATEUR (LOGIN, ID_SEXE, ID_AGE, ID_PRATIQUE, MDP, NOM, PRENOM, DATE_DE_NAISSANCE, MAIL) 
                    VALUES (:login, (SELECT ID_SEXE FROM SEXE WHERE LIBELE_SEXE_COURT = :sexe), :age, :pratique, :mdp, :name, :alias, CURDATE(), :email)";
            $stmt = $pdo->prepare($query);
            try {
                $stmt->execute([
                    ':login' => $login,
                    ':sexe' => $sexe,
                    ':age' => $age,
                    ':pratique' => $pratique,
                    ':mdp' => $mdp,
                    ':name' => $name,
                    ':alias' => $alias,
                    ':email' => $email
                ]);
                echo "Utilisateur ajouté : $login\n";
            } catch (PDOException $e) {
                echo "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage() . "\n";
            }
        }
        fclose($handle);
    } else {
        echo "Impossible d'ouvrir le fichier.";
    }

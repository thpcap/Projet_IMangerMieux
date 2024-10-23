<!DOCTYPE html>
<html>
    <head>
        <title>Creer un compte</title>
        <link rel="icon" href="">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="CSS/stylesLogin.css">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="centerd">
            <form id='login' onsubmit="createUser();">
                <table>
                    <tbody>
                        <tr>
                            <th colspan="2"><h1>Créer un Compte</h1></th>
                        </tr>
                        <tr>
                            <th colspan="2"><p id="error"></p></th>
                        </tr>
                        <tr>
                            <th>Nom</th>
                            <td><input id="inputNom" type="text" required></td>
                        </tr>
                        <tr>
                            <th>Prénom</th>
                            <td><input id="inputPrenom" type="text" required></td>
                        </tr>
                        <tr>
                            <th>Date De Naissance</th>
                            <td><input id="inputDate" type="date" required></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><input id="inputEmail" type="email" required></td>
                        </tr>
                        <tr>
                            <th>Sexe</th>
                            <td><select id="selectSexes" type="text" required></select></td>
                        </tr>
                        <tr>
                            <th>Niveaux De Pratique</th>
                            <td><select id="selectNiveauxPratiques" type="text" required></select></td>
                        </tr>
                        <tr>
                            <th>Login</th>
                            <td><input id="inputLogin" type="text" required></td>
                        </tr>
                        <tr>
                            <th>Mot De Passe</th>
                            <td><input id="Mot_De_Passe" type="password" required></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input id="submit" type="submit" value="Créer un Compte"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href=
                                    <?php 
                                        require_once("ConfigFrontEnd.php");
                                        echo '"'.URL_Login.'"';
                                    ?>
                                >Retour</a>
                            </td>
                        </tr>
                    </tbody>
                </table>                
            </form>
        </div>
        <script>
            $(document).ready(function(){
                //ajout des potions de sexe
                let request=$.ajax({
                    url: "<?php require_once("ConfigFrontEnd.php"); echo URL_API ?>/sexes.php",
                    method: "GET",
                    contentType: "application/json"
                });
                request.done(function(reponse){
                    if (Array.isArray(reponse)) {
                        // Sélectionne le <select> où ajouter les options
                        const $select = $('#selectSexes');
                        
                        // Vider le select au cas où il y aurait déjà des options
                        $select.empty();
                        
                        // Boucle sur chaque élément de la réponse JSON
                        $.each(reponse, function(index, sexe) {
                            // Créer une nouvelle option pour chaque sexe
                            let option = $('<option>', {
                                value: sexe.ID_SEXE, // Valeur de l'option (ID_SEXE)
                                text: sexe.LIBELE_SEXE // Texte affiché (LIBELE_SEXE)
                            });
                            // Ajouter l'option dans le <select>
                            $select.append(option);
                        });
                    } else {
                        console.log("Aucune donnée disponible pour les sexes.");
                    }
                });
                request.fail(function(xhr, status, error){
                    console.error("Erreur lors de la requête AJAX : " + textStatus, errorThrown);
                });
                // Ajout des options de niveau de pratique
                let requestNiveauxPratiques = $.ajax({
                    url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/niveaux_de_pratique.php", 
                    method: "GET",
                    contentType: "application/json"
                });
                requestNiveauxPratiques.done(function(reponse) {
                    if (Array.isArray(reponse)) {
                        // Sélectionne le <select> où ajouter les options
                        const $selectNiveau = $('#selectNiveauxPratiques');
                        
                        // Vider le select au cas où il y aurait déjà des options
                        $selectNiveau.empty();
                        
                        // Boucle sur chaque élément de la réponse JSON
                        $.each(reponse, function(index, niveau) {
                            // Créer une nouvelle option pour chaque niveau de pratique
                            let option = $('<option>', {
                                value: niveau.ID_PRATIQUE, // Valeur de l'option (ID_PRATIQUE)
                                text: niveau.LIBELE_PRATIQUE // Texte affiché (LIBELE_PRATIQUE)
                            });
                            // Ajouter l'option dans le <select>
                            $selectNiveau.append(option);
                        });
                    } else {
                        console.log("Aucune donnée disponible pour les niveaux de pratique.");
                    }
                });
                requestNiveauxPratiques.fail(function(xhr, status, error) {
                    console.error("Erreur lors de la requête AJAX : " + status, error);
                });
            });

            function createUser() {
                event.preventDefault(); // Empêche l'envoi du formulaire par défaut
                const login = $('#inputLogin').val();
                const mdp = $('#Mot_De_Passe').val();
                const nom = $('#inputNom').val();
                const prenom = $('#inputPrenom').val();
                const date = $('#inputDate').val();
                const email = $('#inputEmail').val();
                const sexe = $('#selectSexes').val();
                const niveauPratique = $('#selectNiveauxPratiques').val();

                let request = $.ajax({
                    url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/create_user.php",
                    method: "POST",
                    dataType: "json",
                    data: JSON.stringify({
                        login: login,
                        motDePasse: mdp,
                        nom: nom,
                        prenom: prenom,
                        date: date,
                        email: email,
                        sexe: sexe,
                        niveauPratique: niveauPratique
                    }),
                    contentType: "application/json"
                });

                request.done(function(reponse) {
                    if (!reponse.connected) {
                        // Affiche le message d'erreur dans la case d'erreur
                        $('#error').text("Erreur : " + reponse.error).show();
                    } else {
                        // Redirection sans l'historique vers la page d'accueil
                        window.location.replace("<?php require_once('ConfigFrontEnd.php'); echo URL_Acceuil; ?>");
                    }
                });

                request.fail(function(xhr, status, error) {
                    // Affiche le message d'erreur dans la case d'erreur
                    const errorMessage = xhr.responseJSON ? xhr.responseJSON.error : 'Erreur inconnue';
                    $('#error').text("Erreur : " + errorMessage).show();
                });
            }

        </script>
    </body>
</html>
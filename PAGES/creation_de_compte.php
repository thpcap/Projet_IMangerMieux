<!DOCTYPE html>
<html lang="fr">
<head>
    <title>IMangerMieux</title>
    <link rel="icon" href="../logo/upper_logo.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="CSS/stylesLogin.css">
    <link rel="stylesheet" href="CSS/stylesAnimations.css"> <!-- Fichier CSS pour les animations -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="centerd">
        <form id='login' onsubmit="createUser();">
            <table>
                <tbody>
                    <tr>
                        <th colspan="2"><h1>IMangerMieux</h1></th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <img src="../logo/upper_logo.png" alt="Logo" style="max-height: 100px; box-shadow:0 0 10px rgba(0, 0, 0, 0.2); border-radius:50%;">
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Créer un Compte</h2></th>
                    </tr>
                    <tr>
                        <th colspan="2"><p id="error" class="error-message"></p></th>
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
                        <td><select id="selectSexes" required></select></td>
                    </tr>
                    <tr>
                        <th>Niveaux De Pratique</th>
                        <td><select id="selectNiveauxPratiques" required></select></td>
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
                            <a href="<?php require_once("ConfigFrontEnd.php"); echo URL_Login; ?>">Retour</a>
                        </td>
                    </tr>
                </tbody>
            </table>                
        </form>
    </div>
    </div class="warning">
        <div style="position:fixed; bottom:0; background-color:white; border-radius:20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);padding:5px">
        <p style="color:red"><strong>Attention ce site n'est pas sécurisé, N'utilisez pas vos veritables donnés personelles</strong></p>
    </div>
    <script>
        $(document).ready(function(){
            // ajout des options de sexe
            let request = $.ajax({
                url: "<?php require_once("ConfigFrontEnd.php"); echo URL_API ?>/sexes.php",
                method: "GET",
                contentType: "application/json"
            });
            request.done(function(reponse){
                if (Array.isArray(reponse)) {
                    const $select = $('#selectSexes').empty();
                    $.each(reponse, function(index, sexe) {
                        let option = $('<option>', {
                            value: sexe.ID_SEXE,
                            text: sexe.LIBELE_SEXE
                        });
                        $select.append(option);
                    });
                } else {
                    console.log("Aucune donnée disponible pour les sexes.");
                }
            });
            request.fail(function(xhr, status, error){
                console.error("Erreur lors de la requête AJAX : " + status, error);
            });

            // ajout des options de niveau de pratique
            let requestNiveauxPratiques = $.ajax({
                url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/niveaux_de_pratique.php",
                method: "GET",
                contentType: "application/json"
            });
            requestNiveauxPratiques.done(function(reponse) {
                if (Array.isArray(reponse)) {
                    const $selectNiveau = $('#selectNiveauxPratiques').empty();
                    $.each(reponse, function(index, niveau) {
                        let option = $('<option>', {
                            value: niveau.ID_PRATIQUE,
                            text: niveau.LIBELE_PRATIQUE
                        });
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
                    $('#error').text("Erreur : " + reponse.error).fadeIn().css('color', 'red').delay(2000).fadeOut(300); // Affiche le message d'erreur
                } else {
                    window.location.replace("<?php require_once('ConfigFrontEnd.php'); echo URL_Acceuil; ?>");
                }
            });

            request.fail(function(xhr, status, error) {
                const errorMessage = xhr.responseJSON ? xhr.responseJSON.error : 'Erreur inconnue';
                $('#error').text("Erreur : " + errorMessage).fadeIn().css('color', 'red').delay(2000).fadeOut(300); // Affiche le message d'erreur
            });
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>IMangerMieux</title>
    <link rel="icon" href="">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="CSS/stylesLogin.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="centerd">
        <form id='login' onsubmit="login();">
            <table>
                <tbody>
                    <tr>
                        <th colspan="2"><h1>IMangerMieux</h1></th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <img src="../logo/upper_logo.png" alt="that's not a moon it's a space station" style="max-height: 100px; box-shadow:0 0 10px rgba(0, 0, 0, 0.2); border-radius:50%;">
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Se Connecter</h2></th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <p id="error" class="error">Login ou Mot de Passe erroné(s)</p>
                        </th>
                    </tr>
                    <tr>
                        <th>Login</th>
                        <td><input id="inputLogin" type="text"></td>
                    </tr>
                    <tr>
                        <th>Mot De Passe</th>
                        <td><input id="Mot_De_Passe" type="password"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input id="submit" type="submit" value="Se Connecter" class="button">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="<?php require_once("ConfigFrontEnd.php"); echo URL_Creation_Compte; ?>">Créer un Compte</a>
                        </td>
                    </tr>
                </tbody>
            </table>                
        </form>
        
    </div class="warning">
        <div style="position:absolute; bottom:0;">
        <p style="color:red"><strong>Attention ce site n'est pas sécurisé, N'utilisez pas vos veritables donnés personelles</strong></p>
        <p style="margin:auto; bottom: 10px; text-align:center; font-size:small;">Ce projet a été développé lors d'un cours de développement Web à <a href="https://imt-nord-europe.fr/">IMT Nord Europe</a>.</p>
    </div>
    

    <script>
        function login(){
            event.preventDefault();
            const login = $('#inputLogin').val();
            const mdp = $('#Mot_De_Passe').val();
            let request = $.ajax({
                url: "<?php require_once("ConfigFrontEnd.php"); echo URL_API ?>/login.php",
                method: "POST",
                dataType: "json",
                data: JSON.stringify({
                    login: login,
                    motDePasse: mdp
                }),
                contentType: "application/json"
            });

            request.done(function(reponse){
                if (!reponse.connected) {
                    $('#error').fadeIn(300).delay(2000).fadeOut(300); // Animation de l'erreur
                } else {
                    // Redirection sans l'historique vers la page d'accueil
                    window.location.replace("<?php require_once("ConfigFrontEnd.php"); echo URL_Acceuil; ?>");
                }
            });

            request.fail(function(xhr, status, error){
                $('#error').fadeIn(300).delay(2000).fadeOut(300); // Animation de l'erreur
            });
        }
    </script>
</body>
</html>

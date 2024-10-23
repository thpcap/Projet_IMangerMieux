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
                            <td><select id="selectSexe" type="text" required></select></td>
                        </tr>
                        <tr>
                            <th>Niveau De Pratique Sportive</th>
                            <td><input id="inputLogin" type="text" required></td>
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
            $()


            function createUser(){
                event.preventDefault();
                const login = $('#inputLogin').val();
                const mdp = $('#Mot_De_Passe').val();
                let request=$.ajax({
                    url: "<?php require_once("ConfigFrontEnd.php"); echo URL_API ?>/Login.php",
                    method: "POST",
                    dataType: "json",
                    data:JSON.stringify(
                        {
                            login:login,
                            motDePasse: mdp
                        }
                    ),
                    contentType: "application/json"
                });
                request.done(function(reponse){
                    if(!reponse.connected){
                        $('#error').show();
                    }else{
                        //redirection sans l'historique vers la page d'acceuil
                        window.location.replace("<?php require_once("ConfigFrontEnd.php");echo URL_Acceuil;?>");
                    }
                });
                request.fail(function(xhr, status, error){
                    
                });
            }
        </script>
    </body>
</html>
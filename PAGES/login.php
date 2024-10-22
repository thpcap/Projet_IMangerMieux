<!DOCTYPE html>
<html>
    <head>
        <title>Se Connecter</title>
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
                            <th colspan="2"><h1>Se Connecter</h1></th>
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
                            <td colspan="2"><input id="submit" type="submit" value="Se Connecter"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href=
                                    <?php 
                                        require_once("ConfigFrontEnd.php");
                                        echo '"'.URL_Creation_Compte.'"';
                                    ?>
                                >Créer un Compte</a>
                            </td>
                        </tr>
                    </tbody>
                </table>                
            </form>
        </div>
        <script>
            function login(){
                
            }
        </script>
    </body>
</html>
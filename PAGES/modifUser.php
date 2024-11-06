<section class="modifyUser">
    <div id="content">
        <div class="centered">
            <form id='login' onsubmit="modifyUser();">
                <table>
                    <tbody>
                        <tr>
                            <th colspan="2"><h1>Compte</h1></th>
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
                            <td><input id="inputLogin" type="text" hidden required></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <th colspan="2"><input id="submit" type="submit" value="Modifier"></th>
                                     
            </form>
            <br>
            <button id="deleteUserBtn" onclick="deleteUser()">Supprimer le compte</button>
            <br>
            <p style="color:red"><strong>Attention ce site n'est pas sécurisé</strong></p>
            <p style="color:red"><strong>N'utilisez pas vos veritables donnés personelles</strong></p>
        </div>
    </div>
    
    <script>
        $(document).ready(function(){
            // Récupération des données utilisateur et remplissage des champs
            const login = getCookie('login');

            if (!login) {
                $('#error').text('Aucun utilisateur connecté.');
                return;
            }
            $('#content').css({
                opacity: 1,
                transform: 'translateY(0)'
            });
            // Requête pour récupérer les informations de l'utilisateur
            $.ajax({
                url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/user.php?login=" + login,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    if (Array.isArray(data) && data.length > 0) {
                        // Remplir les champs avec les données récupérées
                        $('#inputNom').val(data[0].NOM);
                        $('#inputPrenom').val(data[0].PRENOM);
                        $('#inputDate').val(data[0].DATE_DE_NAISSANCE);
                        $('#inputEmail').val(data[0].MAIL);
                        $('#inputLogin').val(data[0].LOGIN);

                        // Récupérer les options pour les sexes
                        $.ajax({
                            url: "<?php echo URL_API ?>/sexes.php",
                            method: "GET",
                            dataType: "json",
                            success: function(sexes) {
                                const $select = $('#selectSexes');
                                $select.empty();
                                $.each(sexes, function(index, sexe) {
                                    let option = $('<option>', {
                                        value: sexe.ID_SEXE,
                                        text: sexe.LIBELE_SEXE
                                    });
                                    $select.append(option);
                                });
                                // Pré-sélectionner la valeur de sexe de l'utilisateur
                                $('#selectSexes').val(data[0].ID_SEXE);
                            },
                            error: function() {
                                $('#error').text("Erreur lors de la récupération des sexes.");
                            }
                        });

                        // Récupérer les options pour les niveaux de pratique
                        $.ajax({
                            url: "<?php echo URL_API ?>/niveaux_de_pratique.php",
                            method: "GET",
                            dataType: "json",
                            success: function(niveaux) {
                                const $selectNiveau = $('#selectNiveauxPratiques');
                                $selectNiveau.empty();
                                $.each(niveaux, function(index, niveau) {
                                    let option = $('<option>', {
                                        value: niveau.ID_PRATIQUE,
                                        text: niveau.LIBELE_PRATIQUE
                                    });
                                    $selectNiveau.append(option);
                                });
                                // Pré-sélectionner la valeur du niveau de pratique de l'utilisateur
                                $('#selectNiveauxPratiques').val(data[0].ID_PRATIQUE);
                            },
                            error: function() {
                                $('#error').text("Erreur lors de la récupération des niveaux de pratique.");
                            }
                        });

                    } else {
                        $('#error').text("Aucune donnée trouvée pour l'utilisateur.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de la récupération des données utilisateur : " + status + ", " + error);
                    $('#error').text("Erreur lors de la récupération des données utilisateur.");
                }
            });

        });
        $('.modifyUser').css({
            opacity: 1,
            transform: 'translateY(0)'
        });
        // Fonction de modification de l'utilisateur
        function modifyUser() {
            event.preventDefault(); // Empêche l'envoi du formulaire par défaut

            const login = $('#inputLogin').val();
            const nom = $('#inputNom').val();
            const prenom = $('#inputPrenom').val();
            const date = $('#inputDate').val();
            const email = $('#inputEmail').val();
            const sexe = $('#selectSexes').val();
            const niveauPratique = $('#selectNiveauxPratiques').val();

            $.ajax({
                url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/user.php",
                method: "PUT",
                dataType: "json",
                data: JSON.stringify({
                    login: login,
                    nom: nom,
                    prenom: prenom,
                    date: date,
                    email: email,
                    sexe: sexe,
                    niveauPratique: niveauPratique
                }),
                contentType: "application/json",
                success: function(reponse) {
                    if (!reponse.success) {
                        $('#error').text("Erreur : " + reponse.error).show();
                    } else {
                        $('#error').text("Utilisateur Modifié").show();
                    }
                },
                error: function(xhr, status, error) {
                    const errorMessage = xhr.responseJSON ? xhr.responseJSON.error : 'Erreur inconnue';
                    $('#error').text("Erreur : " + errorMessage).show();
                }
            });
        }

        // Fonction de suppression de l'utilisateur
        function deleteUser() {
            const login = $('#inputLogin').val();
            const motDePasse = prompt("Veuillez entrer votre mot de passe pour confirmer la suppression de votre compte :");

            if (motDePasse === null || motDePasse === "") {
                $('#error').text("La suppression a été annulée.");
                return;
            }

            $.ajax({
                url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/user.php",
                method: "DELETE",
                dataType: "json",
                data: JSON.stringify({
                    login: login,
                    motDePasse: motDePasse
                }),
                contentType: "application/json",
                success: function(reponse) {
                    if (!reponse.success) {
                        $('#error').text("Erreur : " + reponse.error).show();
                    } else {
                        $('#error').text("Utilisateur supprimé.").show();
                        // Rediriger l'utilisateur vers la page de déconnexion ou d'accueil après suppression
                        window.location.href = 'index.php'; // Redirige vers l'endpoint d'entrée
                    }
                },
                error: function(xhr, status, error) {
                    const errorMessage = xhr.responseJSON ? xhr.responseJSON.error : 'Erreur inconnue';
                    $('#error').text("Erreur : " + errorMessage).show();
                }
            });
        }

        // Fonction getCookie pour récupérer le cookie 'login'
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

    
        
    </script>
</section>

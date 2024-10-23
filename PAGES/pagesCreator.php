<?php 
    session_start();
    require_once('ConfigFrontEnd.php');
    if(isset($_SESSION['connected'])&&$_SESSION['connected']){
        require_once('template_header.php'); /** contenu de header */
        require_once('template_menu.php');  /** contenu menu */ 
        $currentPageId = 'accueil';

        if (isset($_GET['page'])) {
            $currentPageId = $_GET['page'];  
        }
        ?>

        <header class="header">
            <button id='logoutButton'>Deconnexion</button>
            <div id="userdata"></div>
            
            <?php
            require_once('template_menu.php');
            renderMenuToHTML($currentPageId);
            ?>
            <div id="graphique"></div>
        </header>

        <?php
        $pageToInclude = $currentPageId . ".php";
        if (is_readable($pageToInclude)) {
            require_once($pageToInclude); 
        } else {
            require_once('error.php');    
        }
    }else{
        header("Location:".URL_Login);
    }
?>   
    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
        $(document).ready(function(){
            function fetchUserData() {
                const login = getCookie('login');
                if (!login) {
                    $('#userdata').html('Aucun utilisateur connecté.'+login);
                    return;
                }
                $.ajax({
                    url: "<?php echo URL_API ?>/user.php?login="+login, // Remplacez par l'URL de votre endpoint
                    method: "GET",
                    success: function(data) {
                        // Vérifiez si les données contiennent des résultats
                        if (Array.isArray(data) && data.length > 0) {
                            // Affichez le nom et le prénom dans la div userdata
                            $('#userdata').html(`<p id='prenom'>${data[0].PRENOM}</p><p id='nom'>${data[0].NOM}</p>`);
                        } else {
                            $('#userdata').html('');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Erreur lors de la récupération des données utilisateur:', textStatus, errorThrown);
                        $('#userdata').html('Erreur lors de la récupération des données utilisateur.');
                    }
                });
            }
            fetchUserData();
            
            $('#logoutButton').on('click',
                function() {
                    event.stopPropagation(); // Empêche la propagation de l'événement
                    $.ajax({
                        url: "<?php echo URL_API ?>/disconnect.php", // Remplacez par l'URL de votre endpoint de déconnexion
                        method: "POST",
                        success: function(response) {
                            // Rediriger vers index.php après la déconnexion
                            window.location.href = "index.php";
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Erreur lors de la déconnexion:', textStatus, errorThrown);
                        }
                    });
                }
            );
            $('#userdata').on('click',
                function(){
                    window.location.href="<?php echo URL_Modif_User;?>";
                }
            );
        });
            
    </script>
    </body>
</html>




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
        <button id="menuButton" >☰ Menu</button>
        <header class="header" style="display:none">
            
            <br id="mbr">
            <button id='logoutButton'>Deconnexion</button>
            <div id="userdata"></div>
            
            <?php
            require_once('template_menu.php');
            renderMenuToHTML($currentPageId);
            ?>
            <canvas id="myChart" width="150" height="150"></canvas>
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
                    url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/user.php?login=" + login,
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
                        url: "<?php echo URL_API ?>/disconnect.php",
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
                    window.location.href="pagesCreator.php?page=modifUser";
                }
            );
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Energie', 'Protéines', 'Glucides', 'Eau', 'Sel'],
                    datasets: [{
                        label: 'Nutriments en %des AR',
                        data: [50, 60, 80, 95, 150],
                        backgroundColor: ['green', 'green', 'green', 'green', 'red']
                    }]
                },
                options: {
                    indexAxis: 'y', // This makes the bar chart horizontal
                    responsive: false, // To allow custom sizing
                    scales: {
                        x: {
                            beginAtZero: true // Ensures bars start from zero
                        }
                    },
                    width: 150, // Set the width of the chart
                    plugins: {
                        legend: {
                            labels: {
                                boxWidth: 0 // Removes the box/rectangle before the label
                            }
                        }
                    }
                }
            });
            $('#menuButton').on('click',function(){
                event.stopPropagation(); // Empêche la propagation de l'événement
                $('.header').toggle();
                
            });
        
            
        });
        
        
    
      
    </script>
    </body>
</html>



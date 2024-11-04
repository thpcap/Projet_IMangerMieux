<?php 
session_start();
require_once('ConfigFrontEnd.php');
if (isset($_SESSION['connected']) && $_SESSION['connected']) {
    require_once('template_header.php'); /** contenu de header */
    require_once('template_menu.php');  /** contenu menu */ 
    $currentPageId = 'accueil';

    if (isset($_GET['page'])) {
        $currentPageId = $_GET['page'];  
    }
    ?>
    <button id="menuButton">☰ Menu</button>
    <header class="header">
        <br id="mbr">
        <button id='logoutButton'>Déconnexion</button>
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
} else {
    header("Location:" . URL_Login);
}
?>   
<script>
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function getColorArray(data) {
        let colorArray = [];
        for (let key in data) {
            if (data[key] < 80) {
                colorArray.push('yellow');
            } else if (data[key] >= 80 && data[key] <= 120) {
                colorArray.push('lightgreen');
            } else {
                colorArray.push('red');
            }
        }
        return colorArray;
    }

    $(document).ready(function() {
        function fetchUserData() {
            const login = getCookie('login');
            if (!login) {
                $('#userdata').html('Aucun utilisateur connecté.' + login);
                return;
            }
            $.ajax({
                url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/user.php?login=" + login,
                method: "GET",
                success: function(data) {
                    if (Array.isArray(data) && data.length > 0) {
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

        function fetchChartData() {
            const login = getCookie('login');
            const date = new Date().toISOString().split('T')[0]; // Récupère la date du jour au format YYYY-MM-DD

            $.ajax({
                url: "<?php require_once('ConfigFrontEnd.php'); echo URL_API ?>/nutrients.php?login=" + login + "&date=" + date,
                method: "GET",
                success: function(data) {
                    if (data && typeof data === 'object') {
                        let labels = Object.keys(data).map(label => label.replace(/\s*\(g\/100g\)/, ''));
                        let values = Object.values(data);
                        let colors = getColorArray(data);

                        // Initialisation du graphique Chart.js
                        var ctx = document.getElementById('myChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Nutriments en % des AR',
                                    data: values,
                                    backgroundColor: colors
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            boxWidth: 0
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        console.error('Erreur: données invalides reçues');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Erreur lors de la récupération des données du graphique:', textStatus, errorThrown);
                }
            });
        }

        fetchChartData();

        $('#logoutButton').on('click', function() {
            event.stopPropagation();
            $.ajax({
                url: "<?php echo URL_API ?>/disconnect.php",
                method: "POST",
                success: function(response) {
                    window.location.href = "index.php";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Erreur lors de la déconnexion:', textStatus, errorThrown);
                }
            });
        });

        $('#userdata').on('click', function() {
            window.location.href = "pagesCreator.php?page=modifUser";
        });

        $('#menuButton').on('click', function(event) {
            event.stopPropagation(); // Prevents the click event from bubbling up
            $('.header').toggleClass('active'); // Toggle the 'active' class
            $('body').toggleClass('active'); // Toggle the 'active' class on the body
        });
    });
</script>
</body>
</html>

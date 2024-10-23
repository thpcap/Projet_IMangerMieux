<?php
    define("URL_API","http://localhost/Projet_IMangerMieux/API");//adresse du dossier API
    define("URL_Creation_Compte","creation_de_compte.php");//adresse de la page creation de compte
    define("URL_Acceuil","pagesCreator.php?page=accueil");//adresse de la page acceuil
    define("URL_Login","login.php");//adresse de la page de login
    define("URL_Modif_User","pagesCreator.php?page=modifUser");//adresse de la page de login
    //tableau qui structure le menu
    $mymenu = array(
        'accueil' => array('Accueil'),
        'mange' => array('Mange'),
        'statistiques' => array('Statistiques'),
        'recommendations' => array('Recommendations'),
        'contact'=> array('Contact')
    );
    ?>

    <!-- Partie frontend : Affichage du graphique et intégration de Plotly.js -->
    
    <div id="graphique"></div>
    
    
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script>
        var data = [{
            type: "pie",
            values: [0.5, 1.0, 1.5, 2.0, 2.5], // Exemples de ratios de besoins en protéines
            labels: ["Très bas", "Bas", "Modéré", "Élevé", "Très élevé"],
            textinfo: "label+percent",
            textposition: "inside",
            hole: .4,
            marker: {
                colors: ['#5DADE2', '#48C9B0', '#F4D03F', '#E67E22', '#EC7063']
            }
        }];
    
        var layout = {
            title: "Ratio Besoins Moyens de Protéines (g/kg/jour)",
            height: 400,
            width: 600,
            showlegend: false
        };
    
        Plotly.newPlot('graphique', data, layout);
    </script>
    



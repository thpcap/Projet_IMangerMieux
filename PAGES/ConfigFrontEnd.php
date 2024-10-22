<?php
    define("URL_API","http://localhost/Projet_IMangerMieux/API");//adresse du dossier API
    define("URL_Creation_Compte","creation_de_compte.php");//adresse de la page creation de compte
    define("URL_Acceuil","http://localhost/Projet_IMangerMieux/Pages/index.php?page=acceuil");//adresse de la page acceuil
    //tableau qui structure le menu
    $mymenu = array(
        'accueil' => array('Accueil'),
        'mange' => array('Mange'),
        'statistiques' => array('Mes statistiques'),
        'recommendations' => array('Mes recommendations'),
        'contact'=> array('Contact')
    );
<?php 
function renderMenuToHTML($currentPageId) {
    require_once("ConfigFrontEnd.php");
    global $mymenu;
    // Génération du code HTML du menu
    echo '<div id="Menu" class="DarkMode_">';
    echo '<h1>MENU</h1>';
    echo '<nav>';
    echo '<ul class="menu">';
    
    foreach ($mymenu as $pageId => $pageParameters) {
        // Si la page actuelle est égale à la page du tableau, ajouter une classe active
        $class = ($currentPageId == $pageId) ? ' class="active"' : '';
        echo '<li' . $class . '><a href="pagesCreator.php?page=' . $pageId . '">' . $pageParameters[0] . '</a></li>';
    }
    
    echo '</ul>';
    echo '</nav>';
    echo '</div>'; // Fermeture du div Menu
    

}
?>

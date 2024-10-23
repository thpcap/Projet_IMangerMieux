<?php 
    session_start();
    require_once('ConfigFrontEnd.php');
    if($_SESSION['connected']){
        require_once('template_header.php'); /** contenu de header */
        require_once('template_menu.php');  /** contenu menu */ 
        $currentPageId = 'accueil';

        if (isset($_GET['page'])) {
            $currentPageId = $_GET['page'];  
        }
        ?>

        <header class="header">
            <?php
            require_once('template_menu.php');
            renderMenuToHTML('index');
            ?>
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
    




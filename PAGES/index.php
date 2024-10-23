<?php
    session_start();
    require_once('ConfigFrontEnd.php');
    if($_SESSION['connected']){
        header("Location:".URL_Acceuil);
    }else{
        header("Location:".URL_Login);
    }
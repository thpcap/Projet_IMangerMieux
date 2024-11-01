<?php
    session_start();
    require_once('ConfigFrontEnd.php');
    if(isset($_SESSION['connected'])&&$_SESSION['connected']){
        header("Location:".URL_Acceuil);
        setcookie('login',$_SESSION['login'], 0, "/");
    }else{
        header("Location:".URL_Login);
    }
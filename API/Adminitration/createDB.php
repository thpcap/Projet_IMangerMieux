<?php
    require_once("C:\UwAmp\www\Projet_IMangerMieux\API\init_PDO.php");
    $querry=file_get_contents('C:\UwAmp\www\Projet_IMangerMieux\API\Adminitration\create_db.sql');
    try {
        $pdo->query($querry);
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }
    try{
        require_once("insersionAliments.php");
    }catch (\Throwable $th) {
        echo $th->getMessage();
    }
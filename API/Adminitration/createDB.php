<p style="color: red; font-size: large;">
    cette fonction retourne beaucoup d'erreurs c'est normal
</p>
<?php
    require_once("../init_PDO.php");
    $querry=file_get_contents('create_db.sql');
    try {
        $pdo->query($querry);
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }
    try{
        require_once("add_Users.php");
        require_once("insersionAliments.php");
    }catch (\Throwable $th) {
        echo $th->getMessage();
    }


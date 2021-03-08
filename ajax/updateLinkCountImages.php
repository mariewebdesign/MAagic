<?php

    include('../config.php');

    if(isset($_POST["linkImagesId"])){

        $query = $con->prepare("UPDATE images SET clicks=clicks+1 WHERE id=:id");
        $query->bindParam(":id",$_POST["linkImagesId"]);
        $query->execute();
    }
    else{
        echo "Aucun lien correspondant";
    }

?>
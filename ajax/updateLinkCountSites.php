<?php

    include('../config.php');

    if(isset($_POST["linksitesid"])){

        $query = $con->prepare("UPDATE sites SET clicks=clicks+1 WHERE id=:id");
        $query->bindParam(":id",$_POST["linksitesid"]);
        $query->execute();
    }
    else{
        echo "Aucun lien correspondant";
    }

?>
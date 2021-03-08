<?php

ob_start();
/* PDO::setAttribute => Configure un attribut PDO
    PDO::ATTR_ERRMODE : rapports d'erreurs
    PDO::ERRMODE_WARNING : émet une alerte E_WRANING
 connexion à la bdd avec pdo
*/

try{
    $con = new PDO("mysql:host=localhost;dbname=googgle;charset=utf8","root","");

    $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
}
catch(Exception $e){
    die('Erreur de connexion :'.$e->getMessage());
}

?>
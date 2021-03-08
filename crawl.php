<?php

include('config.php');
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();



// eviter les doublons
function linkExists($url){
    global $con;

     // requete préparée
     $query = $con->prepare("SELECT * FROM sites WHERE url = :url");
     $query->bindParam(":url",$url);
     $query->execute();
    return $query->rowCount() != 0; 
}

function linkImageExists($src){
    global $con;

     // requete préparée
     $query = $con->prepare("SELECT * FROM images WHERE imageUrl = :imageUrl");
     $query->bindParam(":imageUrl",$src);
     $query->execute();
    return $query->rowCount() != 0; 
}

// insertion des liens dans la bdd
function insertLink($url,$title,$description,$keywords){
    // on va importer la variable $con
    global $con;

    // requete préparée
    $query = $con->prepare("INSERT INTO sites(url,title,description,keywords,clicks) VALUES(:url,:title,:description,:keywords,0)");

    $query->bindParam(":url",$url);
    $query->bindParam(":title",$title);
    $query->bindParam(":description",$description);
    $query->bindParam(":keywords",$keywords);

    return $query->execute();
}

// insertion des images dans la bdd



function insertLinkImages($url,$src,$alt,$title){

    // on va importer la variable $con
    global $con;

    // requete préparée
    $query = $con->prepare("INSERT INTO images(siteUrl,imageUrl,alt,title,clicks,broken) VALUES(:siteUrl,:imageUrl,:alt,:title,0,0)");

    $query->bindParam(":siteUrl",$url);
    $query->bindParam(":imageUrl",$src);
    $query->bindParam(":alt",$alt);
    $query->bindParam(":title",$title);
    
    return $query->execute();
}

// Création d'une fonction pour re-créer les urls
function createLinks($src,$url){

    // vérification
   // echo "Src : $href<br>";
   // echo "URL : $url<br>";

   // convertir les chemins relatifs en chemin absolus

   $scheme = parse_url($url)["scheme"]; // => http

   $host = parse_url($url)["host"]; // www.monsite.fr 

   // vérifier si les 2 premiers caractères pour voir si ce sont des //

   if(substr($src,0,2) == "//"){
       $src = $scheme.":".$src;
   }

   // verifier si le premier caractère /

   else if(substr($src,0,1) == "/"){
        $src = $scheme."://".$host.$src;
    }

    // ./
    else if(substr($src,0,2) == "./"){
        $src = $scheme."://".$host.dirname(parse_url($url)["path"]).substr($src,1);
    }

    // ../
    else if(substr($src,0,3) == "../"){
        $src = $scheme."://".$host."/".$src;
    }

    
    // verifier si c'est différent de hhtp ou https : about/about.php 
    else if(substr($src,0,5) !== "https" && substr($src,0,4) !== "http"){
        $src = $scheme."://".$host."/".$src;
    }

    return $src;

}

function getDetails($url){

    global $alreadyFoundImages;
    $parser = new DomDocumentParser($url);
    $titleArray = $parser->getTitleTags();

    // il n'y a pas d'éléments sur la 1ere ligne du tableau

    if(sizeof($titleArray)==0 || $titleArray->item(0)==NULL){
        return;
    }

    $title = $titleArray->item(0)->nodeValue;

    // supprimer les sauts de ligne
    $title = str_replace("\n","",$title);
    
    //s'il n'y a pas de titre, on ignore le lien
    if($title==""){
        return;
    }

    // description et meta
    $description = "";
    $keywords ="";


    $metaArray = $parser->getMetaTags();

    foreach($metaArray as $meta){
        
        if($meta->getAttribute("name")=="description"){
            $description = $meta->getAttribute("content");
        }

        if($meta->getAttribute("name")=="keywords"){
            $keywords = $meta->getAttribute("content");
        }
    }

    // suppression des sauts de ligne
    $description=str_replace("\n","",$description);
    $keywords = str_replace("\n","",$keywords);

    // vérifier si les urls existent
    if(linkExists($url)){
        echo "$url est déjà dans la bdd<br>";
    }
    // insertion dans liens bdd
    else if(insertLink($url,$title,$description,$keywords)){
        echo "succes, liens insérés dans la bdd<br>";
    }

    else{
        echo "Erreur lors de l'insertion dans la bdd<br>";
    }

    

    $imageArray = $parser->getImageTags();

    foreach($imageArray as $image){
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt){
            continue;
        }

        // creation lien absolu avec le chemin relatif des images
        $src = createLinks($src,$url);

        if(!in_array($src,$alreadyFoundImages)){
            $alreadyFoundImages[]=$src;

            // vérifier si les urls des images existent
            if(linkImageExists($src)){
                echo "$src est déjà dans la bdd<br>";
            }
            // insertion dans liens des images bdd
            else if(insertLinkImages($url,$src,$alt,$title)){
                echo "succes, liens images insérés dans la bdd <br>";
            }

            else{
                echo "Erreur lors de l'insertion dans la bdd<br>";
            }
        }
    }

   // echo "Url : $url, <br> Description : $description,<br> Mots clés : $keywords <br>, Images : $image<br><br>";

    
}

function followLinks($url){

    global $alreadyCrawled;
    global $crawling;

    $parser = new DomDocumentParser($url);

    $linkList = $parser->getLinks();

    foreach($linkList as $link){
        
        // récupération des href

        $href = $link->getAttribute("href");

        // supprimer les lignes ne comportant que des #

        if(strpos($href,'#') !== false){
            continue;
        }

        // supprimer les lignes comportant du javascript
        else if(substr($href,0,11) == "javascript:"){
            continue;
        }

       $href = createLinks($href,$url);
       // echo $href . '<br>';

       // conditions pour savoir si l'url n'a pas encore été visitée
       if(!in_array($href,$alreadyCrawled)){
           $alreadyCrawled[]=$href;
           $crawling[]=$href;

           // insertion des données récupérées
           getDetails($href);

       }

       // on passe à la ligne suivante
       array_shift($crawling);

       // creation d'une boucle pour récupérer les lignes du tableau $crawling

       foreach($crawling as $site){
           followLinks($site);
       }
    }

}

$startUrl = "https://imvd.fr";
followLinks($startUrl);

?>
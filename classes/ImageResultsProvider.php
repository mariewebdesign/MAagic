<?php

class ImageResultsProvider{

    private $_con;

    public function __construct($con){
        $this->_con = $con;
    } 

    public function getNumResults($term){

        $query = $this->_con->prepare("SELECT COUNT(*) as total
                                       FROM images WHERE (alt LIKE :term
                                       OR title LIKE :term)
                                       AND broken = 0
                                       ");
        $searchTerm = "%".$term."%";
        $query->bindParam(":term",$searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }


    // Méthode pour afficher les résultats
    public function getResultsHtml($term,$page,$pageSize){

        // $page : la page courante
        // $pageSize : le nombre de résultats
        // $term : terme  de recherche

        $fromLimit = ($page-1) * $pageSize;        

        $query = $this->_con->prepare("SELECT * 
                                       FROM images WHERE (title LIKE :term
                                       OR alt LIKE :term)
                                       AND broken = 0
                                       ORDER BY clicks DESC
                                       LIMIT :fromLimit,:pageSize");
        $term = 
        $searchTerm = "%".$term."%";                             
        $query->bindParam(":term",$searchTerm);
        $query->bindParam(":fromLimit",$fromLimit,PDO::PARAM_INT);
        $query->bindParam(":pageSize",$pageSize,PDO::PARAM_INT);
        $query->execute();

        $resultHtml = "<div class='row imageResults'>";
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $id = $row["id"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $imageUrl = $row["imageUrl"];
            $alt = $row["alt"];

            $resultHtml .= "<div class='resultContainer col-md-3'>
                                    <a href='$siteUrl' class='resultImages' data-linkImagesId='$id'>
                                        <img src='$imageUrl' alt='$alt' class=' img-fluid'>
                                    </a>                    
                                    <h6 class='siteUrl'>
                                        <a class='resultImages' href='$siteUrl' data-linkImagesId='$id'>
                                            $siteUrl
                                        </a>
                                    </h6>
                            </div>
                            ";
        }
        $resultHtml.= "</div>";
        return $resultHtml;
    }

   
}

?>
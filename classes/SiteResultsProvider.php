<?php

class SiteResultsProvider{

    private $_con;

    public function __construct($con){
        $this->_con = $con;
    } 

    public function getNumResults($term){

        $query = $this->_con->prepare("SELECT COUNT(*) as total
                                       FROM sites WHERE title LIKE :term
                                       OR url LIKE :term
                                       OR keywords LIKE :term
                                       OR description LIKE :term");
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

        $fromLimit = ($page-1) * $pageSize ;

        // page 1 : (1-1) * 20 =0 résultats
        // page 2 : (2-1) * 20 =20 résultats
        // page 3 : (3-1) * 20 =40 résultats

        

        $query = $this->_con->prepare("SELECT * 
                                       FROM sites WHERE title LIKE :term
                                       OR url LIKE :term
                                       OR keywords LIKE :term
                                       OR description LIKE :term
                                       ORDER BY clicks DESC
                                       LIMIT :fromLimit,:pageSize");
        $searchTerm = "%".$term."%";                             
        $query->bindParam(":term",$searchTerm);
        $query->bindParam(":fromLimit",$fromLimit,PDO::PARAM_INT);
        $query->bindParam(":pageSize",$pageSize,PDO::PARAM_INT);
        $query->execute();

        $resultHtml = "<div class='siteResults'>";
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->trimField($title,60);
            $description = $this->trimField($description,200);

            $resultHtml .= "<div class='resultContainer'>
                                <h3 class='title'>
                                    <a class='result' href='$url' data-linksitesid='$id'>
                                        $title
                                    </a>
                                </h3>
                                <p class='url'>$url</p>
                                <p class='description'>$description</p>
                            </div>";
        }
        $resultHtml.= "</div>";
        return $resultHtml;
    }

    private function trimField($string,$characterLimit){

        // condition ternaire
        $dots = strlen($string) > $characterLimit ? "..." : "";

        return  substr($string,0,$characterLimit).$dots;

    }
}

?>
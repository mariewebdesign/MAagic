<?php

include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/ImageResultsProvider.php");

if($_GET["term"] != ""){
    $term = $_GET["term"];
}else{
    header('Location:index.php');

}

// détecter quel est le type qui est présent dans l'url pour afficher différement l'onglet actif
$type = isset($_GET['type']) ? $_GET['type'] : "sites";

$page = isset($_GET['page']) ? $_GET['page'] : 1;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAagic</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

    <main class="container-fluid searchPage" >

        <header class="jumbotron fixed-top">
            <div class="row">
            
                    <h1><a href="index.php"><img src="assets/img/logo.png" alt="logo de Maagic" class="img-fluid logosm"></a></h1>
                    <form action="search.php" method="GET">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <input type="search" name="term" class="form-control" value="<?php echo $term; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                        
                    </form>
 
            </div>
            <div class="tabs">
                <ul id="tabList" class="row">
                        <li class="<?php echo $type =='sites' ? 'active' : ''; ?>">
                            <a href="<?php echo 'search.php?type=sites&term='.$term; ?>">Sites</a>
                        </li>
                        <li class="<?php echo $type =='images' ? 'active' : ''; ?>">
                            <a href="<?php echo 'search.php?type=images&term='.$term; ?>">Images</a>
                        </li>
                </ul>
            </div>

        </header>
        <section class="resultat">
            <!-- Résultats de la recherche-->
            <?php
                if($type == "sites"){
                    $resultProvider = new SiteResultsProvider($con);
                    $pageSize=10;
                    
                }else{
                    $resultProvider = new ImageResultsProvider($con);
                    $pageSize=30;
                }

                $numResults = $resultProvider->getNumResults($term);
                echo "<h4 class='numberResults'>$numResults résultat(s)</h4>";
                echo $resultProvider->getResultsHtml($term,$page,$pageSize);
            ?>
        </section>
        <section class="pagination">
            <div class="pageBtn">
                <div class="pageNumberContainer" >
                    <img src="assets/img/pageStart.png" alt="" class="logomd" id="logoStart">
                </div>

            <?php

                //$currentPage = 1;
                //$pagesLeft = 3;

                $pageToShow=10;
                $numPages = ceil($numResults/$pageSize);
                $pagesLeft = min($pageToShow,$numPages);
                $currentPage = $page-floor($pageToShow/2);

                if($currentPage < 1){
                    $currentPage=1;
                }

                if($currentPage + $pagesLeft > $numPages + 1){
                    $currentPage = $numPages + 1 - $pagesLeft;
                }

                while($pagesLeft!=0){

                    if($currentPage == $page){
                        echo "<div class='pageNumberContainer'>
                                    <img src='assets/img/pageSelected.png' class='img-fluid logossm'>
                                    <span class='pageNumber'>$currentPage</span>
                              </div>";
                    }else{
                        echo "<div class='pageNumberContainer'>
                                <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                    <img src='assets/img/page.png' class='img-fluid logossm'>
                                    <span class='pageNumber'>$currentPage</span>
                                </a>
                               </div>";
                    }
                    $currentPage++;
                    $pagesLeft--;
                }
            ?>
                <div class="pageNumberContainer">
                    <img src="assets/img/pageEnd.png" alt="" class="logosm">
                </div>
            </div>
        </section>
    </main>

<footer>
    <p class="text-center justify-content-end mt-5" >Copyright 2021 - Marie Web Design</p>
</footer>

<script
	src="https://code.jquery.com/jquery-3.6.0.min.js"
	integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
	crossorigin="anonymous">
</script>
<script src="assets/js/script.js" type="text/javascript"></script>

</body>
</html>


$(document).ready(function(){


   // si on clique sur un lien dans la section sites
  $(".result").on("click",function(){
       console.log("Lien cliqué");
       

       // ID(siteResultsProvider)
       var id = $(this).attr("data-linksitesid");

       // url (href)
       var url = $(this).attr("href");

        // test
       // console.log(id);
        //console.log(url);
    if(!id){
        alert("data-linksitesid non trouvé");
    }

    if(!url){
        alert("url non trouvé");
    }

       // augmentation dans la bdd +1
        increaseClicksSites(id,url);
        return false;
   });

   // si on clique sur un lien dans la section images
    $(".resultImages").on("click",function(){
        console.log("Lien cliqué");

        // ID(siteResultsProvider)
        var id = $(this).attr("data-linkImagesId");

        // url (href)
        var siteUrl = $(this).attr("href");

        // test
        //console.log(id);
        //console.log(url);
    if(!id){
        alert("data-linkImagesId non trouvé");
    }

    if(!siteUrl){
        alert("imageUrl non trouvé");
    }


        // augmentation dans la bdd +1
        increaseClicksImages(id,siteUrl);
        return false;
    });

});

   function increaseClicksSites(linksitesid,url){

        // chargement
        $.post("ajax/updateLinkCountSites.php",{linksitesid:linksitesid}).done(function(result){
            if(result != ""){
                alert(result);
                return;
            }
            
            window.location.href=url;
        });
        
   }

   function increaseClicksImages(linkImagesId,siteUrl){

    // chargement
    $.post("ajax/updateLinkCountImages.php",{linkImagesId:linkImagesId}).done(function(result){
        if(result != ""){
            alert(result);
            return;
        }
        
        window.location.href=siteUrl;
    });
    
}



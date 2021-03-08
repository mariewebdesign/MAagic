<?php


class DomDocumentParser{

    public function __construct($url){
        //echo "URL : $url";

        $options = array(
            'http'=> array(
                'method' => 'GET',
                'header' => 'User-Agent: GooggleBot/1.0\n'
            )
        );

        $context = stream_context_create($options);

        // chargement du document html

        $this->_doc = new DomDocument();

        @$this->_doc ->loadHTML(file_get_contents($url,false,$context));
    }

    public function getLinks(){
        return $this->_doc->getElementsByTagName("a");
    }

    public function getTitleTags(){
        return $this->_doc->getElementsByTagName("title");
    }

    public function getMetaTags(){
        return $this->_doc->getElementsByTagName("meta");
    }

    public function getImageTags(){
        return $this->_doc->getElementsByTagName("img");
    }

}

?>
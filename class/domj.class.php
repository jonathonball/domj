<?php

class domj extends DOMDocument {
  
    function __construct($filename, $title = NULL, $boilerplate = FALSE) {
        parent::__construct(NULL, NULL);
        parent::loadHTMLFile($filename);
        if($title) {
            $this->addElementTitle($title);
        }
        if($boilerplate) {
            $this->addStyle("css/normalize.css");
            $this->addScript("js/jquery.js");
            $this->addScript("js/jquery-ui.js");
        }
    }
    

    public function createElement($name, $value = null, $attr = NULL) {
        $el = parent::createElement($name, $value);
        $this->addAttributes ($el, $attr);
        return $el;
    }
    
    function appendChildById($target, $child) {
        parent::getElementById($target)->appendChild($child);
    }
    
    function graft($target, $file, $id = NULL, $attr = NULL) {
        $new = $this->importElement($file, $id, $attr);
        $this->appendChildById($target, $new);
    }
    
    function importElement($file, $id = NULL, $attr = NULL) {
        $foreign = new domj($file);
        $new = parent::importNode($foreign->getElementById("import"), TRUE);
        $new->removeAttribute("id");
        if($id) {
            $new->setAttribute("id", $id);
        }
        $this->addAttributes($new, $attr);
        return $new;
    }
    
    function addAttributes($el, $attr = NULL) {
        if($attr) {
            foreach($attr as $k=>$v) {
                $el->setAttribute($k, $v);
            }
        }
        return $el;
    }
   
     function table($target, $data, $header = FALSE, $attr = NULL) {
        $table = $this->createElement("table", NULL, $attr);
        $tableStartPos = 0;
        if($header) {
            $table->appendChild($this->tableHead(array_keys($data[0])));
            $tableStartPos = 1;
        }
        $tbody = $this->createElement("tbody");
        $count = count($data);
        for($i = $tableStartPos; $i < $count; $i++) {
            $tr = $this->createElement("tr");
            foreach($data[$i] as $cell) {
                $td = $this->createElement("td", $cell);
                $tr->appendChild($td);
            }
            $tbody->appendChild($tr);
        }
        $table->appendChild($tbody);
        $this->appendChildById($target, $table);
    }
    
    function tableHead($keys) {
        $thead = $this->createElement("thead");
        $tr = $this->createElement("tr");
        foreach($keys as $k) {
            $th = $this->createElement("th", $k);
            $tr->appendChild($th);            
        }
        $thead->appendChild($tr);
        return $thead;
    }

     function addScript($filename, $inHeader = TRUE) { 
        $script = $this->createElement("script", "", array("type"=>"text/javascript", "href"=>$filename));
        if($inHeader) {
            $target = parent::getElementsByTagName("head");
        } else {
            $target = parent::getElementsByTagName("body");
        }
        $target->item(0)->appendChild($script);
    }
    
    function addStyle($filename) {
        $style = $this->createElement("link", NULL, array("rel"=>"stylesheet", "type"=>"text/css", "href"=>$filename));
        $target = parent::getElementsByTagName("head");
        $target->item(0)->appendChild($style);
    }
    
    function addElementTitle($title) {
        $titleEl = $this->createElement("title", $title);
        $target = parent::getElementsByTagName("head");
        $target->item(0)->appendChild($titleEl);
    }
    
    function getBody() {
        $body = parent::getElementsByTagName("body")->item(0);
        return $body;
    }
    
    function copyAttr() {
        
    }
    
    //function addElementForm($filename) {
    //    $formEl = $this->importElement($filename, "form");
    //}
    
    function addElementInfo() {
        
    }
    
    function __toString() {
        return parent::saveHTML();
    }
}

<?php
/* Usage
 Grab some XML data, either from a file, URL, etc. however you want. Assume storage in $strYourXML;

 $objXML = new xml2Array();
 $arrOutput = $objXML->parse($strYourXML);
 print_r($arrOutput); //print it out, or do whatever!
  
*/
class xml2Array {
   
   var $arrOutput = array();
   var $resParser;
   var $strXmlData;
   
   /**
   * Convert a utf-8 string to html entities
   *
   * @param string $str The UTF-8 string
   * @return string
   */
   function utf8_to_entities($str) {
       $entities = '';
       $values = array();
       $lookingfor = 1;

       for ($i = 0; $i < strlen($str); $i++) {
           $thisvalue = ord($str[$i]);
           if ($thisvalue < 128) {
               $entities .= $str[$i]; // Leave ASCII chars unchanged 
           } else {
               if (count($values) == 0) {
                   $lookingfor = ($thisvalue < 224) ? 2 : 3;
               }
               $values[] = $thisvalue;
               if (count($values) == $lookingfor) {
                   $number = ($lookingfor == 3) ?
                       (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64):
                       (($values[0] % 32) * 64) + ($values[1] % 64);
                   $entities .= '&#' . $number . ';';
                   $values = array();
                   $lookingfor = 1;
               }
           }
       }
       return $entities;
   }

   /**
   * Parse an XML text string and create an array tree that rapresent the XML structure
   *
   * @param string $strInputXML The XML string
   * @return array
   */
   function parse($strInputXML) {
           $this->resParser = xml_parser_create ('UTF-8');
           xml_set_object($this->resParser,$this);
           xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
           
           xml_set_character_data_handler($this->resParser, "tagData");
       
           $this->strXmlData = xml_parse($this->resParser,$strInputXML );
           if(!$this->strXmlData) {
               die(sprintf("XML error: %s at line %d",
                           xml_error_string(xml_get_error_code($this->resParser)),
                           xml_get_current_line_number($this->resParser)));
           }
                           
           xml_parser_free($this->resParser);
           
           return $this->arrOutput;
   }
   
   function tagOpen($parser, $name, $attrs) {
       $tag=array("name"=>$name,"attrs"=>$attrs); 
       array_push($this->arrOutput,$tag);
   }
   
   function tagData($parser, $tagData) {
       if(trim($tagData)) {
           if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $this->utf8_to_entities($tagData);
           } else {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $this->utf8_to_entities($tagData);
           }
       }
   }
   
   function tagClosed($parser, $name) {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
   }
}
?>
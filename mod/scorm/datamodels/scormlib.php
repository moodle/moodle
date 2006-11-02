<?php
function scorm_get_resources($blocks) {
    $resources = array();
    foreach ($blocks as $block) {
        if ($block['name'] == 'RESOURCES') {
            foreach ($block['children'] as $resource) {
                if ($resource['name'] == 'RESOURCE') {
                    $resources[addslashes($resource['attrs']['IDENTIFIER'])] = $resource['attrs'];
                }
            }
        }
    }
    return $resources;
}

function scorm_get_manifest($blocks,$scoes) {
    static $parents = array();
    static $resources;

    static $manifest;
    static $organization;

    if (count($blocks) > 0) {
        foreach ($blocks as $block) {
            switch ($block['name']) {
                case 'METADATA':
                    if (isset($block['children'])) {
                        foreach ($block['children'] as $metadata) {
                            if ($metadata['name'] == 'SCHEMAVERSION') {
                                if (empty($scoes->version)) {
                                    if (isset($metadata['tagData']) && (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches))) {
                                        $scoes->version = 'SCORM_'.$matches[count($matches)-1];
                                    } else {
                                         if (isset($metadata['tagData']) && (preg_match("/^2004 3rd Edition$/",$metadata['tagData'],$matches))) {
                                            $scoes->version = 'SCORM_1.3';
                                        } else {
                                            $scoes->version = 'SCORM_1.2';
                                        }
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'MANIFEST':
                    $manifest = addslashes($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $resources = array();
                    $resources = scorm_get_resources($block['children']);
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    if (count($scoes->elements) <= 0) {
                        foreach ($resources as $item => $resource) {
                            if (!empty($resource['HREF'])) {
                                $sco = new stdClass();
                                $sco->identifier = $item;
                                $sco->title = $item;
                                $sco->parent = '/';
                                $sco->launch = addslashes($resource['HREF']);
                                $sco->scormtype = addslashes($resource['ADLCP:SCORMTYPE']);
                                $scoes->elements[$manifest][$organization][$item] = $sco;
                            }
                        }
                    }
                break;
                case 'ORGANIZATIONS':
                    if (!isset($scoes->defaultorg)) {
                        $scoes->defaultorg = addslashes($block['attrs']['DEFAULT']);
                    }
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                break;
                case 'ORGANIZATION':
                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = '/';
                    $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                    $scoes->elements[$manifest][$organization][$identifier]->scormtype = '';

                    $parents = array();
                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);
                    $organization = $identifier;

                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    
                    array_pop($parents);
                break;
                case 'ITEM':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);

                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
                    if (!isset($block['attrs']['ISVISIBLE'])) {
                        $block['attrs']['ISVISIBLE'] = 'true';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->isvisible = addslashes($block['attrs']['ISVISIBLE']);
                    if (!isset($block['attrs']['PARAMETERS'])) {
                        $block['attrs']['PARAMETERS'] = '';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->parameters = addslashes($block['attrs']['PARAMETERS']);
                    if (!isset($block['attrs']['IDENTIFIERREF'])) {
                        $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
                    } else {
                        $idref = addslashes($block['attrs']['IDENTIFIERREF']);
                        $base = '';
                        if (isset($resources[$idref]['XML:BASE'])) {
                            $base = $resources[$idref]['XML:BASE'];
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->launch = addslashes($base.$resources[$idref]['HREF']);
                        if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
                            $resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = addslashes($resources[$idref]['ADLCP:SCORMTYPE']);
                    }

                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);

                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    
                    array_pop($parents);
                break;
                case 'TITLE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = addslashes($block['tagData']);
                break;
                case 'ADLCP:PREREQUISITES':
                    if ($block['attrs']['TYPE'] == 'aicc_script') {
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = addslashes($block['tagData']);
                    }
                break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = addslashes($block['tagData']);
                break;
                case 'ADLCP:TIMELIMITACTION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = addslashes($block['tagData']);
                break;
                case 'ADLCP:DATAFROMLMS':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = addslashes($block['tagData']);
                break;
                case 'ADLCP:MASTERYSCORE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = addslashes($block['tagData']);
                break;
            }
        }
    }
    return $scoes;
}

function scorm_parse_scorm($pkgdir,$scormid) {
    global $CFG;
    
    $launch = 0;
    $manifestfile = $pkgdir.'/imsmanifest.xml';

    if (is_file($manifestfile)) {
    
        $xmltext = file_get_contents($manifestfile);

        $pattern = '/&(?!\w{2,6};)/';
        $replacement = '&amp;';
        $xmltext = preg_replace($pattern, $replacement, $xmltext);

        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmltext);
        //print_r($manifests); 
        $scoes = new stdClass();
        $scoes->version = '';
        $scoes = scorm_get_manifest($manifests,$scoes);

        if (count($scoes->elements) > 0) {
            $olditems = get_records('scorm_scoes','scorm',$scormid);
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        // This new db mngt will support all SCORM future extensions
                        /*$newitem = new stdClass(); 
                        $newitem->scorm = $scormid;
                        $newitem->manifest = $manifest;
                        $newitem->organization = $organization;
                        $standarddatas = array('parent', 'identifier', 'launch', 'scormtype', 'title');
                        foreach ($standarddatas as $standarddata) {
                            $newitem->$standarddata = $item->$standarddata;
                        }

                        if ($olditemid = scorm_array_search('identifier',$newitem->identifier,$olditems)) {
                            $newitem->id = $olditemid;
                            $id = update_record('scorm_scoes',$newitem);
                            unset($olditems[$olditemid]);
                            delete_records('scorm_scoes_data','scoid',$olditem->id);
                        } else {
                            $id = insert_record('scorm_scoes',$newitem);
                        }

                        $data = new stdClass();
                        $data->scormid = $scormid;
                        $data->scoid = $id;
                        $optionaldatas = scorm_optionals_data();
                        foreach ($optionalsdatas as $optionaldata) {
                            if (isset($item->$optionaldata)) {
                                $data->name =  $optionaldata;
                                $data->value = $item->$optionaldata;
                                $dataid = insert_record('scorm_scoes_data');
                            }
                        } */

                        $item->scorm = $scormid;
                        $item->manifest = $manifest;
                        $item->organization = $organization;
                        if ($olditemid = scorm_array_search('identifier',$item->identifier,$olditems)) {
                            $item->id = $olditemid;
                            $id = update_record('scorm_scoes',$item);
                            unset($olditems[$olditemid]);
                        } else {
                            $id = insert_record('scorm_scoes',$item);
                        }
                
                        if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                            $launch = $id;
                        }
                    }
                }
            }
            if (!empty($olditems)) {
                foreach($olditems as $olditem) {
                   delete_records('scorm_scoes','id',$olditem->id);
                   //delete_records('scorm_scoes_data','scoid',$olditem->id);
                   delete_records('scorm_scoes_track','scoid',$olditem->id);
                }
            }
            set_field('scorm','version',$scoes->version,'id',$scormid);
        }
    } 
    
    return $launch;
}

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
       global $CFG;

       $entities = '';
       $values = array();
       $lookingfor = 1;

       if (empty($CFG->unicodedb)) {  // If Unicode DB support enable does not convert string
           $textlib = textlib_get_instance();
           for ($i = 0; $i < $textlib->strlen($str,'utf-8'); $i++) {
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
       } else {
           return $str;
       }
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

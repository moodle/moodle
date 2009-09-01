<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * This file generate a web service documentation in HTML
 * This documentation describe how to call a Moodle Web Service
 */
require_once('../config.php');
require_once('lib.php');
$protocol = optional_param('protocol',"soap",PARAM_ALPHA);

/// PAGE settings
$PAGE->set_course($COURSE);
$PAGE->set_url('webservice/wsdoc.php');
$PAGE->set_title(get_string('wspagetitle', 'webservice'));
$PAGE->set_heading(get_string('wspagetitle', 'webservice'));
$PAGE->set_generaltype("form");
echo $OUTPUT->header();
webservice_lib::display_webservices_availability($protocol);
generate_documentation($protocol);
generate_functionlist();
echo $OUTPUT->footer();



/**
 * Generate documentation specific to a protocol
 * @param string $protocol
 */
function generate_documentation($protocol) {
    switch ($protocol) {

        case "soap":
            $documentation = get_string('soapdocumentation','webservice');
            break;
        case "xmlrpc":
            $documentation = get_string('xmlrpcdocumentation','webservice');
            break;
        default:
            break;
    }
    echo $documentation;
    echo "<strong style=\"color:orange\">".get_string('wsuserreminder','webservice')."</strong>";

}


/**
 * Generate web service function list
 * @global object $CFG
 */
function generate_functionlist () {
    global $CFG;
    $documentation = "<H2>".get_string('functionlist','webservice')."</H2>";

    //retrieve all external file
    $externalfiles = array();
    $externalfunctions = array();
    setListApiFiles($externalfiles, $CFG->dirroot);

    foreach ($externalfiles as $file) {
        require($file);

        $classpath = substr($file,strlen($CFG->dirroot)+1); //remove the dir root + / from the file path
        $classpath = substr($classpath,0,strlen($classpath) - 13); //remove /external.php from the classpath
        $classpath = str_replace('/','_',$classpath); //convert all / into _
        $classname = $classpath."_external";
        $api = new $classname();
        $documentation .= "<H3><u>".get_string('moodlepath','webservice').": ".$classpath."</u></H3><ul>";
        if ($classname == "user_external") {
            $description = $api->get_descriptions();
            var_dump("<pre>");
            convertDescriptionType($description);
            var_dump("</pre>");
            foreach ($description as $functionname => $functiondescription) {
                $documentation .= <<<EOF
        <li><b>{$functionname}(</b>
EOF;
                $arrayparams = array();
                $comma="";
                foreach($functiondescription['params'] as $param => $type) {
                //  $type = converterMoodleParamIntoWsParam($type);
                    $documentation .= <<<EOF
                <span style=color:green>{$comma} {$type} <b>{$param}</b>
EOF;
                    if (empty($comma)) {
                        $comma = ',';
                    }
                }
                $documentation .= <<<EOF
                    <b></span>)</b> :
EOF;
                if (array_key_exists('return', $functiondescription)) {
                    foreach($functiondescription['return'] as $return => $type) {
                    //   $thetype = converterMoodleParamIntoWsParam($type);
                        $documentation .= <<<EOF
                <span style=color:blue>
                <i>
                            {$type}</i>
EOF;
                        if (is_array($type)) {
                            $arraytype = "<pre>".print_r($type, true)."</pre>";
                            $documentation .= <<<EOF
                 <b>{$return}</b><br/><br/><b>{$return}</b>  {$arraytype} </span>
EOF;
                        }
                    }
                }

                $documentation .= <<<EOF
                    <br/><br/><span style=color:green>
EOF;
                foreach($functiondescription['params'] as $param => $type) {

                    if (is_array($type)) {
                        $arraytype = "<pre>".print_r($type, true)."</pre>";
                        $documentation .= <<<EOF
         <b>{$param}</b> : {$arraytype} <br>
EOF;
                    }
                    else {
                    // $type = converterMoodleParamIntoWsParam($type);
                        $documentation .= <<<EOF
         <b>{$param}</b> : {$type} <br>
EOF;
                    }

                }
                $documentation .= <<<EOF
                    </div><br/><br/>
EOF;

            }
        }
        $documentation .= <<<EOF
            </ul>
EOF;

    }

    echo $documentation;

}

function convertDescriptionType(&$description) {
    foreach ($description as &$type) {
        if (is_array($type)) { //is it a List ?
            convertDescriptionType($type);
        }
        else {
            if (is_object($type)) { //is it a object
               convertObjectTypes($type);
            }
            else { //it's a primary type
            
            $type = converterMoodleParamIntoWsParam($type);
            }
        }
      
    }
}

function convertObjectTypes(&$type) {
   foreach (get_object_vars($type) as $propertyname => $propertytype) {
       if (is_array($propertytype)) {
           convertDescriptionType($propertytype);
           $type->$propertyname = $propertytype;
       } else {
           $type->$propertyname = converterMoodleParamIntoWsParam($propertytype);
       }
   }
}

/**
 * Convert a Moodle type (PARAM_ALPHA, PARAM_NUMBER,...) as a SOAP type (string, interger,...)
 * @param integer $moodleparam
 * @return string  SOAP type
 */
function converterMoodleParamIntoWsParam($moodleparam) {
    switch ($moodleparam) {
        case PARAM_NUMBER:
            return "integer";
            break;
        case PARAM_INT:
            return "integer";
            break;
        case PARAM_BOOL:
            return "boolean";
            break;
        case PARAM_ALPHANUM:
            return "string";
            break;
         case PARAM_ALPHA:
            return "string";
            break;
        case PARAM_RAW:
            return "string";
            break;
        case PARAM_ALPHANUMEXT:
            return "string";
            break;
        case PARAM_NOTAGS:
            return "string";
            break;
        case PARAM_TEXT:
            return "string";
            break;
        //here we check that the value has not already been changed
        //the algo could want to do it in the case two functions of the web description use the
        //same object ($params or $return could be the same for two functions, so the guy
        //writing the web description use the same object)
        //as the convertDescriptionType function passes parameter in reference
        case "integer":
            return "integer";
            break;
        case "boolean":
            return "boolean";
            break;
        case "string":
            return "string";
            break;
        default:
            
            //return get_object_vars($moodleparam);
            return "object";
            break;
    }
}

/**
 * Retrieve all external.php from Moodle
 * @param array $files
 * @param string $directorypath
 * @return boolean result true if n
 */
function setListApiFiles( &$files, $directorypath ) {
    if(is_dir($directorypath)) { //check that we are browsing a folder not a file

        if( $dh = opendir($directorypath)) {
            while( false !== ($file = readdir($dh))) {

                if( $file == '.' || $file == '..') {   // Skip '.' and '..'
                    continue;
                }
                $path = $directorypath . '/' . $file;
                ///browse the subfolder
                if( is_dir($path) ) {
                    setListApiFiles($files, $path);
                }
                ///retrieve api.php file
                else if ($file == "external.php") {
                        $files[] = $path;
                    }
            }
            closedir($dh);

        }
    }
}

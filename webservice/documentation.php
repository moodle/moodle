<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   webservice
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

/**
 * This file generate a web service documentation in HTML
 * This documentation describe how to call a Moodle Web Service
 */
require_once('../config.php');
require_once('lib.php');
$protocol = optional_param('protocol',"soap",PARAM_ALPHA);

print_header(get_string('wspagetitle','webservice'), get_string('wspagetitle','webservice').":", true);
check_webservices($protocol);
generate_documentation($protocol);
generate_functionlist();
print_footer();

/**
 * Check if the Moodle site has the web service protocol enable
 * @global object $CFG
 * @param string $protocol
 */
function check_webservices($protocol){
    global $CFG;

    echo get_string('webservicesenable','webservice').": ";
    if (empty($CFG->enablewebservices)) {
        echo "<strong style=\"color:red\">".get_string('fail','webservice')."</strong>";
    } else {
        echo "<strong style=\"color:green\">".get_string('ok','webservice')."</strong>";
    }
    echo "<br/>";

    foreach(webservice_lib::get_list_protocols() as $wsprotocol) {
        if (strtolower($wsprotocol->get_protocolname()) == strtolower($protocol)) {
            echo get_string('protocolenable','webservice',array($wsprotocol->get_protocolname())).": ";
            if ( get_config($wsprotocol-> get_protocolname(), "enable")) {
                echo "<strong style=\"color:green\">".get_string('ok','webservice')."</strong>";
            } else {
                echo "<strong style=\"color:red\">".get_string('fail','webservice')."</strong>";
            }
            echo "<br/>";
            continue;
        }
    }

    //check debugging
    if ($CFG->debugdisplay) {
        echo "<strong style=\"color:red\">".get_string('debugdisplayon','webservice')."</strong>";
    }


}

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

        $description = webservice_lib::generate_webservice_description($file, $classname);

        foreach ($description as $functionname => $functiondescription) {
            $documentation .= <<<EOF
        <li><b>{$functionname}(</b>
EOF;
            $arrayparams = array();
            $comma="";
            foreach($functiondescription['params'] as $param => $type) {

                $documentation .= <<<EOF
                {$comma} {$type} {$param}
EOF;
                if (empty($comma)) {
                    $comma = ',';
                }
            }
            $documentation .= <<<EOF
        <b>)</b> :
EOF;
            foreach($functiondescription['return'] as $return => $type) {
                $documentation .= <<<EOF
                <i>
                {$type}</i>
EOF;
                if (is_array($type)) {
                    $arraytype = "<pre>".print_r($type, true)."</pre>";
                    $documentation .= <<<EOF
                <i> {$return}  {$arraytype} <br><br></i>
EOF;
                }
            }
            foreach($functiondescription['params'] as $param => $type) {

                if (is_array($type)) {
                    $arraytype = "<pre>".print_r($type, true)."</pre>";
                    $documentation .= <<<EOF
         <u>{$param}</u> : {$arraytype} <br>
EOF;
                }

            }

        }
        $documentation .= <<<EOF
        </ul>
EOF;

    }

    echo $documentation;

}


 /**
 * Retrieve all external.php from Moodle
 * @param array $files
 * @param string $directorypath
 * @return boolean result true if n
 */
function setListApiFiles( &$files, $directorypath )
{
    if(is_dir($directorypath)){ //check that we are browsing a folder not a file

        if( $dh = opendir($directorypath))
        {
            while( false !== ($file = readdir($dh)))
            {

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


?>

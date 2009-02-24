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
 * This file generate a SOAP documentation in HTML
 * This documentation describe how to call Moodle SOAP Web Service
 */
require_once('../config.php');
require_once('lib.php');
$protocol = optional_param('protocol',"soap",PARAM_ALPHA);
generate_documentation($protocol);
generate_functionlist();


/**
 *
 * @param <type> $protocol
 */
function generate_documentation($protocol) {
    switch ($protocol) {
        case "soap":
            $documentation = <<<EOF
        <H2>SOAP Manual</H2>
        <b>1.</b> Call the method <b>tmp_get_token</b> on "<i>http://remotemoodle/webservice/soap/zend_soap_server.php?wsdl</i>"<br>
        Function parameter is an array: in PHP it would be array('username' => "wsuser", 'password' => "wspassword")<br>
        Return value is a token (integer)<br>
        <br>
        <b>2.</b> Then call a moodle web service method on "<i>http://remotemoodle/webservice/soap/zend_soap_server.php?token=the_received_token&classpath=the_moodle_path&wsdl</i>"<br>
        Every method has only one parameter which is an array.<br>
        <br>
        For example in PHP for this specific function:<br>
        Moodle path: <b>user</b><br>
        <b>tmp_delete_user</b>( string username, integer mnethostid,   )<br>
        You will call something like:<br>
        your_client->tmp_delete_user(array('username' => "username_to_delete",'mnethostid' => 1))<br>

EOF;
            break;
        case "xmlrpc":
            $documentation = <<<EOF
        <H2>XMLRPC Manual</H2>
        <b>1.</b> Call the method <b>authentication.tmp_get_token</b> on "<i>http://remotemoodle/webservice/xmlrpc/zend_xmlrpc_server.php</i>"<br>
        Function parameter is an array: in PHP it would be array('username' => "wsuser", 'password' => "wspassword")<br>
        Return value is a token (integer)<br>
        <br>
        <b>2.</b> Then call a moodle web service method on "<i>http://remotemoodle/webservice/xmlrpc/zend_xmlrpc_server.php?classpath=the_moodle_path&token=the_received_token</i>"<br>
        Every method has only one parameter which is an array.<br>
        <br>
        For example in PHP for this specific function:<br>
        Moodle path: <b>user</b><br>
        <b>tmp_delete_user</b>( string username, integer mnethostid,   )<br>
        You will call something like:<br>
        your_client->call('user.tmp_delete_user', array(array('username' => "username_to_delete",'mnethostid' => 1)))<br>

EOF;
            break;
        default:
            break;
    }
    echo $documentation;
}


/**
 *
 * @global <type> $CFG
 */
function generate_functionlist () {
    global $CFG;
    $documentation = <<<EOF
        <H2>list of web service functions</H2>
EOF;

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
        $documentation .= <<<EOF
        <H3>Moodle path: {$classpath}</H3>
EOF;

        $description = webservice_lib::generate_webservice_description($file, $classname);

        foreach ($description as $functionname => $functiondescription) {
            $documentation .= <<<EOF
        <b>{$functionname}(</b>
EOF;
            $arrayparams = array();
            foreach($functiondescription['params'] as $param => $type) {
                $documentation .= <<<EOF
        {$type} {$param},
EOF;
            }
                $documentation .= <<<EOF
        <b>)</b><br/>
EOF;
             foreach($functiondescription['params'] as $param => $type) {

                 if (is_array($type)) {
                     $arraytype = "<pre>".print_r($type, true)."</pre>";
                      $documentation .= <<<EOF
         <u>{$param}</u> : {$arraytype} <br>
EOF;
                 }
               
                 }
            
            }

        }

        echo $documentation;

    }


 /**
 * Retrieve all external.php from Moodle
 * @param <type> $
 * @param <type> $directorypath
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

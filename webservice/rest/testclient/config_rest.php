<?php
/**
 * Created on 10/17/2008
 *
 * Rest Test Client configuration file
 *
 * @author David Castro Garcia
 * @author Ferran Recio Calderó
 * @author Jerome Mouneyrac
 *
 */

require_once("../../../config.php");
require_once ('lib.php');

if (empty($CFG->enablewebservices)) {
    die;
}

$CFG->serverurl = $CFG->wwwroot.'/webservice/rest/server.php';
if (!function_exists('curl_init')) die ('CURL library was not found!');

?>
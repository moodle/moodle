<?php
/**
 * Created on 10/14/2008
 *
 * REST Moodle server.
 *
 * NOTE: for this first implementation, REST requires implicit url encoded params.
 * It uses function get_file_argument to get them.
 *
 * @author Jerome Mouneyrac
 * @author Ferran Recio
 * @author David Castro Garcia
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('lib.php');

//retrieve path and function name from the URL
$rest_arguments = get_file_argument('server.php');

header ("Content-type: text/xml");

echo call_moodle_function($rest_arguments);
?>
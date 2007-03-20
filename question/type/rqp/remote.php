<?php  // $Id$
/**
* Remote question processing interface (using RQP)
*
* @version $Id$
* @author Alex Smith and others members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package questionbank
* @subpackage questiontypes
*/

// Load functions for the RQP-SOAP binding
require_once($CFG->dirroot . '/question/type/rqp/rqp.php');

// Remote item processing flags (cached from server)
define('REMOTE_TEMPLATE', 4);
define('REMOTE_ADAPTIVE', 8);

// Global connection variable
global $remote_connections;
$remote_connections = array();


/**
* Create connection to an RQP server of required type if it does not already exist
*
* If the global array $remote_connections does not already have an entry for this
* server type then it randomly goes through the existing servers and tries to connect
* using rqp_connect(). The connection is then added to the $remote_connections array.
* If the function fails to connect to any server it returns false.
* @param string $typeid  The type of the RQP server
* @return boolean  Indicates success or failure
*
* @todo flag dead servers
*/
function remote_connect($typeid) {
    global $remote_connections;

    if (!array_key_exists($typeid, $remote_connections)) {
        // get the available servers
        if (!$servers = get_records('question_rqp_servers', 'typeid', $typeid)) {
            // we don't have a server for this question type
            return false;
        }
        // put them in a random order
        shuffle($servers);
        // go through them and try to connect to each until we are successful
            foreach ($servers as $server) {
            if ($remote_connections[$typeid] = rqp_connect($server->url)) {
            break; // we have a connection
            } else {
                // We have a dead server here, should somehow flag that
            }
        }
    }
    // check that we did get a connection
    if (!$remote_connections[$typeid]) {
        unset($remote_connections[$typeid]);
        return false;
    }
    return true;
}

/**
* Create connection to an RQP server and requests server information
*
* @param string $url  The url of the RQP server
* @return object      An object holding the results of the ServerInformation call 
*                     plus the server url. Returns false in the case of failure
*/
function remote_server_info($url) {

    if (!$connection = rqp_connect($url)) {
        return false;
    }
    $return = rqp_server_info($connection);
    if (is_soap_fault($return)) {
        $return = false;
    }
    $return->url = $url;
    return $return;
}

/**
* Create connection to an RQP server and requests server information
*
* @param object $options  The RQP question options as stored in the question_rqp table
* @return object      An object holding the results of the ItemInformation call 
*                     Returns false in the case of failure
*/
function remote_item_info(&$options) {
    global $remote_connections;

    if (!remote_connect($options->type)) {
        return false;
    }

    return rqp_item_info($remote_connections[$options->type],
     $options->source, $options->format, 0);
}

/**
 * Perform a remote rendering operation on the RQP question
 *
 * @param object $question
 * @param object $state
 * @param boolean $advanceState
 * @param string $output One of 'normal', 'readonly' or 'print'.
 */
function remote_render(&$question, &$state, $advanceState=false, $output='normal') {
    global $remote_connections;

    // Make the code more readable
    $options =& $question->options;

    // Add prefix to response variable names
    $responses = array();
    foreach ($state->responses as $key => $resp) {
        $responses[$question->name_prefix . $key] = $resp;
    }

    // Prepare the render format
    if ('print' === $output) {
        $renderFormat = RQP_URI_FORMAT . 'latex-2e';
    } else if ('readonly' === $output) {
        $renderFormat = RQP_URI_FORMAT . 'xhtml-1.0-print';
    } else {
        $renderFormat = RQP_URI_FORMAT . 'xhtml-1.0-web';
    }
    // Perform the RQP operation
    if (!remote_connect($options->type)) {
        return false;
    }
    return rqp_render($remote_connections[$options->type],
         $options->source, $options->format, $state->options->persistent_data,
         $question->name_prefix, $responses, $advanceState,
         $renderFormat, $state->options->template_vars, 0);
}

/**
 * Perform a remote SessionInformation call
 *
 * @param object $question
 * @param object $state
 */
function remote_session_info(&$question, &$state) {
    global $remote_connections;

    // Make the code more readable
    $options =& $question->options;

    // Perform the RQP operation
    if (!remote_connect($options->type)) {
        return false;
    }
    return rqp_session_info($remote_connections[$options->type],
         $options->source, $options->format, $state->options->persistent_data,
         $state->options->template_vars);
}


?>

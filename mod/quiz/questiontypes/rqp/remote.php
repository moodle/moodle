<?php  // $Id$
/**
* Remote question processing interface (using RQP)
*
* @version $Id$
* @author Alex Smith and others members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

// Load functions for the RQP-SOAP binding
require_once('rqp.php');

// Remote item processing flags (cached from server)
define('REMOTE_CLONING', 1);
define('REMOTE_IMPLICIT_CLONING', 2);
define('REMOTE_TEMPLATES_SUPPORTED', 3);
define('REMOTE_TEMPLATE', 4);
define('REMOTE_ADAPTIVE', 8);

// Global connection variable
global $remote_connections;
$remote_connections = array();


/**
* Create connection to rendering server if it does not already exist
*
* @param object $options  This holds in particular the URL of the rendering
*                         server in $options->rendering_server
* @return boolean  Indicates success or failure
*/
function remote_rendering_connect($options) {
    global $remote_connections;

    if (!array_key_exists($options->rendering_server, $remote_connections)) {
        $remote_connections[$options->rendering_server] =
         rqp_connect($options->rendering_server);
    }
    if (!$remote_connections[$options->rendering_server]) {
        unset($remote_connections[$options->rendering_server]);
        return false;
    }
    return true;
}

/**
* Create connection to cloning server if it does not already exist
*
* @param object $options  This holds in particular the URL of the rendering
*                         server in $options->cloning_server
* @return boolean  Indicates success or failure
*/
function remote_cloning_connect($options) {
    global $remote_connections;

    if (!array_key_exists($options->cloning_server, $remote_connections)) {
        $remote_connections[$options->cloning_server] =
         rqp_connect($options->cloning_server);
    }
    if (!$remote_connections[$options->cloning_server]) {
        unset($remote_connections[$options->cloning_server]);
        return false;
    }
    return true;
}

function remote_server_info($options, $rendering_server=true,
 $cloning_server=true) {
    global $remote_connections;

    $return = array();
    if ($rendering_server) {
        if (remote_rendering_connect($options)) {
            $return['rendering'] =
             rqp_server_info($remote_connections[$options->rendering_server]);
            if (is_soap_fault($return['rendering'])) {
                $return['rendering'] = false;
            }
        }
        else {
            $return['rendering'] = false;
        }
    }
    if ($cloning_server) {
        if (remote_cloning_connect($options)) {
            $return['cloning'] =
             rqp_server_info($remote_connections[$options->cloning_server]);
            if (is_soap_fault($return['cloning'])) {
                $return['cloning'] = false;
            }
        }
        else {
            $return['cloning'] = false;
        }
    }
    return $return;
}

function remote_item_info(&$options, $allinpackage=false) {
    global $remote_connections;

    if (!remote_rendering_connect($options)) {
        return false;
    }
    if ($allinpackage) {
        $items = rqp_item_info($remote_connections[$options->rendering_server],
         $options->source, $options->format, 0);
        if ($items->length > 0) {
            $items = array($items);
            for ($i = 1; $i < $items[0]->length; $i++) {
                $items[$i] =
                 rqp_item_info($remote_connections[$options->rendering_server],
                 $options->source, $options->format, $i);
            }
        }
        return $items;
    }
    return rqp_item_info($remote_connections[$options->rendering_server],
     $options->source, $options->format, 0);
}

/**
 * Perform a remote rendering operation on the RQP question
 *
 * @param string $output One of 'normal', 'readonly' or 'print'.
 */
function remote_render(&$question, &$state, $advanceState=false,
 $output='normal', $from=0, $to=0) {
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
    if (!remote_rendering_connect($options)) {
        return false;
    }
    $output = array();
    for ($i = $from; $i <= $to; $i++) {
        $output[$i] = rqp_render($remote_connections[$options->rendering_server],
         $options->source, $options->format, $state->options->persistent_data,
         $question->name_prefix, $responses, $advanceState,
         $renderFormat, $state->options->template_vars, 0, '', '', '', $i);
        if (false === $output[$i] || is_soap_fault($output[$i])) {
            return $output[$i];
        }
    }
    if ($from == $to) {
        return $output[$from];
    }
    return $output;
}

function remote_session_info(&$question, &$state, $from=0, $to=0) {
    global $remote_connections;

    // Make the code more readable
    $options =& $question->options;

    // Perform the RQP operation
    if (!remote_rendering_connect($options)) {
        return false;
    }
    $info = array();
    for ($i = $from; $i <= $to; $i++) {
        $info[$i] = rqp_session_info($remote_connections[$options->rendering_server],
         $options->source, $options->format, $state->options->persistent_data,
         $state->options->template_vars, 0, '', $i);
        if (false === $info[$i] || is_soap_fault($info[$i])) {
            return $info[$i];
        }
    }
    if ($from == $to) {
        return $info[$from];
    }
    return $info;
}

function remote_clone($options, $vars=array()) {
    global $remote_connections;

    if (!remote_cloning_connect($options)) {
        return false;
    }
    return rqp_clone($remote_connections[$options->cloning_server],
     $options->source, $options->format, $vars);
}

?>

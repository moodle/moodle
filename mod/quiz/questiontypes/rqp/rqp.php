<?php // $Id$
/**
* Library of functions binding RQP to SOAP
*
* @version $Id$
* @author Alex Smith and others members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

// Load the SOAP library that gives a unified wrapping to either the native
// PHP5 SOAP extension if available or to nuSOAP otherwise.
require_once('uni_soap.php');

/**
* Base RQP URI for RQP-defined identifiers
*
* RQP defines standard URIs for common values of the parameters. Currently
* there is no RQP domain so we define a base URI here so that it can be
* changed later.
*/
define('RQP_URI_BASE', 'http://rqp.org/');

/**
* RQP parameter URIs
*
* RQP defines standard URIs for common values of the parameters. These are
* defined in several categories under different directories under the base
* URI.
*/
define('RQP_URI_ERROR', RQP_URI_BASE . 'errors/');
define('RQP_URI_FORMAT', RQP_URI_BASE . 'formats/');
define('RQP_URI_OUTCOME', RQP_URI_BASE . 'outcomes/');
define('RQP_URI_COMPONENT', RQP_URI_BASE . 'components/');


/**
* Start a SOAP connection
*
* @param string $server  The URL of the RQP server that we want to connect to
* @return mixed          Returns a SoapClient object if connection is successful
*                        or false in the case of a soap fault.
*/
function rqp_connect($server) {
    $connection = soap_connect($server . '?wsdl');
    if (is_soap_fault($connection)) {
        return false;
    }
    return $connection;
}

/**
* Get server information using the RQP_ServerInformation operation
*
* @param SoapClient $connection  The URL of the RQP server that we want to connect to
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_server_info($connection) {
    return soap_call($connection, 'RQP_ServerInformation', array());
}

/**
* Get item information using the RQP_ItemInformation operation
*
* @param SoapClient $connection  The URL of the RQP server that we want to connect to
* @param string $source          Item source
* @param anyURI $format          Item format
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_item_info($connection, $source, $format='', $index=0) {
    $itemInfo = soap_call($connection, 'RQP_ItemInformation',
     array('source'=>$source, 'format'=>$format, 'index'=>$index));
    if (is_soap_fault($itemInfo)) {
        return $itemInfo;
    }
    // put the sourceErrors into an associative array indexed by the
    // error identifier
    $sourceErrors = array();
    if (!empty($itemInfo->sourceErrors)) {
        foreach ($itemInfo->sourceErrors as $error) {
            $id = $error->identifier;
            unset($error->identifier);
            $sourceErrors[$id] = (object) $error;
        }
    }
    $itemInfo->sourceErrors = $sourceErrors;
    return $itemInfo;
}

/**
* Process an item template to produce template variables using the RQP_ProcessTemplate operation
*
* @param SoapClient $connection  The URL of the RQP server that we want to connect to
* @param string $source          Item source
* @param anyURI $format          Item format
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_process_template($connection, $source, $format='') {
    $return = soap_call($connection, 'RQP_ProcessTemplate',
     array('source'=>$source, 'format'=>$format));
    if (is_soap_fault($return)) {
        return $return;
    }
    $templateVars = array();
    if (!empty($return->templateVars)) {
        foreach ($return->templateVars as $var) {
            $templateVars[$var->identifier] = $var->values;
        }
    }
    $return->templateVars = $templateVars;
    return $return;
}

/**
* Clone an item template using the RQP_ProcessTemplate operation
*
* @param SoapClient $connection  The URL of the RQP server that we want to connect to
* @param string $source          Item source
* @param anyURI $format          Item format
* @param array $templateVars     Associative array of template variables
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_clone($connection, $source, $format='', $templateVars=array()) {
    // make an array of key-value pairs from the template variables array
    array_walk($templateVars, create_function('&$val, $key',
     '$val = (object) array(\'identifier\'=>$key, \'values\'=>$val);'));

    $return = soap_call($connection, 'RQP_Clone', array('source'=>$source,
     'format'=>$format, 'templateVars'=>array_values($templateVars)));
    if (is_soap_fault($return)) {
        return $return;
    }
    $templateVars = array();
    if (!empty($return->templateVars)) {
        foreach ($return->templateVars as $var) {
            $templateVars[$var->identifier] = $var->values;
        }
    }
    $return->templateVars = $templateVars;
    return $return;
}

/**
* Get runtime information about the item in the given state using the 
* RQP_SessionInformation operation
*
* @param SoapClient $connection  The URL of the RQP server that we want to connect to
* @param string $source          Item source
* @param anyURI $format          Item format
* @param string $persistentData  String giving the state of the item session
* @param array $templateVars     Associative array of template variables
* @param string $embedPrefix
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_session_info($connection, $source, $format='', $persistentData='',
 $templateVars=array(), $embedPrefix='', $index=0) {
    // make an array of key-value pairs from the template variables array
    array_walk($templateVars, create_function('&$val, $key',
     '$val = (object) array(\'identifier\'=>$key, \'values\'=>$val);'));

    $return = soap_call($connection, 'RQP_SessionInformation',
     array('source'=>$source, 'format'=>$format, 'index'=>$index, 
     'templateVars'=>array_values($templateVars),
     'persistentData'=>$persistentData, 'embedPrefix'=>$embedPrefix));
    if (is_soap_fault($return)) {
        return $return;
    }
    $templateVars = array();
    if (!empty($return->templateVars)) {
        foreach ($return->templateVars as $var) {
            $templateVars[$var->identifier] = $var->values;
        }
    }
    $return->templateVars = $templateVars;
    $responses = array();
    if (!empty($return->correctResponses)) {
        foreach ($return->correctResponses as $var) {
            $responses[$var->name] = $var->value;
        }
    }
    $return->correctResponses = $responses;
    return $return;
}

/**
* Process and render the item in the given state using the RQP_Render operation
*
* @param SoapClient $connection  The URL of the RQP server that we want to connect to
* @param string $source          Item source
* @param anyURI $format          Item format
* @param string $persistentData  String giving the state of the item session
* @param string $embedPrefix
* @param array $responses        Student responses from the form elements
* @param boolean $advanceState   Should the item be advanced to the next state?
* @param anyURI $renderFormat
* @param array $templateVars     Associative array of template variables
* @param anyURI $mediaBase
* @param anyURI $appletBase
* @param anyURI $modalFormat
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_render($connection, $source, $format='', $persistentData='',
 $embedPrefix='', $responses=array(), $advanceState=true, $renderFormat='',
 $templateVars=array(), $mediaBase='', $appletBase='',
 $modalFormat='', $index=0) {
    // make an array of key-value pairs from the template vars array
    array_walk($templateVars, create_function('&$val, $key',
     '$val = (object) array(\'identifier\'=>$key, \'values\'=>$val);'));

    // make an array of name-value pairs from the responses array
    array_walk($responses, create_function('&$val, $key',
     '$val = (object) array(\'name\'=>$key, \'value\'=>$val);'));

    $return = soap_call($connection, 'RQP_Render', array('source'=>$source,
     'format'=>$format, 'index'=>$index, 'templateVars'=>array_values($templateVars),
     'persistentData'=>$persistentData, 'responses'=>$responses,
     'advanceState'=>$advanceState, 'embedPrefix'=>$embedPrefix,
     'appletBase'=>$appletBase, 'mediaBase'=>$mediaBase,
     'renderFormat'=>$renderFormat, 'modalFormat'=>$modalFormat));
    if (is_soap_fault($return)) {
        return $return;
    }
    $outcomeVars = array();
    if (!empty($return->outcomeVars)) {
        foreach ($return->outcomeVars as $var) {
            $outcomeVars[$var->identifier] = $var->values;
        }
    }
    $return->outcomeVars = $outcomeVars;

    $templateVars = array();
    if (!empty($return->templateVars)) {
        foreach ($return->templateVars as $var) {
            $templateVars[$var->identifier] = $var->values;
        }
    }
    $return->templateVars = $templateVars;

    $output = array();
    if (!empty($return->output)) {
        foreach ($return->output as $out) {
            $id = $out->identifier;
            unset($out->identifier);
            $output[$id] = $out;
        }
    }
    $return->output = $output;

    return $return;
}

/**
* Call to the RQP_Author operation
*
* @param SoapClient $connection  The URL of the RQP server
* @param string $source          Item source
* @param anyURI $format          Item format
* @param string $persistentData  String giving the state of the authoring session
* @param string $embedPrefix
* @param array $responses        Teacher responses from the form elements
* @param anyURI $renderFormat
* @return object    Object holding the return parameters or a SoapFault.
*/
function rqp_author($connection, $source, $format='', $persistentData='',
 $embedPrefix='', $responses=array(), $renderFormat='') {

    // make an array of name-value pairs from the responses array
    array_walk($responses, create_function('&$val, $key',
     '$val = (object) array(\'name\'=>$key, \'value\'=>$val);'));

    return soap_call($connection, 'RQP_Author', array('source'=>$source,
     'format'=>$format, 'persistentData'=>$persistentData, 'responses'=>$responses,
     'embedPrefix'=>$embedPrefix, 'renderFormat'=>$renderFormat));
}

?>

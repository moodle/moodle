<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web services wrapper library script.
 *
 * @package    core
 * @subpackage lib
 * @author     Alex Smith and others members of the Serving Mathematics project
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *             {@link http://maths.york.ac.uk/serving_maths}
 *             and others
 * @deprecated Moodle 4.5
 * @todo MDL-82194 Remove this file.
 */

defined('MOODLE_INTERNAL') || die();

debugging(
    'The soaplib.php file is deprecated and should not be used any more. ',
    DEBUG_DEVELOPER,
);

/**
* Create a new SoapClient object
*
* @param string $wsdl   URI of the WSDL file
* @param boolean $trace indicates if the soap messages should be saved (i.e. if
*                       get_soap_messages is used) and should be used only for debugging
* @return mixed         Returns either a SoapClient object or, if the connection failed,
*                       a SoapFault object.
*/
function soap_connect($wsdl, $trace=false) {
    try {
        $connection = new SoapClient($wsdl, array('soap_version'=>SOAP_1_1, 'exceptions'=>true, 'trace'=>$trace));
    }
    catch (SoapFault $f) {
        $connection = $f;
    }
    catch (Exception $e) {
        $connection = new SoapFault('client', 'Could not connect to the service');
    }
    return $connection;
}

/**
* Make a call to a SoapClient
*
* @param SoapClient $connection  The SoapClient to call
* @param string $call            Operation to be performed by client
* @param array $params           Parameters for the call
* @return mixed                  The return parameters of the operation or a SoapFault
*                                If the operation returned several parameters then these
*                                are returned as an object rather than an array
*/
function soap_call($connection, $call, $params) {
    try {
        $return = $connection->__soapCall($call, $params);
    }
    catch (SoapFault $f) {
        $return = $f;
    }
    catch (Exception $e) {
        $return = new SoapFault('client', 'Could call the method');
    }
    // return multiple parameters using an object rather than an array
    if (is_array($return)) {
        $keys = array_keys($return);
        $assoc = true;
        foreach ($keys as $key) {
            if (!is_string($key)) {
                $assoc = false;
                break;
            }
        }
        if ($assoc)
            $return = (object) $return;
    }
    return $return;
}

function soap_serve($wsdl, $functions) {
    // create server object
    $s = new SoapServer($wsdl);
    // export functions
    foreach ($functions as $func)
        $s->addFunction($func);
    // handle the request
    $s->handle();
}

function make_soap_fault($faultcode, $faultstring, $faultactor='', $detail='', $faultname='', $headerfault='') {
    return new SoapFault($faultcode, $faultstring, $faultactor, $detail, $faultname, $headerfault);
}

function get_last_soap_messages($connection) {
    return array('request'=>$connection->__getLastRequest(), 'response'=>$connection->__getLastResponse());
}

// Fix simple type encoding - work around a bug in early versions of PHP5 < 5.0.3, see http://bugs.php.net/bug.php?id=31832
function soap_encode($value, $name, $type, $namespace, $encode=XSD_STRING) {
    $value = new SoapVar($value, $encode, $type, $namespace);
    if ('' === $name)
        return $value;
    return new SoapParam($value, $name);
}

// Fix complex type encoding - work around a bug in early versions of PHP5 < 5.0.3, see http://bugs.php.net/bug.php?id=31832
function soap_encode_object($value, $name, $type, $namespace) {
    if (!is_object($value))
        return $value;
    $value = new SoapVar($value, SOAP_ENC_OBJECT, $type, $namespace);
    if ('' === $name)
        return $value;
    return new SoapParam($value, $name);
}

// Fix array encoding - work around a bug in early versions of PHP5 < 5.0.3, see http://bugs.php.net/bug.php?id=31832
function soap_encode_array($value, $name, $type, $namespace) {
    if (!is_array($value))
        return $value;
    $value = new SoapVar($value, SOAP_ENC_ARRAY, 'ArrayOf' . $type, $namespace);
    if ('' === $name)
        return $value;
    return new SoapParam($value, $name);
}

// In both cases...
function handle_soap_wsdl_request($wsdlfile, $address=false) {
    header('Content-type: application/wsdl+xml');
    $wsdl = file_get_contents($wsdlfile);
    if (false !== $address) {
        if (true === $address) {
            $address = (($_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        }
        $wsdl = str_replace('###SERVER_ADDRESS###', $address, $wsdl);
    }
    echo $wsdl;
    exit;
}

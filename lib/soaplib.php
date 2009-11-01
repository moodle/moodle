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
 * Web services wrapper library script
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (check_php_version('5') && class_exists('SoapClient')) {
    /** Use the native PHP5 support */
    require_once($CFG->libdir . '/soap/phpsoap.php');
}
else{
    /** Use nuSOAP instead */
    require_once($CFG->libdir . '/soap/nusoap.php');

    function make_soap_fault($faultcode, $faultstring, $faultactor='', $detail='', $faultname='', $headerfault='') {
        return new soap_fault($faultcode, $faultactor, $faultstring, $detail);
    }

    function is_soap_fault($obj) {
        if (!is_object($obj))
            return false;
        return (strcasecmp(get_class($obj), 'soap_fault') === 0);
    }

    if (class_exists('soap_client')) {
        function soap_connect($wsdl, $trace=false) {
            return new soap_client($wsdl, 'wsdl');
        }
    }
    else {
        /** @ignore */
        function soap_connect($wsdl, $trace=false) {
            return new soapclient($wsdl, 'wsdl');
        }
    }

    function soap_call($connection, $call, $params) {
        $result = $connection->call($call, $params);
        if ($connection->fault) {
            return @make_soap_fault($result['faultcode'], $result['faultstring'], '', $result['detail']);
        }
        if ($connection->error_str) {
            return @make_soap_fault('server', $connection->error_str, '', $connection->response);
        }
        /* Fix objects being returned as associative arrays (to fit with PHP5
        SOAP support */
        return fix_object($result);
    }

    function soap_serve($wsdl, $functions) {
        global $HTTP_RAW_POST_DATA;

        $s = new soap_server($wsdl);
        $s->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '');
    }

    function get_last_soap_messages($connection) {
        return array('request'=>$connection->request, 'response'=>$connection->response);
    }

    /* Fix objects being returned as associative arrays (to fit with PHP5
    SOAP support */
    function fix_object($value) {
        if (is_array($value)) {
            $value = array_map('fix_object', $value);
            $keys = array_keys($value);
            /* check for arrays of length 1 (they get given the key "item"
            rather than 0 by nusoap) */
            if (1 === count($value) && 'item' === $keys[0]) {
               $value = array_values($value);
            }
            else {
                /* cast to object if it is an associative array with at least
                one string key */
                foreach ($keys as $key) {
                    if (is_string($key)) {
                        $value = (object) $value;
                        break;
                    }
                }
            }
        }
        return $value;
    }

    // Fix simple type encoding - not needed for nuSOAP
    function soap_encode($value, $name, $type, $namespace, $encode=0) {
        return $value;
    }

    // Fix complex type encoding - not needed for nuSOAP
    function soap_encode_object($value, $name, $type, $namespace) {
        return $value;
    }

    // Fix array encoding - not needed for nuSOAP
    function soap_encode_array($value, $name, $type, $namespace) {
        return $value;
    }
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

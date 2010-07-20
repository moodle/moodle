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
 * AMF web service implementation classes and methods.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");
require_once( "{$CFG->dirroot}/webservice/amf/introspector.php");
require_once 'Zend/Amf/Server.php';
/**
 * Exception indicating an invalid return value from a function.
 * Used when an externallib function does not return values of the expected structure. 
 */
class invalid_return_value_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo some detailed information
     */
    function __construct($debuginfo=null) {
        parent::__construct('invalidreturnvalue', 'webservice_amf', '', $debuginfo, $debuginfo);
    }
}

/**
 * AMF service server implementation.
 * @author Petr Skoda (skodak)
 */
class webservice_amf_server extends webservice_zend_server {
    /**
     * Contructor
     * @param integer $authmethod authentication method - one of WEBSERVICE_AUTHMETHOD_*
     */
    public function __construct($authmethod) {
        parent::__construct($authmethod, 'Moodle_Amf_Server');
        $this->wsname = 'amf';
    }
    protected function init_service_class(){
        parent::init_service_class();
        //allow access to data about methods available.
        $this->zend_server->setClass( "MethodDescriptor" );
        MethodDescriptor::$classnametointrospect = $this->service_class;
    }
    
    protected function service_class_method_body($function, $params){
        //cast the param from object to array (validate_parameters except array only)
        $castingcode = '';
        if ($params){
            $paramstocast = explode(',', $params);
            foreach ($paramstocast as $paramtocast) {
                $paramtocast = trim($paramtocast);
                $castingcode .= $paramtocast .
                '=webservice_zend_server::cast_objects_to_array('.$paramtocast.');';
        }

        }

        $externallibcall = $function->classname.'::'.$function->methodname.'('.$params.')';
        $descriptionmethod = $function->methodname.'_returns()';
        $callforreturnvaluedesc = $function->classname.'::'.$descriptionmethod;
        return $castingcode . 
'        return webservice_amf_server::validate_and_cast_values('.$callforreturnvaluedesc.', '.$externallibcall.');';
    }
    /**
     * Validates submitted value, comparing it to a description. If anything is incorrect
     * invalid_return_value_exception is thrown. Also casts the values to the type specified in
     * the description.
     * @param mixed $description description of parameters or null if no return value
     * @param mixed $value the actual values
     * @param boolean $singleasobject specifies whether a external_single_structure should be cast to a stdClass object
     *                                 should always be false for use in validating parameters in externallib functions.
     * @return mixed params with added defaults for optional items, invalid_parameters_exception thrown if any problem found
     */
    public static function validate_and_cast_values($description, $value) {
        if (is_null($description)){
            return;
        }
        if ($description instanceof external_value) {
            if (is_array($value) or is_object($value)) {
                throw new invalid_return_value_exception('Scalar type expected, array or object received.');
            }

            if ($description->type == PARAM_BOOL) {
                // special case for PARAM_BOOL - we want true/false instead of the usual 1/0 - we can not be too strict here ;-)
                if (is_bool($value) or $value === 0 or $value === 1 or $value === '0' or $value === '1') {
                    return (bool)$value;
                }
            }
            return validate_param($value, $description->type, $description->allownull, 'Invalid external api parameter');

        } else if ($description instanceof external_single_structure) {
            if (!is_array($value)) {
                throw new invalid_return_value_exception('Only arrays accepted.');
            }
            $result = array();
            foreach ($description->keys as $key=>$subdesc) {
                if (!array_key_exists($key, $value)) {
                    if ($subdesc->required == VALUE_REQUIRED) {
                        throw new invalid_return_value_exception('Missing required key in single structure: '.$key);
                    }
                    if ($subdesc instanceof external_value) {
                            if ($subdesc->required == VALUE_DEFAULT) {
                                $result[$key] = self::validate_and_cast_values($subdesc, $subdesc->default);
                            }
                    }
                } else {
                    $result[$key] = self::validate_and_cast_values($subdesc, $value[$key]);
                }
                unset($value[$key]);
            }
/*          Was decided that extra keys should just be ignored and not returned.
 *          if (!empty($value)) {
                throw new invalid_return_value_exception('Unexpected keys detected in parameter array.');
            }*/
            return (object)$result;

        } else if ($description instanceof external_multiple_structure) {
            if (!is_array($value)) {
                throw new invalid_return_value_exception('Only arrays accepted.');
            }
            $result = array();
            foreach ($value as $param) {
                $result[] = self::validate_and_cast_values($description->content, $param);
            }
            return $result;

        } else {
            throw new invalid_return_value_exception('Invalid external api description.');
        }
    }    
    
    /**
     * Set up zend service class
     * @return void
     */
    protected function init_zend_server() {
        parent::init_zend_server();
        $this->zend_server->setProduction(false); //set to false for development mode
                                                 //(complete error message displayed into your AMF client)
        // TODO: add some exception handling
    }


}
class Moodle_Amf_Server extends Zend_Amf_Server{
    /**
     * Raise a server fault
     *
     * @param  string|Exception $fault
     * @return void
     */
    public function fault($fault = null, $code = 404)
    {
        if (!$fault instanceof Exception) {
            $fault = new Exception($fault);
        }
        $request = $this->getRequest();
        // Get the object encoding of the request.
        $objectEncoding = $request->getObjectEncoding();

        // create a response object to place the output from the services.
        $response = $this->getResponse();

        // set reponse encoding
        $response->setObjectEncoding($objectEncoding);
        
        $responseBody = $request->getAmfBodies();

        foreach($responseBody as $body){
            $return = $this->_errorMessage($objectEncoding, $fault->getMessage(), 
                $fault->getMessage(), $fault->getTraceAsString(),$fault->getCode(),  $fault->getLine());
            $responseType = Zend_AMF_Constants::STATUS_METHOD;
    
    
            $responseURI = $body->getResponseURI() . $responseType;
            $newBody     = new Zend_Amf_Value_MessageBody($responseURI, null, $return);
            $response->addAmfBody($newBody);
        }
        $response->finalize();
        echo $response;
    }
}

// TODO: implement AMF test client somehow, maybe we could use moodle form to feed the data to the flash app somehow

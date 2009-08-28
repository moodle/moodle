<?php
/*
* Created on 01/12/2008
 *
 * Moodle base webservice api
 *
 * @author Jerome Mouneyrac
 */

/**
 * DO NOT USE ANYTHING FROM THIS FILE - WORK IN PROGRESS
 */
abstract class moodle_external {

    protected $descriptions;

    /**
     * Constructor - We set the description of this API in order to be access by Web service
     */
    function __construct () {
        $this->descriptions = array();
    }

    /**
     *
     *  @param string $functionname
     *  @return array
     */
    public function get_function_webservice_description($functionname) {
        if (key_exists($functionname, $this->descriptions)) {
            return $this->descriptions[$functionname];
        }
        else {
            return false;
        }
    }

    /**
     *
     * @return array
     */
    public function get_descriptions() {
        return $this->descriptions;
    }

    /**
     * This function clean params, because it should only be called by external itself, it has to be protected (server should not call it)
     * @param array $params
     */
    protected function clean_function_params($functionname, &$params) {
        $description = $this->get_function_webservice_description($functionname);
        $this->clean_object($description['params'], $params);
    }

    /**
     *
     * @param <type> $params
     */
    protected function clean_params($description, &$params) {

        if (is_array($params) ) { //it's a list
            $nextdescriptionkey = key($description);
            if (isset($nextdescriptionkey)) {
                $this->clean_params($description[$nextdescriptionkey], $params[key($params)]);
            } else {            
                throw new moodle_exception('wswrongparams');
            }
        }
        else {
            if (is_object($params)) { //is it a object
                $this->clean_object($description, $params);
            }
            else { //it's a primary type
                $params = clean_param($params, $description);
            }
        }

    }

    protected function  clean_object($objectdescription, &$paramobject) {
        foreach (get_object_vars($paramobject) as $propertyname => $propertyvalue) {
            if (is_array($propertyvalue)) {
                if (isset($objectdescription->$propertyname)) {
                    $this->clean_params($objectdescription->$propertyname, $propertyvalue);
                    $paramobject->$propertyname = $propertyvalue;
                } else {               
                    throw new moodle_exception('wswrongparams');
                }
            } else {
                if (isset($objectdescription->$propertyname)) {          
                    $paramobject->$propertyname = clean_param($propertyvalue, $objectdescription->$propertyname);
                } else { 
                    throw new moodle_exception('wswrongparams');
                }
            }
        }
    }

}
?>

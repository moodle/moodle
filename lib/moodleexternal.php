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
         varlog($functionname);
        foreach ($params as $param) { //we are applying the algo for all params
            $key = key($description['params']); //get next key of the description array => params need to be ordered !         
            $this->clean_params($description['params'][$key], $param);
           
        }
    }

    /**
     *
     * @param <type> $params
     */
    protected function clean_params($description, &$params) {
        if (!is_array($params)) {
             $paramvalue = clean_param($params, $description);
        } else {
        foreach ($params as $paramname => &$paramvalue) {
            if (is_array($paramvalue)) { //it's a list
            //A description array does not support list of different objects
            //it's why we retrieve the first key, because there should be only one key
                $this->clean_params($description[key($description)], $paramvalue);
            }
            else {
                if (is_object($paramvalue)) { //is it a object
                    $this->clean_object_types($description[$paramname], $paramvalue);
                }
                else { //it's a primary type
                    $paramvalue = clean_param($paramvalue, $description[$paramname]);
                }
            }
             

        }

        }
    }

    protected function  clean_object_types($objectdescription, &$paramobject) {
        foreach (get_object_vars($paramobject) as $propertyname => $propertyvalue) {
            if (is_array($propertyvalue)) {
                $this->clean_params($objectdescription->$propertyname, $propertyvalue);
                $paramobject->$propertyname = $propertyvalue;
            } else {
                $paramobject->$propertyname = clean_param($propertyvalue, $objectdescription->$propertyname);

            }
        }
    }

}
?>

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
abstract class moodle_ws_api {

 protected $descriptions;

    /**
     * Constructor - We set the description of this API in order to be access by Web service
     */
    function __construct () {
          $this->descriptions = array();
         
    }

     /**
     *
     *  @param <type> $functionname
     */
    function get_function_webservice_description($functionname) {
        if (key_exists($functionname, $this->descriptions)) {
            return $this->descriptions[$functionname];
        }
        else {
            return false;
        }
    }

    function get_descriptions() {
        return $this->descriptions;
    }

}
?>

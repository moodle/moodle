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
 protected $user;

    /**
     * Constructor - We set the description of this API in order to be access by Web service
     */
    function __construct ($user = null) {
        $this->descriptions = array();
    }

     /**
     *
     *  @param <type> $functionname
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
     * @return <type> 
     */
    public function get_descriptions() {
        return $this->descriptions;
    }

}
?>

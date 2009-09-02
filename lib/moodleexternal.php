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
 * moodleexternal.php - parent class of any external.php file into Moodle
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class moodle_external {

    protected $descriptions; //the web service description of the external.php functions

    function __construct () {
        $this->descriptions = array();
    }

    /**
     * Return web service description for a specific function
     *  @param string $functionname
     *  @return array the web service description of the function, return false if the function name doesn't exist
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
     * Return web service description for all web service functions of the external class
     * @return array
     */
    public function get_descriptions() {
        return $this->descriptions;
    }

    /**
     * This function clean params,
     * It's protected because we should only clean the params into external files (not server layer)
     * @param array $params
     */
    protected function clean_function_params($functionname, &$params) {
        $description = $this->get_function_webservice_description($functionname);
        $this->clean_object($description['params'], $params);
    }


     /**
      * Clean an array  param
      * @param array $description - an array with only one element !
      * @param array $params - an array with one or several elements
      */
    protected function clean_params($description, &$params) {
        foreach ($params as &$param) {
            if (is_array($param) ) { //it's a list
                    $this->clean_params($description[0], $param);
            }
            else {
                if (is_object($param)) { //it's an object
                    $this->clean_object($description[0], $param);
                }
                else { //it's a primary type
                    $param = clean_param($param, $description[0]);
                }
            }
        }
    }

    /**
     * Clean an object param
     * @param object $objectdescription
     * @param object $paramobject
     */
    protected function  clean_object($objectdescription, &$paramobject) {
        foreach (get_object_vars($paramobject) as $propertyname => $propertyvalue) {
            if (!isset($objectdescription->$propertyname)) { //if the param is not defined into the web service description
                throw new moodle_exception('wswrongparams'); //throw exception
            }
            if (is_array($propertyvalue)) { //the object property is a list
                $this->clean_params($objectdescription->$propertyname, $propertyvalue);
                $paramobject->$propertyname = $propertyvalue;

            }
            else {
                if (is_object($propertyvalue)) { //the object property is an object
                    $this->clean_object($objectdescription->$propertyname, $propertyvalue);
                    $paramobject->$propertyname = $propertyvalue;
                }
                else { //the object property is a primary type
                    $paramobject->$propertyname = clean_param($propertyvalue, $objectdescription->$propertyname);

                }
            }
        }
    }

}


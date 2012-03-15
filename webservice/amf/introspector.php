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
 * Introspection for amf
 *
 * Figures out where all the services are and
 * returns a list of their available methods.
 * Requires $CFG->amf_introspection = true for security.
 *
 * @package    webservice_amf
 * @copyright  2009 Penny Leach <penny@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Provides a function to get details of methods available on another class.
 *
 * @package    webservice_amf
 * @copyright  HP
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MethodDescriptor {

    /** @var array The details of the methods*/
    private $methods;

    /** @var array Classes to introspect
     *  Note: setup() code has been written to introspect multiple classes.
     *  However the setup() only deal with $classnametointrospect.
     */
    private $classes;

    /** @var string Class to introspect */
    static public $classnametointrospect;
    
    /**
     * constructor
     */
    public function __construct() {
        $this->setup();
    }

    /**
     * Generate the class method descriptions.
     * These description are assigned in the class properties
     *
     * @return void
     */
    private function setup() {
        global $CFG;
    	if (!empty($this->nothing)) {
            return; // we've already tried, no classes.
        }
        if (!empty($this->classes)) { // we've already done it successfully.
            return;
        }

        //TODO MDL-31148 most likely can be removed, but check if there is any interest, never know...
        /*if (empty($CFG->amf_introspection)) {
            throw new Exception(get_string('amfintrospectiondisabled', 'local'));
        }*/
        
        //TODO MDL-31148 just one class here, possibility for expansion in future
        $classes = array(MethodDescriptor::$classnametointrospect);

        $hugestructure = array();

        foreach ($classes as $c) {
            $r = new ReflectionClass($c);

            if (!$methods = $r->getMethods()) {
                continue;
            }
            $this->classes[] = $c;
            $hugestructure[$c] = array('docs' => $r->getDocComment(), 'methods' => array());
            foreach ($methods as $method) {
                if (!$method->isPublic()) {
                    continue;
                }
                $params = array();
                foreach ($method->getParameters() as $param) {
                    $params[] = array('name' => $param->getName(), 'required' => !$param->isOptional());
                }
                $hugestructure[$c]['methods'][$method->getName()] = array(
                    'docs' => $method->getDocComment(),
                    'params' => $params,
                );
            }
        }
        $this->methods = $hugestructure;
        if (empty($this->classes)) {
            $this->nothing = true;
        }
    }

    /**
     * Get the method descriptions
     *
     * @return array
     */
    public function getMethods() {
        $this->setup();
        return $this->methods;
    }

    /**
     * Get the class descriptions
     *
     * @return array
     */
    public function getClasses() {
        $this->setup();
        return $this->classes;
    }
    
    /**
     * As the class does not extend another class and as this function does nothing
     * except return true,
     * I guess this is just a function that was a copy/paste and it has been forgotten.
     * TODO MDL-31148 this function is not called and most likely can be removed
     *
     * @return true
     */
    public function isConnected() {
        return true;
    }
}


<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @author     Penny Leach <penny@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * Introspection for amf - figures out where all the services are and
 * returns a list of their available methods.
 * Requires $CFG->amf_introspection = true for security.
 */


/**
 * Provides a function to get details of methods available on another class.
 * @author HP
 *
 */
class MethodDescriptor {

    private $methods;
    private $classes;

	static public $classnametointrospect;
    
    
    public function __construct() {
        $this->setup();
    }

    private function setup() {
        global $CFG;
    	if (!empty($this->nothing)) {
            return; // we've already tried, no classes.
        }
        if (!empty($this->classes)) { // we've already done it successfully.
            return;
        }
        /*if (empty($CFG->amf_introspection)) {
            throw new Exception(get_string('amfintrospectiondisabled', 'local'));
        }*/
        
        //just one class here, possibility for expansion in future
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

    public function getMethods() {
        $this->setup();
        return $this->methods;
    }

    public function getClasses() {
        $this->setup();
        return $this->classes;
    }
    
    public function isConnected() {
        return true;
    }
}


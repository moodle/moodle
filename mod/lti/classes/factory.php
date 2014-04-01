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
 * Class Builder
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */

namespace mod_lti;

use coding_exception;
use mod_lti\observer\dispatcher;

/**
 * Builds various classes
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */
class factory {
    /**
     * Given a component and class name suffix, create a full
     * class name and ensure that it exists.
     *
     * Classes are namespace based.
     *
     * @param string $component The component to find the class file in
     * @param string $suffix This is appended to the component name, together make the class name we want
     * @return string
     * @throws \coding_exception
     */
    protected function build_class_name($component, $suffix) {
        $classname = "\\$component\\$suffix";
        if (!class_exists($classname)) {
            throw new coding_exception("Expected to find $classname in the classes directory of $component");
        }
        return $classname;
    }

    /**
     * Instantiate a class and optionally verify parent class
     *
     * @param string $class Create a new instance of this class
     * @param null|string $parent Ensure that this is the parent class
     * @return mixed
     * @throws coding_exception
     */
    protected function build_generic_instance($class, $parent = null) {
        if (!is_null($parent)) {
            $reflection = new \ReflectionClass($class);
            if (!$reflection->isSubclassOf($parent)) {
                throw new coding_exception("The $class must be a subclass of $parent");
            }
        }
        return new $class();
    }

    /**
     * Builds a single ltisource plugin listener
     *
     * @param string $component The component to find the class file in
     * @return \mod_lti\observer\listener_interface
     */
    public function build_listener($component) {
        return $this->build_generic_instance(
            $this->build_class_name($component, 'listener'),
            '\mod_lti\observer\listener_interface'
        );
    }

    /**
     * Builds ltisource plugin listeners
     *
     * @return \mod_lti\observer\listener_interface[]
     */
    public function build_listeners() {
        $plugins   = \core_component::get_plugin_list('ltisource');
        $listeners = array();
        foreach (array_keys($plugins) as $pluginname) {
            try {
                $listeners[] = $this->build_listener('ltisource_'.$pluginname);
            } catch (\Exception $e) {
                // Class is optional, so ignore if not found.
            }
        }
        return $listeners;
    }

    /**
     * Builds an event dispatcher with listeners.
     *
     * @return dispatcher
     */
    public function build_dispatcher() {
        $dispatcher = new dispatcher();
        $dispatcher->set_listeners($this->build_listeners());

        return $dispatcher;
    }
}
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
 * Advanced grading methods support
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Factory method returning an instance of the grading manager
 *
 * @param stdClass $context
 * @param string $component the frankenstyle name of the component
 * @param string $areaname the name of the gradable area
 * @return grading_manager
 */
function get_grading_manager($context = null, $component = null, $areaname = null) {

    $manager = new grading_manager();

    if (!is_null($context)) {
        $manager->set_context($context);
    }

    if (!is_null($component)) {
        $manager->set_component($component);
    }

    if (!is_null($areaname)) {
        $manager->set_areaname($areaname);
    }

    return $manager;
}

/**
 * General class providing access to common grading features
 *
 * Fully initialized instance of the grading manager operates over a single
 * gradable area. It is possible to work with a partially initialized manager
 * that knows just context and component without known areaname, for example.
 * It is also possible to change context, component and areaname of an existing
 * manager. Such pattern is used when copying form definitions, for example.
 */
class grading_manager {

    /** @var stdClass the context */
    protected $context;

    /** @var string the frankenstyle name of the component */
    protected $component;

    /** @var string the name of the gradable area */
    protected $areaname;

    /**
     * Sets the context the manager operates on
     *
     * @param stdClass $context
     */
    public function set_context(stdClass $context) {
        $this->context = $context;
    }

    /**
     * Sets the component the manager operates on
     *
     * @param string $component the frankenstyle name of the component
     */
    public function set_component($component) {
        $this->component = $component;
    }

    /**
     * Sets the areaname the manager operates on
     *
     * @param string $areaname the name of the gradable area
     */
    public function set_areaname($areaname) {
        $this->areaname = $areaname;
    }

    /**
     * Returns the list of available grading methods in the given context
     *
     * Basically this returns the list of installed grading plugins with an empty value
     * for simple direct grading. In the future, the list of available methods may be
     * controlled per-context.
     *
     * Requires the context property to be set in advance.
     * @return array of the (string)name => (string)localized title of the method
     */
    public function get_available_methods() {

        $this->ensure_isset(array('context'));

        // todo - hardcoded list for now, should read the list of installed grading plugins
        return array(
            '' => get_string('gradingmethodnone', 'core_grading'),
            'rubric' => 'Rubric',
        );
    }

    /**
     * Returns the list of gradable areas in the given context and component
     *
     * This performs a callback to the library of the relevant plugin to obtain
     * the list of supported areas.
     * @return array of (string)areacode => (string)localized title of the area
     */
    public function get_available_areas() {
        global $CFG;

        $this->ensure_isset(array('context', 'component'));

        // example: if the given context+component lead to mod_assignment, this method
        // will do something like
        // require_once($CFG->dirroot.'/mod/assignment/lib.php');
        // return assignment_gradable_area_list();

        // todo - hardcoded list for now
        return array('submission' => get_string('assignmentsubmission', 'assignment'));
    }

    /**
     * Returns the currently active grading method in the given gradable area
     *
     * @return string the name of the grading plugin
     */
    public function get_active_area_method() {
        $this->ensure_isset(array('context', 'component', 'areaname'));
        // todo - hardcoded value for now
        return 'rubric';
    }

    /**
     * Make sure that the given properties were set to some not-null value
     *
     * @param array $properties the list of properties
     * @throws coding_exception
     */
    private function ensure_isset(array $properties) {
        foreach ($properties as $property) {
            if (!isset($this->$property)) {
                throw new coding_exception('The property '.$property.' is not set.');
            }
        }
    }
}

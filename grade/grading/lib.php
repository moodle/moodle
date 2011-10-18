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
 * @param stdClass|int $context or $areaid
 * @param string $component the frankenstyle name of the component
 * @param string $area the name of the gradable area
 * @return grading_manager
 */
function get_grading_manager($context_or_areaid = null, $component = null, $area = null) {
    global $DB;

    $manager = new grading_manager();

    if (is_object($context_or_areaid)) {
        $context = $context_or_areaid;
    } else {
        $context = null;

        if (is_numeric($context_or_areaid)) {
            $manager->load($context_or_areaid);
            return $manager;
        }
    }

    if (!is_null($context)) {
        $manager->set_context($context);
    }

    if (!is_null($component)) {
        $manager->set_component($component);
    }

    if (!is_null($area)) {
        $manager->set_area($area);
    }

    return $manager;
}

/**
 * General class providing access to common grading features
 *
 * Grading manager provides access to the particular grading method controller
 * in that area.
 *
 * Fully initialized instance of the grading manager operates over a single
 * gradable area. It is possible to work with a partially initialized manager
 * that knows just context and component without known area, for example.
 * It is also possible to change context, component and area of an existing
 * manager. Such pattern is used when copying form definitions, for example.
 */
class grading_manager {

    /** @var stdClass the context */
    protected $context;

    /** @var string the frankenstyle name of the component */
    protected $component;

    /** @var string the name of the gradable area */
    protected $area;

    /** @var stdClass|false|null the raw record from {grading_areas}, false if does not exist, null if invalidated cache */
    private $areacache = null;

    /**
     * @return stdClass grading manager context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Sets the context the manager operates on
     *
     * @param stdClass $context
     */
    public function set_context(stdClass $context) {
        $this->areacache = null;
        $this->context = $context;
    }

    /**
     * @return string grading manager component
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * Sets the component the manager operates on
     *
     * @param string $component the frankenstyle name of the component
     */
    public function set_component($component) {
        $this->areacache = null;
        list($type, $name) = normalize_component($component);
        $this->component = $type.'_'.$name;
    }

    /**
     * @return string grading manager area name
     */
    public function get_area() {
        return $this->area;
    }

    /**
     * Sets the area the manager operates on
     *
     * @param string $area the name of the gradable area
     */
    public function set_area($area) {
        $this->areacache = null;
        $this->area = $area;
    }

    /**
     * Returns a text describing the context and the component
     *
     * At the moment this works for gradable areas in course modules. In the future, this
     * method should be improved so it works for other contexts (blocks, gradebook items etc)
     * or subplugins.
     *
     * @return string
     */
    public function get_component_title() {

        $this->ensure_isset(array('context', 'component'));
        list($context, $course, $cm) = get_context_info_array($this->get_context()->id);

        if (!empty($cm->name)) {
            $title = $cm->name;
        } else {
            debugging('Gradable areas are currently supported at the course module level only', DEBUG_DEVELOPER);
            $title = $this->get_component();
        }

        return $title;
    }

    /**
     * Returns the localized title of the currently set area
     *
     * @return string
     */
    public function get_area_title() {

        $this->ensure_isset(array('context', 'component', 'area'));
        $areas = $this->get_available_areas();

        return $areas[$this->get_area()];
    }

    /**
     * Loads the gradable area info from the database
     *
     * @param int $areaid
     */
    public function load($areaid) {
        global $DB;

        $this->areacache = $DB->get_record('grading_areas', array('id' => $areaid), '*', MUST_EXIST);
        $this->context = get_context_instance_by_id($this->areacache->contextid, MUST_EXIST);
        $this->component = $this->areacache->component;
        $this->area = $this->areacache->areaname;
    }

    /**
     * Returns the list of available grading methods in the given context
     *
     * Basically this returns the list of installed grading plugins with an empty value
     * for simple direct grading. In the future, the list of available methods may be
     * controlled per-context.
     *
     * Requires the context property to be set in advance.
     *
     * @param bool $includenone should the 'Simple direct grading' be included
     * @return array of the (string)name => (string)localized title of the method
     */
    public function get_available_methods($includenone = true) {

        $this->ensure_isset(array('context'));

        if ($includenone) {
            $list = array('' => get_string('gradingmethodnone', 'core_grading'));
        } else {
            $list = array();
        }

        foreach (get_plugin_list('gradingform') as $name => $location) {
            $list[$name] = get_string('pluginname', 'gradingform_'.$name);
        }

        return $list;
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
        return array('submission' => 'Submissions');
    }

    /**
     * Returns the currently active grading method in the gradable area
     *
     * @return string|null the name of the grading plugin of null if it has not been set
     */
    public function get_active_method() {
        global $DB;

        $this->ensure_isset(array('context', 'component', 'area'));

        // get the current grading area record if it exists
        if (is_null($this->areacache)) {
            $this->areacache = $DB->get_record('grading_areas', array(
                'contextid' => $this->context->id,
                'component' => $this->component,
                'areaname'  => $this->area),
            '*', IGNORE_MISSING);
        }

        if ($this->areacache === false) {
            // no area record yet
            return null;
        }

        return $this->areacache->activemethod;
    }

    /**
     * Sets the currently active grading method in the gradable area
     *
     * @param string $method the method name, eg 'rubric' (must be available)
     * @return bool true if the method changed or was just set, false otherwise
     */
    public function set_active_method($method) {
        global $DB;

        $this->ensure_isset(array('context', 'component', 'area'));

        // make sure the passed method is empty or a valid plugin name
        if (empty($method)) {
            $method = null;
        } else {
            if ('gradingform_'.$method !== clean_param('gradingform_'.$method, PARAM_COMPONENT)) {
                throw new moodle_exception('invalid_method_name', 'core_grading');
            }
            $available = $this->get_available_methods(false);
            if (!array_key_exists($method, $available)) {
                throw new moodle_exception('invalid_method_name', 'core_grading');
            }
        }

        // get the current grading area record if it exists
        if (is_null($this->areacache)) {
            $this->areacache = $DB->get_record('grading_areas', array(
                'contextid' => $this->context->id,
                'component' => $this->component,
                'areaname'  => $this->area),
            '*', IGNORE_MISSING);
        }

        $methodchanged = false;

        if ($this->areacache === false) {
            // no area record yet, create one with the active method set
            $area = array(
                'contextid'     => $this->context->id,
                'component'     => $this->component,
                'areaname'      => $this->area,
                'activemethod'  => $method);
            $DB->insert_record('grading_areas', $area);
            $methodchanged = true;

        } else {
            // update the existing record if needed
            if ($this->areacache->activemethod !== $method) {
                $DB->set_field('grading_areas', 'activemethod', $method, array('id' => $this->areacache->id));
                $methodchanged = true;
            }
        }

        $this->areacache = null;

        return $methodchanged;
    }

    /**
     * Extends the settings navigation with the grading settings
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING and the user has the permission moodle/grade:managegradingforms.
     *
     * @param settings_navigation $settingsnav {@link settings_navigation}
     * @param navigation_node $modulenode {@link navigation_node}
     */
    public function extend_settings_navigation(settings_navigation $settingsnav, navigation_node $modulenode=null) {

        $this->ensure_isset(array('context', 'component'));

        $areas = $this->get_available_areas();

        if (empty($areas)) {
            // no money, no funny
            return;

        } else if (count($areas) == 1) {
            // make just a single node for the management screen
            $areatitle = reset($areas);
            $areaname  = key($areas);
            $this->set_area($areaname);
            $method = $this->get_active_method();
            $managementnode = $modulenode->add(get_string('gradingmanagement', 'core_grading'),
                $this->get_management_url(), settings_navigation::TYPE_CUSTOM);
            if ($method) {
                $controller = $this->get_controller($method);
                $controller->extend_settings_navigation($settingsnav, $managementnode);
            }

        } else {
            // make management screen node for each area
            $managementnode = $modulenode->add(get_string('gradingmanagement', 'core_grading'),
                null, settings_navigation::TYPE_CUSTOM);
            foreach ($areas as $areaname => $areatitle) {
                $this->set_area($areaname);
                $method = $this->get_active_method();
                $node = $managementnode->add($areatitle,
                    $this->get_management_url(), settings_navigation::TYPE_CUSTOM);
                if ($method) {
                    $controller = $this->get_controller($method);
                    $controller->extend_settings_navigation($settingsnav, $node);
                }
            }
        }
    }

    /**
     * Returns the given method's controller in the gradable area
     *
     * @param string $method the method name, eg 'rubric' (must be available)
     * @return grading_controller
     */
    public function get_controller($method) {
        global $CFG;

        $this->ensure_isset(array('context', 'component', 'area'));

        // make sure the passed method is a valid plugin name
        if ('gradingform_'.$method !== clean_param('gradingform_'.$method, PARAM_COMPONENT)) {
            throw new moodle_exception('invalid_method_name', 'core_grading');
        }
        $available = $this->get_available_methods(false);
        if (!array_key_exists($method, $available)) {
            throw new moodle_exception('invalid_method_name', 'core_grading');
        }

        // get the current grading area record if it exists
        if (is_null($this->areacache)) {
            $this->areacache = $DB->get_record('grading_areas', array(
                'contextid' => $this->context->id,
                'component' => $this->component,
                'areaname'  => $this->area),
            '*', IGNORE_MISSING);
        }

        if ($this->areacache === false) {
            // no area record yet, create one
            $area = array(
                'contextid' => $this->context->id,
                'component' => $this->component,
                'areaname'  => $this->area);
            $areaid = $DB->insert_record('grading_areas', $area);
            // reload the cache
            $this->areacache = $DB->get_record('grading_areas', array('id' => $areaid), '*', MUST_EXIST);
        }

        require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
        $classname = 'gradingform_'.$method.'_controller';

        return new $classname($this->context, $this->component, $this->area, $this->areacache->id);
    }

    /**
     * Returns the controller for the active method if it is available
     *
     * @return null|grading_controller
     */
    public function get_active_controller() {
        if ($gradingmethod = $this->get_active_method()) {
            $controller = $this->get_controller($gradingmethod);
            if ($controller->is_form_available()) {
                return $controller;
            }
        }
        return null;
    }

    /**
     * Returns the URL of the grading area management page
     *
     * @param moodle_url $returnurl optional URL of the page where the user should be sent back to
     * @return moodle_url
     */
    public function get_management_url(moodle_url $returnurl = null) {

        $this->ensure_isset(array('context', 'component'));

        if ($this->areacache) {
            $params = array('areaid' => $this->areacache->id);
        } else {
            $params = array('contextid' => $this->context->id, 'component' => $this->component);
            if ($this->area) {
                $params['area'] = $this->area;
            }
        }

        if (!is_null($returnurl)) {
            $params['returnurl'] = $returnurl->out(false);
        }

        return new moodle_url('/grade/grading/manage.php', $params);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Make sure that the given properties were set to some not-null value
     *
     * @param array $properties the list of properties
     * @throws coding_exception
     */
    private function ensure_isset(array $properties) {
        foreach ($properties as $property) {
            if (!isset($this->$property)) {
                throw new coding_exception('The property "'.$property.'" is not set.');
            }
        }
    }
}

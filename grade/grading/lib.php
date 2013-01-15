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
 * @package    core_grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Factory method returning an instance of the grading manager
 *
 * There are basically ways how to use this factory method. If the area record
 * id is known to the caller, get the manager for that area by providing just
 * the id. If the area record id is not know, the context, component and area name
 * can be provided. Note that null values are allowed in the second case as the context,
 * component and the area name can be set explicitly later.
 *
 * @category grading
 * @example $manager = get_grading_manager($areaid);
 * @example $manager = get_grading_manager(get_system_context());
 * @example $manager = get_grading_manager($context, 'mod_assignment', 'submission');
 * @param stdClass|int|null $context_or_areaid if $areaid is passed, no other parameter is needed
 * @param string|null $component the frankenstyle name of the component
 * @param string|null $area the name of the gradable area
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
 *
 * @package    core_grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category   grading
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
     * Returns grading manager context
     *
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
     * Returns grading manager component
     *
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
     * Returns grading manager area name
     *
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

        if ($this->get_context()->contextlevel == CONTEXT_SYSTEM) {
            if ($this->get_component() == 'core_grading') {
                $title = ''; // we are in the bank UI
            } else {
                throw new coding_exception('Unsupported component at the system context');
            }

        } else if ($this->get_context()->contextlevel >= CONTEXT_COURSE) {
            list($context, $course, $cm) = get_context_info_array($this->get_context()->id);

            if (!empty($cm->name)) {
                $title = $cm->name;
            } else {
                debugging('Gradable areas are currently supported at the course module level only', DEBUG_DEVELOPER);
                $title = $this->get_component();
            }

        } else {
            throw new coding_exception('Unsupported gradable area context level');
        }

        return $title;
    }

    /**
     * Returns the localized title of the currently set area
     *
     * @return string
     */
    public function get_area_title() {

        if ($this->get_context()->contextlevel == CONTEXT_SYSTEM) {
            return '';

        } else if ($this->get_context()->contextlevel >= CONTEXT_COURSE) {
            $this->ensure_isset(array('context', 'component', 'area'));
            $areas = $this->get_available_areas();
            if (array_key_exists($this->get_area(), $areas)) {
                return $areas[$this->get_area()];
            } else {
                debugging('Unknown area!');
                return '???';
            }

        } else {
            throw new coding_exception('Unsupported context level');
        }
    }

    /**
     * Loads the gradable area info from the database
     *
     * @param int $areaid
     */
    public function load($areaid) {
        global $DB;

        $this->areacache = $DB->get_record('grading_areas', array('id' => $areaid), '*', MUST_EXIST);
        $this->context = context::instance_by_id($this->areacache->contextid, MUST_EXIST);
        $this->component = $this->areacache->component;
        $this->area = $this->areacache->areaname;
    }

    /**
     * Returns the list of installed grading plugins together, optionally extended
     * with a simple direct grading.
     *
     * @param bool $includenone should the 'Simple direct grading' be included
     * @return array of the (string)name => (string)localized title of the method
     */
    public static function available_methods($includenone = true) {

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
     * Returns the list of available grading methods in the given context
     *
     * Currently this is just a static list obtained from {@link self::available_methods()}.
     * In the future, the list of available methods may be controlled per-context.
     *
     * Requires the context property to be set in advance.
     *
     * @param bool $includenone should the 'Simple direct grading' be included
     * @return array of the (string)name => (string)localized title of the method
     */
    public function get_available_methods($includenone = true) {
        $this->ensure_isset(array('context'));
        return self::available_methods($includenone);
    }

    /**
     * Returns the list of gradable areas provided by the given component
     *
     * This performs a callback to the library of the relevant plugin to obtain
     * the list of supported areas.
     *
     * @param string $component normalized component name
     * @return array of (string)areacode => (string)localized title of the area
     */
    public static function available_areas($component) {
        global $CFG;

        list($plugintype, $pluginname) = normalize_component($component);

        if ($component === 'core_grading') {
            return array();

        } else if ($plugintype === 'mod') {
            return plugin_callback('mod', $pluginname, 'grading', 'areas_list', null, array());

        } else {
            throw new coding_exception('Unsupported area location');
        }
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

        if ($this->get_context()->contextlevel == CONTEXT_SYSTEM) {
            if ($this->get_component() !== 'core_grading') {
                throw new coding_exception('Unsupported component at the system context');
            } else {
                return array();
            }

        } else if ($this->get_context()->contextlevel == CONTEXT_MODULE) {
            list($context, $course, $cm) = get_context_info_array($this->get_context()->id);
            return self::available_areas('mod_'.$cm->modname);

        } else {
            throw new coding_exception('Unsupported gradable area context level');
        }
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
     * Extends the module navigation with the advanced grading information
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING.
     *
     * @param global_navigation $navigation
     * @param navigation_node $modulenode
     */
    public function extend_navigation(global_navigation $navigation, navigation_node $modulenode=null) {
        $this->ensure_isset(array('context', 'component'));

        $areas = $this->get_available_areas();
        foreach ($areas as $areaname => $areatitle) {
            $this->set_area($areaname);
            if ($controller = $this->get_active_controller()) {
                $controller->extend_navigation($navigation, $modulenode);
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
        global $CFG, $DB;

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

    /**
     * Creates a new shared area to hold a grading form template
     *
     * Shared area are implemented as virtual gradable areas at the system level context
     * with the component set to core_grading and unique random area name.
     *
     * @param string $method the name of the plugin we create the area for
     * @return int the new area id
     */
    public function create_shared_area($method) {
        global $DB;

        // generate some unique random name for the new area
        $name = $method . '_' . sha1(rand().uniqid($method, true));
        // create new area record
        $area = array(
            'contextid'     => context_system::instance()->id,
            'component'     => 'core_grading',
            'areaname'      => $name,
            'activemethod'  => $method);
        return $DB->insert_record('grading_areas', $area);
    }

    /**
     * Removes all data associated with the given context
     *
     * This is called by {@link context::delete_content()}
     *
     * @param int $contextid context id
     */
    public static function delete_all_for_context($contextid) {
        global $DB;

        $areaids = $DB->get_fieldset_select('grading_areas', 'id', 'contextid = ?', array($contextid));
        $methods = array_keys(self::available_methods(false));

        foreach($areaids as $areaid) {
            $manager = get_grading_manager($areaid);
            foreach ($methods as $method) {
                $controller = $manager->get_controller($method);
                $controller->delete_definition();
            }
        }

        $DB->delete_records_list('grading_areas', 'id', $areaids);
    }

    /**
     * Helper method to tokenize the given string
     *
     * Splits the given string into smaller strings. This is a helper method for
     * full text searching in grading forms. If the given string is surrounded with
     * double quotes, the resulting array consists of a single item containing the
     * quoted content.
     *
     * Otherwise, string like 'grammar, english language' would be tokenized into
     * the three tokens 'grammar', 'english', 'language'.
     *
     * One-letter tokens like are dropped in non-phrase mode. Repeated tokens are
     * returned just once.
     *
     * @param string $needle
     * @return array
     */
    public static function tokenize($needle) {

        // check if we are searching for the exact phrase
        if (preg_match('/^[\s]*"[\s]*(.*?)[\s]*"[\s]*$/', $needle, $matches)) {
            $token = $matches[1];
            if ($token === '') {
                return array();
            } else {
                return array($token);
            }
        }

        // split the needle into smaller parts separated by non-word characters
        $tokens = preg_split("/\W/u", $needle);
        // keep just non-empty parts
        $tokens = array_filter($tokens);
        // distinct
        $tokens = array_unique($tokens);
        // drop one-letter tokens
        foreach ($tokens as $ix => $token) {
            if (strlen($token) == 1) {
                unset($tokens[$ix]);
            }
        }

        return array_values($tokens);
    }

    // //////////////////////////////////////////////////////////////////////////

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

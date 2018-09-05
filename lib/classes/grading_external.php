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
 * External grading API
 *
 * @package    core_grading
 * @since      Moodle 2.5
 * @copyright  2013 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/grade/grading/lib.php");

/**
 * core grading functions
 */
class core_grading_external extends external_api {

    /**
     * Describes the parameters for get_definitions
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_definitions_parameters() {
        return new external_function_parameters(
            array(
                'cmids' => new external_multiple_structure(
                        new external_value(PARAM_INT, 'course module id'), '1 or more course module ids'),
                'areaname' => new external_value(PARAM_AREA, 'area name'),
                'activeonly' => new external_value(PARAM_BOOL, 'Only the active method', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns the definitions for the requested course module ids
     * @param array of ints $cmids
     * @param string $areaname
     * @param boolean $activeonly default is false, if true, only the active method is returned
     * @return array of areas with definitions for each requested course module id
     * @since Moodle 2.5
     */
    public static function get_definitions($cmids, $areaname, $activeonly = false) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/grade/grading/form/lib.php");
        $params = self::validate_parameters(self::get_definitions_parameters(),
            array('cmids' => $cmids,
                'areaname' => $areaname,
                'activeonly' => $activeonly));
        $warnings = array();
        $areas = array();
        foreach ($params['cmids'] as $cmid) {
            $context = context_module::instance($cmid);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $warnings[] = array(
                    'item' => 'module',
                    'itemid' => $cmid,
                    'message' => 'No access rights in module context',
                    'warningcode' => '1'
                );
                continue;
            }
            // Check if the user has managegradingforms capability.
            $isgradingmethodmanager = false;
            if (has_capability('moodle/grade:managegradingforms', $context)) {
                $isgradingmethodmanager = true;
            }
            $module = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
            $componentname = "mod_".$module->modname;

            // Get the grading manager.
            $gradingmanager = get_grading_manager($context, $componentname, $params['areaname']);
            // Get the controller for each grading method.
            $methods = array();
            if ($params['activeonly'] == true) {
                $methods[] = $gradingmanager->get_active_method();
            } else {
                $methods = array_keys($gradingmanager->get_available_methods(false));
            }

            $area = array();
            $area['cmid'] = $cmid;
            $area['contextid'] = $context->id;
            $area['component'] = $componentname;
            $area['areaname'] = $params['areaname'];
            $area['activemethod'] = $gradingmanager->get_active_method();
            $area['definitions'] = array();

            foreach ($methods as $method) {
                $controller = $gradingmanager->get_controller($method);
                $def = $controller->get_definition(true);
                if ($def == false) {
                    continue;
                }
                if ($isgradingmethodmanager == false) {
                    $isviewable = true;
                    if ($def->status != gradingform_controller::DEFINITION_STATUS_READY) {
                        $warnings[] = array(
                            'item' => 'module',
                            'itemid' => $cmid,
                            'message' => 'Capability moodle/grade:managegradingforms required to view draft definitions',
                            'warningcode' => '1'
                        );
                        $isviewable = false;
                    }
                    if (!empty($def->options)) {
                        $options = json_decode($def->options);
                        if (isset($options->alwaysshowdefinition) &&
                            $options->alwaysshowdefinition == 0) {
                            $warnings[] = array(
                                'item' => 'module',
                                'itemid' => $cmid,
                                'message' => 'Capability moodle/grade:managegradingforms required to preview definition',
                                'warningcode' => '1'
                            );
                            $isviewable = false;
                        }
                    }
                    if ($isviewable == false) {
                        continue;
                    }
                }
                $definition = array();
                $definition['id'] = $def->id;
                $definition['method'] = $method;
                $definition['name'] = $def->name;
                $definition['description'] = $def->description;
                $definition['descriptionformat'] = $def->descriptionformat;
                $definition['status'] = $def->status;
                $definition['copiedfromid'] = $def->copiedfromid;
                $definition['timecreated'] = $def->timecreated;
                $definition['usercreated'] = $def->usercreated;
                $definition['timemodified'] = $def->timemodified;
                $definition['usermodified'] = $def->usermodified;
                $definition['timecopied'] = $def->timecopied;
                // Format the description text field.
                $formattedtext = external_format_text($definition['description'],
                    $definition['descriptionformat'],
                    $context->id,
                    $componentname,
                    'description',
                    $def->id);
                $definition['description'] = $formattedtext[0];
                $definition['descriptionformat'] = $formattedtext[1];

                $details = $controller->get_external_definition_details();
                $items = array();
                foreach ($details as $key => $value) {
                    $items[$key] = self::format_text($def->{$key}, $context->id, $componentname, $def->id);
                }
                $definition[$method] = $items;
                $area['definitions'][] = $definition;
            }
            $areas[] = $area;
        }
        $result = array(
            'areas' => $areas,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Recursively processes all elements in an array and runs external_format_text()on
     * all elements which have a text field and associated format field with a key name
     * that ends with the text 'format'. The modified array is returned.
     * @param array $items the array to be processed
     * @param int $contextid
     * @param string $componentname
     * @param int $itemid
     * @see external_format_text in lib/externallib.php
     * @return array the input array with all fields formatted
     */
    private static function format_text($items, $contextid, $componentname, $itemid) {
        $formatkeys = array();
        foreach ($items as $key => $value) {
            if (!is_array($value) && substr_compare($key, 'format', -6, 6) === 0) {
                $formatkeys[] = $key;
            }
        }
        foreach ($formatkeys as $formatkey) {
            $descriptionkey = substr($formatkey, 0, -6);
            $formattedtext = external_format_text($items[$descriptionkey],
                $items[$formatkey],
                $contextid,
                $componentname,
                'description',
                $itemid);
            $items[$descriptionkey] = $formattedtext[0];
            $items[$formatkey] = $formattedtext[1];
        }
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $items[$key] = self::format_text($value, $contextid, $componentname, $itemid);
            }
        }
        return $items;
    }

    /**
     * Creates a grading area
     * @return external_single_structure
     * @since  Moodle 2.5
     */
    private static function grading_area() {
        return new external_single_structure(
            array (
                'cmid'    => new external_value(PARAM_INT, 'course module id'),
                'contextid'  => new external_value(PARAM_INT, 'context id'),
                'component' => new external_value(PARAM_TEXT, 'component name'),
                'areaname' => new external_value(PARAM_TEXT, 'area name'),
                'activemethod' => new external_value(PARAM_TEXT, 'active method', VALUE_OPTIONAL),
                'definitions'  => new external_multiple_structure(self::definition(), 'definitions')
            )
        );
    }

    /**
     * creates a grading form definition
     * @return external_single_structure
     * @since  Moodle 2.5
     */
    private static function definition() {
        global $CFG;
        $definition = array();
        $definition['id']                = new external_value(PARAM_INT, 'definition id', VALUE_OPTIONAL);
        $definition['method']            = new external_value(PARAM_TEXT, 'method');
        $definition['name']              = new external_value(PARAM_TEXT, 'name');
        $definition['description']       = new external_value(PARAM_RAW, 'description', VALUE_OPTIONAL);
        $definition['descriptionformat'] = new external_format_value('description', VALUE_OPTIONAL);
        $definition['status']            = new external_value(PARAM_INT, 'status');
        $definition['copiedfromid']      = new external_value(PARAM_INT, 'copied from id', VALUE_OPTIONAL);
        $definition['timecreated']       = new external_value(PARAM_INT, 'creation time');
        $definition['usercreated']       = new external_value(PARAM_INT, 'user who created definition');
        $definition['timemodified']      = new external_value(PARAM_INT, 'last modified time');
        $definition['usermodified']      = new external_value(PARAM_INT, 'user who modified definition');
        $definition['timecopied']        = new external_value(PARAM_INT, 'time copied', VALUE_OPTIONAL);
        foreach (self::get_grading_methods() as $method) {
            require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
            $details  = call_user_func('gradingform_'.$method.'_controller::get_external_definition_details');
            if ($details != null) {
                $items = array();
                foreach ($details as $key => $value) {
                    $details[$key]->required = VALUE_OPTIONAL;
                    $items[$key] = $value;
                }
                $definition[$method] = new external_single_structure($items, 'items', VALUE_OPTIONAL);
            }
        }
        return new external_single_structure($definition);
    }

    /**
     * Describes the get_definitions return value
     * @return external_single_structure
     * @since Moodle 2.5
     */
    public static function get_definitions_returns() {
        return new external_single_structure(
            array(
                'areas' => new external_multiple_structure(self::grading_area(), 'list of grading areas'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * @return array of available grading methods
     * @since Moodle 2.5
     */
    private static function get_grading_methods() {
        $methods = array_keys(grading_manager::available_methods(false));
        return $methods;
    }

    /**
     * Describes the parameters for get_gradingform_instances
     *
     * @return external_function_parameters
     * @since Moodle 2.6
     */
    public static function get_gradingform_instances_parameters() {
        return new external_function_parameters(
            array(
                'definitionid' => new external_value(PARAM_INT, 'definition id'),
                'since' => new external_value(PARAM_INT, 'submitted since', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns the instances and fillings for the requested definition id
     *
     * @param int $definitionid
     * @param int $since only return instances with timemodified >= since
     * @return array of grading instances with fillings for the definition id
     * @since Moodle 2.6
     */
    public static function get_gradingform_instances($definitionid, $since = 0) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/grade/grading/form/lib.php");
        $params = self::validate_parameters(self::get_gradingform_instances_parameters(),
            array('definitionid' => $definitionid,
                'since' => $since));
        $instances = array();
        $warnings = array();

        $definition = $DB->get_record('grading_definitions',
            array('id' => $params['definitionid']),
            'areaid,method', MUST_EXIST);
        $area = $DB->get_record('grading_areas',
            array('id' => $definition->areaid),
            'contextid,component', MUST_EXIST);

        $context = context::instance_by_id($area->contextid);
        require_capability('moodle/grade:managegradingforms', $context);

        $gradingmanager = get_grading_manager($definition->areaid);
        $controller = $gradingmanager->get_controller($definition->method);
        $activeinstances = $controller->get_all_active_instances ($params['since']);
        $details = $controller->get_external_instance_filling_details();
        if ($details == null) {
            $warnings[] = array(
                'item' => 'definition',
                'itemid' => $params['definitionid'],
                'message' => 'Fillings unavailable because get_external_instance_filling_details is not defined',
                'warningcode' => '1'
            );
        }
        $getfilling = null;
        if (method_exists('gradingform_'.$definition->method.'_instance', 'get_'.$definition->method.'_filling')) {
            $getfilling = 'get_'.$definition->method.'_filling';
        } else {
            $warnings[] = array(
                'item' => 'definition',
                'itemid' => $params['definitionid'],
                'message' => 'Fillings unavailable because get_'.$definition->method.'_filling is not defined',
                'warningcode' => '1'
            );
        }
        foreach ($activeinstances as $activeinstance) {
            $instance = array();
            $instance['id'] = $activeinstance->get_id();
            $instance['raterid'] = $activeinstance->get_data('raterid');
            $instance['itemid'] = $activeinstance->get_data('itemid');
            $instance['rawgrade'] = $activeinstance->get_data('rawgrade');
            $instance['status'] = $activeinstance->get_data('status');
            $instance['feedback'] = $activeinstance->get_data('feedback');
            $instance['feedbackformat'] = $activeinstance->get_data('feedbackformat');
            // Format the feedback text field.
            $formattedtext = external_format_text($activeinstance->get_data('feedback'),
                $activeinstance->get_data('feedbackformat'),
                $context->id,
                $area->component,
                'feedback',
                $params['definitionid']);
            $instance['feedback'] = $formattedtext[0];
            $instance['feedbackformat'] = $formattedtext[1];
            $instance['timemodified'] = $activeinstance->get_data('timemodified');

            if ($details != null && $getfilling != null) {
                $fillingdata = $activeinstance->$getfilling();
                $filling = array();
                foreach ($details as $key => $value) {
                    $filling[$key] = self::format_text($fillingdata[$key],
                        $context->id,
                        $area->component,
                        $params['definitionid']);
                }
                $instance[$definition->method] = $filling;
            }
            $instances[] = $instance;
        }
        $result = array(
            'instances' => $instances,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Creates a grading instance
     *
     * @return external_single_structure
     * @since  Moodle 2.6
     */
    private static function grading_instance() {
        global $CFG;
        $instance = array();
        $instance['id']                = new external_value(PARAM_INT, 'instance id');
        $instance['raterid']           = new external_value(PARAM_INT, 'rater id');
        $instance['itemid']            = new external_value(PARAM_INT, 'item id');
        $instance['rawgrade']          = new external_value(PARAM_TEXT, 'raw grade', VALUE_OPTIONAL);
        $instance['status']            = new external_value(PARAM_INT, 'status');
        $instance['feedback']          = new external_value(PARAM_RAW, 'feedback', VALUE_OPTIONAL);
        $instance['feedbackformat']    = new external_format_value('feedback', VALUE_OPTIONAL);
        $instance['timemodified']      = new external_value(PARAM_INT, 'modified time');
        foreach (self::get_grading_methods() as $method) {
            require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
            $details  = call_user_func('gradingform_'.$method.'_controller::get_external_instance_filling_details');
            if ($details != null) {
                $items = array();
                foreach ($details as $key => $value) {
                    $details[$key]->required = VALUE_OPTIONAL;
                    $items[$key] = $value;
                }
                $instance[$method] = new external_single_structure($items, 'items', VALUE_OPTIONAL);
            }
        }
        return new external_single_structure($instance);
    }

    /**
     * Describes the get_gradingform_instances return value
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function get_gradingform_instances_returns() {
        return new external_single_structure(
            array(
                'instances' => new external_multiple_structure(self::grading_instance(), 'list of grading instances'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for save_definitions
     *
     * @return external_function_parameters
     * @since Moodle 2.8
     */
    public static function save_definitions_parameters() {
        return new external_function_parameters(
            array(
                'areas' => new external_multiple_structure(self::grading_area(), 'areas with definitions to save')
            )
        );
    }

    /**
     * Saves the areas and definitions
     * @param array $areas array of areas containing definitions to be saved
     * @return null
     * @throws invalid_parameter_exception
     * @since Moodle 2.8
     */
    public static function save_definitions($areas) {
        $params = self::validate_parameters(self::save_definitions_parameters(),
                                            array('areas' => $areas));

        foreach ($params['areas'] as $area) {

            $context = context::instance_by_id($area['contextid']);
            require_capability('moodle/grade:managegradingforms', $context);
            $gradingmanager = get_grading_manager($context, $area['component'], $area['areaname']);
            $gradingmanager->set_active_method($area['activemethod']);
            $availablemethods = $gradingmanager->get_available_methods();

            foreach ($area['definitions'] as $definition) {
                if (array_key_exists($definition['method'], $availablemethods)) {
                    $controller = $gradingmanager->get_controller($definition['method']);
                    $controller->update_definition(self::create_definition_object($definition));
                } else {
                    throw new invalid_parameter_exception('Unknown Grading method: '. $definition['method']);
                }
            }
        }
    }

    /**
     * Describes the return value for save_definitions
     *
     * @return external_single_structure
     * @since Moodle 2.8
     */
    public static function save_definitions_returns() {
        return null;
    }

    /**
     * Creates a definition stdClass object using the values from the definition
     * array that is passed in as a parameter
     *
     * @param array $definition
     * @return stdClass definition object
     * @since Moodle 2.8
     */
    private static function create_definition_object($definition) {
        global $CFG;

        $method = $definition['method'];
        $definitionobject = new stdClass();
        foreach ($definition as $key => $value) {
            if (!is_array($value)) {
                $definitionobject->$key = $value;
            }
        }
        $text = '';
        $format = FORMAT_MOODLE;
        if (isset($definition['description'])) {
            $text = $definition['description'];
            if (isset($definition['descriptionformat'])) {
                $format = $definition['descriptionformat'];
            }
        }
        $definitionobject->description_editor = array('text' => $text, 'format' => $format);

        require_once("$CFG->libdir/filelib.php");
        require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
        $details  = call_user_func('gradingform_'.$method.'_controller::get_external_definition_details');
        $methodarray = array();
        foreach (array_keys($details) as $definitionkey) {
            $items = array();
            $idnumber = 1;
            foreach ($definition[$method][$definitionkey] as $item) {
                $processeditem = self::set_new_ids($item, $idnumber);
                $items[$processeditem['id']] = $processeditem;
                $idnumber++;
            }
            $definitionobjectkey = substr($definitionkey, strlen($method.'_'));
            $methodarray[$definitionobjectkey] = $items;
            $definitionobject->$method = $methodarray;
        }

        return $definitionobject;
    }

    /**
     * Recursively iterates through arrays. Any array without an id key-value combination
     * is assumed to be an array of values to be inserted and an id key-value is added with
     * the value matching the regex '/^NEWID\d+$/' that is expected by each grading form implementation.
     *
     * @param array $arraytoset the array to be processed
     * @param int $startnumber the starting number for the new id numbers
     * @return array with missing id keys added for all arrays
     * @since Moodle 2.8
     */
    private static function set_new_ids($arraytoset, $startnumber) {
        $result = array();
        $foundid = false;
        $number = $startnumber;
        foreach ($arraytoset as $key1 => $value1) {
            if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) {
                    $processedvalue = self::set_new_ids($value2, $number);
                    $result[$key1][$processedvalue['id']] = $processedvalue;
                    $number++;
                }
            } else {
                $result[$key1] = $value1;
            }
            if ($key1 === 'id') {
                $foundid = true;
            }
        }
        if (!$foundid) {
            $result['id'] = 'NEWID'.$number;
        }
        return $result;
    }

}

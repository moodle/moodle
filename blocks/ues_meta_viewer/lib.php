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
 * @package    block_ues_meta_viewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/ues_meta_viewer/classes/lib.php');
require_once($CFG->dirroot . '/blocks/cps/classes/ues_meta_viewer_handler.php');

abstract class ues_meta_viewer {

    /**
     * Helper structure for eliminating events_trigger_legacy()
     * @var array map from types to an array of event handlers having the form:
     * array(include_file => listener_classname, ...).
     */
    private static $handlermap = array('ues_user' => array('/blocks/cps/classes/ues_meta_viewer_handler.php' => 'blocks_cps_ues_meta_viewer_handler')
                                     , 'sports_grade' => array('/blocks/student_gradeviewer/events/lib.php' => 'student_gradeviewer_handlers'),
                                      );

    /**
     * Helper function for eliminating events_trigger_legacy().
     * All input params go towards building the following function
     * invocation:
     *
     * $class::{$type . '_data_ui_'.$fn}($params);
     * @global stdClass $CFG
     * @param string $type
     * @param objcet $params
     * @param string $fn
     */
    private static function mock_event($type, $params, $fn) {
        global $CFG;
        if (array_key_exists($type, self::$handlermap)) {
            foreach (self::$handlermap[$type] as $file => $class) {
                if (file_exists($CFG->dirroot.$file)) {
                    require_once($CFG->dirroot.$file);
                    $class::{$type . '_data_ui_'.$fn} ($params);
                }
            }
        }
    }

    public static function sql($handlers) {
        $flatten = function($dsl, $handler) {
            return $handler->sql($dsl);
        };

        try {
            $filters = array_reduce($handlers, $flatten, ues::where());

            // Catch empty.
            $filters->get();
            return $filters;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function result_table($users, $handlers) {
        $table = new html_table();
        $table->head = array();
        $table->data = array();

        foreach ($handlers as $handler) {
            $table->head[] = $handler->name();
        }

        foreach ($users as $id => $user) {
            $format = function($handler) use ($user) {
                return $handler->format($user);
            };
            $table->data[] = array_map($format, $handlers);
        }
        return $table;
    }

    public static function handler($type, $field) {
        $cur = current_language();
        $strs = get_string_manager()->load_component_strings('moodle', $cur);

        if (!isset($strs[$field])) {
            $name = $field;
        } else {
            $name = $strs[$field];
        }

        $handler = new stdClass;
        $handler->ui_element = new meta_data_text_box($field, $name);
        self::mock_event($type, $handler, 'element');
        return $handler->ui_element;
    }

    public static function generate_keys($type, $class, $user) {
        $types = self::supported_types();
        $fields = new stdClass;
        $fields->user = $user;
        $fields->keys = $types[$type]->defaults();

        // Auto fill based on system.
        $additionalfields = $class::get_meta_names();
        foreach ($additionalfields as $field) {
            $fields->keys[] = $field;
        }

        // Should this user see appropriate fields?
        self::mock_event($type, $fields, 'keys');
        return $fields->keys;
    }

    public static function supported_types() {
        if (!class_exists('supported_meta')) {
            global $CFG;
            require_once($CFG->dirroot . '/blocks/ues_meta_viewer/classes/support.php');
        }

        $supportedtypes = new stdClass;
        $supportedtypes->types = array(
            'ues_user' => new ues_user_supported_meta(),
            'ues_section' => new ues_section_supported_meta(),
            'ues_course' => new ues_course_supported_meta(),
            'ues_semester' => new ues_semester_supported_meta(),
            'ues_teacher' => new ues_teacher_supported_meta(),
            'ues_student' => new ues_student_supported_meta()
        );

        require_once($CFG->dirroot.'/blocks/student_gradeviewer/events/lib.php');
        $supportedtypes = student_gradeviewer_handlers::ues_meta_supported_types($supportedtypes);
        return $supportedtypes->types;
    }
}

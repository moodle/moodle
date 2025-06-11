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

namespace theme_snap\webservice;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_single_structure;

/**
 * Course tools block actions webservice.
 * @author    Daniel Cifuentes
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_coursetools_block_actions extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'params' => new external_single_structure([
                'action' => new external_value(PARAM_TEXT, 'Block action.', VALUE_REQUIRED),
                'id' => new external_value(PARAM_INT, 'Block ID.', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
            ], ),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'success' => new external_value(PARAM_BOOL, 'The block action was successfully executed.', VALUE_REQUIRED),
            'error' => new external_value(PARAM_TEXT, 'error', VALUE_OPTIONAL)
        ];

        return new external_single_structure($keys, 'blockactions');
    }

    /**
     * @param string $action
     * @param int $id
     * @return array
     */
    public static function service($params) {
        global $PAGE, $DB;

        $params = self::validate_parameters(self::service_parameters(), ['params' => $params]);

        try {
            $course = $DB->get_record('course', ['id' => $params['params']['courseid']]);
            if (!$course) {
                throw new \moodle_exception('invalidcourseid');
            }
            $context = \context_course::instance($course->id);
            // Load the block instances for all the regions.
            if (!$PAGE->has_set_url()) {
                $PAGE->set_url(new \moodle_url('/course/view.php', array('id' => $params['params']['courseid'])));
                $PAGE->set_context($context);
                $PAGE->set_course($course);
                $PAGE->set_pagelayout('course');
                $PAGE->set_pagetype('course-view-'.$course->format);
            }
            $PAGE->blocks->load_blocks();
            $PAGE->blocks->create_all_block_instances();
            $block = $PAGE->blocks->find_instance($params['params']['id']);

            if (!$PAGE->user_can_edit_blocks() ||
                !$block->user_can_edit() ||
                !$block->user_can_addto($PAGE)) {
                throw new \moodle_exception('nopermissions');
            }

            if ($params['params']['action'] == "bui_hideid") {
                $newvisibility = 0;
                blocks_set_visibility($block->instance, $PAGE, $newvisibility);
            } elseif ($params['params']['action'] == "bui_showid") {
                $newvisibility = 1;
                blocks_set_visibility($block->instance, $PAGE, $newvisibility);
            } elseif ($params['params']['action'] == "bui_deleteid") {
                if (in_array($block->instance->blockname, \block_manager::get_undeletable_block_types()) ||
                    in_array($block->instance->blockname, $PAGE->blocks->get_required_by_theme_block_types())) {
                    throw new \moodle_exception('nopermissions');
                }
                blocks_delete_instance($block->instance);
                // bui_deleteid and bui_confirm should not be in the PAGE url.
                $PAGE->ensure_param_not_in_url('bui_deleteid');
                $PAGE->ensure_param_not_in_url('bui_confirm');
            } else {
                throw new \moodle_exception('invalidaction');
            }
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e];
        }
    }
}

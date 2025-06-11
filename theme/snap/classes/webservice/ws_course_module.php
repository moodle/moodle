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
 * Course module web service.
 *
 * This web service returns the HTML for a specified single course module, given its cmid.
 *
 * @author    Julian Tovar
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_course_module extends external_api {

    public static function service_parameters() {
        $parameters = [
            'cmid' => new external_value(PARAM_INT, 'Course module ID', VALUE_REQUIRED),
        ];
        return new external_function_parameters($parameters);
    }

    public static function service_returns() {
        $keys = [
            'html' => new external_value(PARAM_RAW, 'Course module HTML')
        ];
        return new external_single_structure($keys, 'course_module HTML return value');
    }

    public static function service($cmid) {
        global $COURSE, $PAGE;

        self::validate_parameters(self::service_parameters(), ['cmid' => $cmid]);

        $context = \context_course::instance($COURSE->id);
        $PAGE->set_context($context);

        $courserenderer = $PAGE->get_renderer('core', 'course');
        $completioninfo = new \completion_info($COURSE);
        $format = \core_courseformat\base::instance($COURSE);
        $modinfo = $format->get_modinfo();
        $mod = $modinfo->cms[$cmid];

        $returnhtml = $courserenderer->course_section_cm_list_item_snap($COURSE, $completioninfo, $mod, null);
        $result = ['html' => $returnhtml];
        return $result;
    }
}

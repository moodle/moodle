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
 * Get content for single rich content item.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

defined('MOODLE_INTERNAL') || die();

use tool_ally\local_content;

require_once(__DIR__.'/../../../../../course/lib.php');


/**
 * Replace content for single rich content item.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class replace_content extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_INT, 'Item id'),
            'component' => new \external_value(PARAM_ALPHANUMEXT, 'Component'),
            'table' => new \external_value(PARAM_ALPHANUMEXT, 'Table'),
            'field' => new \external_value(PARAM_ALPHANUMEXT, 'Field'),
            'content' => new \external_value(PARAM_RAW, 'HTML Content')
        ]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'success'    => new \external_value(PARAM_BOOL, 'Content replaced succesfully?')
        ]);
    }

    /**
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     * @param string $content
     * @return array
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public static function execute_service($id, $component, $table, $field, $content) {
        $params = self::validate_parameters(self::service_parameters(), [
            'id' => $id,
            'component' => $component,
            'table' => $table,
            'field' => $field,
            'content' => $content
        ]);

        self::validate_context(\context_system::instance());
        require_capability('moodle/course:view', \context_system::instance());
        require_capability('moodle/course:viewhiddencourses', \context_system::instance());

        $success = local_content::replace_html_content(
                $params['id'], $params['component'], $params['table'], $params['field'], $params['content']);

        return (['success' => $success]);
    }
}

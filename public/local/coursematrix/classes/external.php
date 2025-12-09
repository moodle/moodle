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
 * External functions for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/local/coursematrix/lib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_warnings;

/**
 * External service class.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_rules_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Get all course matrix rules
     * @return array
     */
    public static function get_rules() {
        // Validate context (system context is appropriate for admin settings).
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $rules = local_coursematrix_get_rules();
        $result = [];

        foreach ($rules as $rule) {
            $result[] = [
                'id' => $rule->id,
                'department' => $rule->department,
                'jobtitle' => $rule->jobtitle,
                'courses' => $rule->courses,
            ];
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_multiple_structure
     */
    public static function get_rules_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Rule ID'),
                'department' => new external_value(PARAM_TEXT, 'Department'),
                'jobtitle' => new external_value(PARAM_TEXT, 'Job Title'),
                'courses' => new external_value(PARAM_RAW, 'Comma separated course IDs'),
            ])
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_rule_parameters() {
        return new external_function_parameters([
            'rules' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Rule ID (optional, 0 for new)', VALUE_DEFAULT, 0),
                    'department' => new external_value(PARAM_TEXT, 'Department'),
                    'jobtitle' => new external_value(PARAM_TEXT, 'Job Title'),
                    'courses' => new external_value(PARAM_RAW, 'Comma separated course IDs'),
                ])
            ),
        ]);
    }

    /**
     * Update or create course matrix rules
     * @param array $rules
     * @return array
     */
    public static function update_rule($rules) {
        global $DB;

        $params = self::validate_parameters(self::update_rule_parameters(), ['rules' => $rules]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $warnings = [];
        $ids = [];

        foreach ($params['rules'] as $rule) {
            $data = new \stdClass();
            if (!empty($rule['id'])) {
                $data->id = $rule['id'];
            }
            $data->department = $rule['department'];
            $data->jobtitle = $rule['jobtitle'];
            $data->courses = $rule['courses'];

            try {
                // Check if we are creating a new rule but one already exists for this combo.
                if (empty($data->id)) {
                    $existing = $DB->get_record(
                        'local_coursematrix',
                        ['department' => $data->department, 'jobtitle' => $data->jobtitle]
                    );
                    if ($existing) {
                        $data->id = $existing->id;
                    }
                }

                $id = local_coursematrix_save_rule($data);
                $ids[] = $id;
            } catch (\Exception $e) {
                $warnings[] = [
                    'item' => $rule['department'] . ' - ' . $rule['jobtitle'],
                    'warningcode' => 'savefailed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'ids' => $ids,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function update_rule_returns() {
        return new external_single_structure([
            'ids' => new external_multiple_structure(new external_value(PARAM_INT, 'Updated/Created Rule ID')),
            'warnings' => new external_warnings(),
        ]);
    }
}

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

namespace core\check\external;

use admin_root;
use admin_setting_check;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use context_system;
use invalid_parameter_exception;

/**
 * Webservice to get result of a given check.
 *
 * @package    core
 * @category   check
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_result_admintree extends external_api {
    /**
     * Defines parameters
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'admintreeid' => new external_value(PARAM_TEXT, 'ID of node in admintree'),
            'settingname' => new external_value(PARAM_TEXT, 'Name of setting'),
            'includedetails' => new external_value(PARAM_BOOL, 'If the details should be included in the response.
                Depending on the check, details could be slower to return.', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Gets the result of the check and returns it.
     * @param string $admintreeid ID of admin_setting to find check object from
     * @param string $settingname Name of admin_setting to find check object from
     * @param bool $includedetails If the details should be included in the response.
     * @param admin_root|null $admintree Root of admin tree to use (for unit testing)
     * @return array returned data
     */
    public static function execute(string $admintreeid, string $settingname, bool $includedetails,
        ?admin_root $admintree = null): array {
        global $OUTPUT, $CFG;

        // Validate parameters.
        self::validate_parameters(self::execute_parameters(), [
            'admintreeid' => $admintreeid,
            'settingname' => $settingname,
            'includedetails' => $includedetails,
        ]);

        // Context and capability checks.
        $context = context_system::instance();
        self::validate_context($context);
        require_admin();

        require_once($CFG->libdir . '/adminlib.php');

        // Find admin node so we can load the check object.
        $check = self::get_check_from_setting($admintreeid, $settingname, $admintree);

        if (empty($check)) {
            throw new invalid_parameter_exception("Could not find check object using admin tree.");
        }

        // Execute the check and get the result.
        $result = $check->get_result();

        // Build the response.
        $data = [
            'status' => s($result->get_status()),
            'summary' => s($result->get_summary()),
            'html' => s($OUTPUT->check_full_result($check, $result, $includedetails)),
        ];

        // Since details might be slower to obtain, we allow this to be optionally returned.
        if ($includedetails) {
            $data['details'] = s($result->get_details());
        }

        return $data;
    }

    /**
     * Finds the check from the admin tree.
     *
     * @param string $settingid ID of the adming_setting
     * @param string $settingname Name of the admin_setting
     * @param admin_root|null $tree Admin tree to use (for unit testing). Null will default to the admin_get_root()
     */
    private static function get_check_from_setting(string $settingid, string $settingname, ?admin_root $tree = null) {
        // Since settings do not know exactly who their parents are in the tree, we must search for the setting.
        if (empty($tree)) {
            $tree = \admin_get_root();
        }

        // Search for the setting name.
        // To do this, we must search in each category.
        $categories = $tree->search($settingname);

        $allsettings = array_map(function($c) {
            return $c->settings;
        }, array_values($categories));

        // Flatten the array.
        $allsettings = array_merge(...$allsettings);

        // Find the one that matches the unique id exactly and are check settings.
        $matchingsettings = array_filter($allsettings, function($s) use ($settingid) {
            return $s->get_id() == $settingid && $s instanceof admin_setting_check;
        });

        // There was either none found or more than one found.
        // In this case, we cannot determine which to use so just return null.
        if (count($matchingsettings) != 1) {
            return null;
        }

        $setting = current($matchingsettings);
        return $setting->get_check();
    }

    /**
     * Defines return structure.
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Result status constant'),
            'summary' => new external_value(PARAM_TEXT, 'Summary of result'),
            'html' => new external_value(PARAM_TEXT, 'Rendered full html result', VALUE_OPTIONAL),
            'details' => new external_value(PARAM_TEXT, 'Details of result (if includedetails was enabled)', VALUE_OPTIONAL),
        ]);
    }
}


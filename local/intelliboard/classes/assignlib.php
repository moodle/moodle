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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_intelliboard_assign extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function save_assigns_parameters() {
        return new external_function_parameters(
            array(
                'assigns' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'External user ID'),
                            'type' => new external_value(PARAM_ALPHA, 'Instance type'),
                            'instance' => new external_value(PARAM_TEXT, 'Local category/course/cohort/profile_field value')
                        )
                    )
                )
            )
        );
    }

    /**
     * Create one or more assigns
     *
     * @param array $assigns.
     * @return array An array of arrays
     * @since Moodle 2.5
     */
    public static function save_assigns($assigns) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::save_assigns_parameters(), array('assigns' => $assigns));

        $transaction = $DB->start_delegated_transaction();

        self::validate_context(context_system::instance());

        $dataobjects = [];

        foreach ($params['assigns'] as $assign) {
            $assign['rel'] = 'external';

            if (!$DB->record_exists('local_intelliboard_assign', $assign)) {
                $assign = (object) $assign;
                $assign->timecreated = time();
                $dataobjects[] = $assign;
            }
        }
        $DB->insert_records('local_intelliboard_assign', $dataobjects);

        $transaction->allow_commit();

        return $dataobjects;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function save_assigns_returns() {
       return new external_multiple_structure(
            new external_single_structure(
                array(
                    'userid' => new external_value(PARAM_INT, 'External user ID'),
                    'type' => new external_value(PARAM_ALPHA, 'Instance type'),
                    'instance' => new external_value(PARAM_TEXT, 'Local category/course/cohort/profile_field value')
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_assigns_parameters() {
        return new external_function_parameters(
            array(
                'assigns' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'External user ID'),
                            'type' => new external_value(PARAM_ALPHA, 'Instance type'),
                            'instance' => new external_value(PARAM_TEXT, 'Local category/course/cohort/profile_field value'),
                        )
                    )
                )
            )
        );
    }

    /**
     *
     * @param array $assigns
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_assigns($assigns) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::delete_assigns_parameters(), array('assigns' => $assigns));

        $transaction = $DB->start_delegated_transaction();

        self::validate_context(context_system::instance());

        foreach ($params['assigns'] as $assign) {
            $assign['rel'] = 'external';

            $DB->delete_records('local_intelliboard_assign', $assign);
        }

        $transaction->allow_commit();

        return null;
    }

    public static function delete_assigns_returns() {
        return null;
    }
}

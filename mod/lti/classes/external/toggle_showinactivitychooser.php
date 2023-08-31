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

namespace mod_lti\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use mod_lti\local\ltiopenid\registration_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * External function to toggle showinactivitychooser setting.
 *
 * @package    mod_lti
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toggle_showinactivitychooser extends external_api {

    /**
     * Get parameter definition.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tooltypeid' => new external_value(PARAM_INT, 'Tool type ID'),
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'coursevisible' => new external_value(PARAM_BOOL, 'Show in activity chooser'),
        ]);
    }

    /**
     * Toggles showinactivitychooser setting.
     *
     * @param int $tooltypeid the id of the course external tool type.
     * @param int $courseid the id of the course we are in.
     * @param bool $showinactivitychooser Show in activity chooser setting.
     * @return bool true
     */
    public static function execute(int $tooltypeid, int $courseid, bool $showinactivitychooser): bool {
        global $DB;

        [
            'tooltypeid' => $tooltypeid,
            'courseid' => $courseid,
            'coursevisible' => $showinactivitychooser,
        ] = self::validate_parameters(self::execute_parameters(), [
            'tooltypeid' => $tooltypeid,
            'courseid' => $courseid,
            'coursevisible' => $showinactivitychooser,
        ]);

        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('mod/lti:addcoursetool', $context);

        if ($showinactivitychooser) {
            $coursevisible = LTI_COURSEVISIBLE_ACTIVITYCHOOSER;
        } else {
            $coursevisible = LTI_COURSEVISIBLE_PRECONFIGURED;
        }
        $ltitype = $DB->get_record('lti_types', ['id' => $tooltypeid]);
        $ltitype->coursevisible = $coursevisible;

        $config = new \stdClass();
        $config->lti_coursevisible = $coursevisible;

        if (intval($ltitype->course) !== intval(get_site()->id)) {
            // It is course tool - just update it.
            lti_update_type($ltitype, $config);
        } else {
            $coursecategory = $DB->get_field('course', 'category', ['id' => $courseid]);
            $sql = "SELECT COUNT(*) AS count
                      FROM {lti_types_categories} tc
                     WHERE tc.typeid = :typeid";
            $restrictedtool = $DB->get_record_sql($sql, ['typeid' => $tooltypeid]);
            if ($restrictedtool->count) {
                $record = $DB->get_record('lti_types_categories', ['typeid' => $tooltypeid, 'categoryid' => $coursecategory]);
                if (!$record) {
                    throw new \moodle_exception('You are not allowed to change this setting for this tool.');
                }
            }

            // This is site tool, but we would like to have course level setting for it.
            $lticoursevisible = $DB->get_record('lti_coursevisible', ['typeid' => $tooltypeid, 'courseid' => $courseid]);
            if (!$lticoursevisible) {
                $lticoursevisible = new \stdClass();
                $lticoursevisible->typeid = $tooltypeid;
                $lticoursevisible->courseid = $courseid;
                $lticoursevisible->coursevisible = $coursevisible;
                $DB->insert_record('lti_coursevisible', $lticoursevisible);
            } else {
                $lticoursevisible->coursevisible = $coursevisible;
                $DB->update_record('lti_coursevisible', $lticoursevisible);
            }
        }

        return true;
    }

    /**
     * Get service returns definition.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL, 'Success');
    }
}

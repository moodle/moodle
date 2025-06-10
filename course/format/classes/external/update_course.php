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

namespace core_courseformat\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_multiple_structure;
use moodle_exception;
use coding_exception;
use context_course;
use core_courseformat\base as course_format;

/**
 * External secrvie to update the course from the course editor components.
 *
 * @package    core_course
 * @copyright  2021 Ferran Recio <moodle@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.0
 */
class update_course extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'action' => new external_value(
                    PARAM_ALPHANUMEXT,
                    'action: cm_hide, cm_show, section_hide, section_show, cm_moveleft...',
                    VALUE_REQUIRED
                ),
                'courseid' => new external_value(PARAM_INT, 'course id', VALUE_REQUIRED),
                'ids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Target id'),
                    'Affected ids',
                    VALUE_DEFAULT,
                    []
                ),
                'targetsectionid' => new external_value(
                    PARAM_INT, 'Optional target section id', VALUE_DEFAULT, null
                ),
                'targetcmid' => new external_value(
                    PARAM_INT, 'Optional target cm id', VALUE_DEFAULT, null
                ),
            ]
        );
    }

    /**
     * This webservice will execute any action from the course editor. The default actions
     * are located in {@see \core_courseformat\stateactions} but the format plugin can extend that class
     * in format_XXX\course.
     *
     * The specific action methods will register in a {@see \core_courseformat\stateupdates} all the affected
     * sections, cms and course attribute. This object (in JSON) will be sent back to the
     * frontend editor to refresh the updated state elements.
     *
     * By default, {@see \core_courseformat\stateupdates} will register only create, delete and update events
     * on cms, sections and the general course data. However, if some plugin needs adhoc messages for
     * its own mutation module, extend this class in format_XXX\course.
     *
     * @param string $action the action name to execute
     * @param int $courseid the course id
     * @param int[] $ids the affected ids (section or cm depending on the action)
     * @param int|null $targetsectionid optional target section id (for move action)
     * @param int|null $targetcmid optional target cm id (for move action)
     * @return string Course state in JSON
     */
    public static function execute(string $action, int $courseid, array $ids = [],
            ?int $targetsectionid = null, ?int $targetcmid = null): string {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'action' => $action,
            'courseid' => $courseid,
            'ids' => $ids,
            'targetsectionid' => $targetsectionid,
            'targetcmid' => $targetcmid,
        ]);
        $action = $params['action'];
        $courseid = $params['courseid'];
        $ids = $params['ids'];
        $targetsectionid = $params['targetsectionid'];
        $targetcmid = $params['targetcmid'];

        self::validate_context(context_course::instance($courseid));

        $courseformat = course_get_format($courseid);

        // Create a course changes tracker object.
        $defaultupdatesclass = 'core_courseformat\\stateupdates';
        $updatesclass = 'format_' . $courseformat->get_format() . '\\courseformat\\stateupdates';
        if (!class_exists($updatesclass)) {
            $updatesclass = $defaultupdatesclass;
        }
        $updates = new $updatesclass($courseformat);

        if (!is_a($updates, $defaultupdatesclass)) {
            throw new coding_exception("The \"$updatesclass\" class must extend \"$defaultupdatesclass\"");
        }

        // Get the actions class from the course format.
        $actionsclass = 'format_'. $courseformat->get_format().'\\courseformat\\stateactions';
        if (!class_exists($actionsclass)) {
            $actionsclass = 'core_courseformat\\stateactions';
        }
        $actions = new $actionsclass();

        if (!is_callable([$actions, $action])) {
            throw new moodle_exception("Invalid course state action $action in ".get_class($actions));
        }

        $course = $courseformat->get_course();

        // Execute the action.
        $actions->$action($updates, $course, $ids, $targetsectionid, $targetcmid);

        // Any state action mark the state cache as dirty.
        course_format::session_cache_reset($course);

        return json_encode($updates);
    }

    /**
     * Webservice returns.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_RAW, 'Encoded course update JSON');
    }
}

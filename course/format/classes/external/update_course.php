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

use core\exception\moodle_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;
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

        $format = course_get_format($courseid);

        $updates = $format->get_stateupdates_instance();
        $actions = $format->get_stateactions_instance();

        if (!is_callable([$actions, $action])) {
            throw new moodle_exception("Invalid course state action $action in ".get_class($actions));
        }

        $course = $format->get_course();

        // Execute the action.
        $actions->$action($updates, $course, $ids, $targetsectionid, $targetcmid);

        // Dispatch the hook for post course content update.
        $hook = new \core_courseformat\hook\after_course_content_updated(
            course: $course,
        );
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

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

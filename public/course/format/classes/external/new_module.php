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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use moodle_exception;
use coding_exception;
use context_course;
use core_courseformat\base as course_format;

/**
 * External service to create a new module instance in the course.
 *
 * @package    core_courseformat
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class new_module extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'course id', VALUE_REQUIRED),
                'modname' => new external_value(PARAM_ALPHANUMEXT, 'module name', VALUE_REQUIRED),
                'targetsectionid' => new external_value(PARAM_INT, 'target section id', VALUE_REQUIRED, null),
                'targetcmid' => new external_value(PARAM_INT, 'Optional target cm id', VALUE_DEFAULT, null),
            ]
        );
    }

    /**
     * This webservice will execute the create_module action from the course editor.
     *
     * The action will register in a {@see \core_courseformat\stateupdates} all the affected
     * sections, cms and course attribute. This object (in JSON) will be sent back to the
     * frontend editor to refresh the updated state elements.
     *
     * By default, {@see \core_courseformat\stateupdates} will register only create, delete and update events
     * on cms, sections and the general course data. However, if some plugin needs adhoc messages for
     * its own mutation module, extend this class in format_XXX\course.
     *
     * @param int $courseid the course id
     * @param string $modname the module name
     * @param int $targetsectionid the target section id
     * @param int|null $targetcmid optional target cm id
     * @return string Course state in JSON
     */
    public static function execute(
        int $courseid,
        string $modname,
        int $targetsectionid,
        ?int $targetcmid = null
    ): string {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        [
            'courseid' => $courseid,
            'modname' => $modname,
            'targetsectionid' => $targetsectionid,
            'targetcmid' => $targetcmid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'modname' => $modname,
            'targetsectionid' => $targetsectionid,
            'targetcmid' => $targetcmid,
        ]);

        self::validate_context(context_course::instance($courseid));

        // Plugin needs to support quick creation and the course format needs to support components.
        // Formats using YUI modules should not be able to quick-create because the front end cannot react to the change.
        if (!plugin_supports('mod', $modname, FEATURE_QUICKCREATE) || !course_get_format($courseid)->supports_components()) {
            throw new moodle_exception("Module $modname does not support quick creation");
        }

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
        /** @var \core_courseformat\stateactions $actions */
        $actions = new $actionsclass();

        $action = 'new_module';
        if (!is_callable([$actions, $action])) {
            throw new moodle_exception("Invalid course state action $action in ".get_class($actions));
        }

        $course = $courseformat->get_course();

        // Execute the action.
        $actions->$action($updates, $course, $modname, $targetsectionid, $targetcmid);

        // Dispatch the hook for post course content update.
        $hook = new \core_courseformat\hook\after_course_content_updated(
            course: $course
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

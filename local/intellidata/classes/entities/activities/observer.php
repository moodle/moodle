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
 * Observer
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\activities;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'course_module_created' event is triggered.
     *
     * @param \core\event\course_module_created $event
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        if (TrackingHelper::enabled()) {
            self::export_event($event);
        }
    }

    /**
     * Triggered when 'course_module_updated' event is triggered.
     *
     * @param \core\event\course_module_updated $event
     */
    public static function course_module_updated(\core\event\course_module_updated $event) {
        if (TrackingHelper::enabled()) {
            self::export_event($event);
        }
    }

    /**
     * Triggered when 'course_module_deleted' event is triggered.
     *
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        if (TrackingHelper::enabled()) {
            self::export_event($event, ['id']);
        }
    }

    /**
     * Export data event.
     *
     * @param $event
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($event, $fields = []) {

        $eventdata = $event->get_data();

        $cm = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
        $cm->modulename = $eventdata['other']['modulename'];

        $activitdata = activity::prepare_export_data($cm, $fields);
        $activitdata->crud = $eventdata['crud'];

        $entity = new activity($activitdata);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

    /**
     * Set additional params.
     *
     * @param $modulename
     * @param $instance
     * @return false|string
     */
    public static function set_additional_params($modulename, $instance) {

        switch ($modulename) {
            case 'forum':
                $params = [
                    'isannouncement' => ($instance->type == 'news') ? 1 : 0,
                ];
                break;
            case 'quiz':
                $params = [
                    'timeopen' => $instance->timeopen,
                    'timeclose' => $instance->timeclose + $instance->graceperiod,
                    'duedate' => $instance->timeclose,
                ];
                break;
            case 'assign':
                $params = [
                    'submissiontypes' => self::get_assignment_submissiontypes($instance),
                    'startdate' => $instance->allowsubmissionsfromdate,
                    'enddate' => $instance->cutoffdate,
                    'duedate' => ($instance->duedate) ? $instance->duedate : $instance->gradingduedate,
                ];
                break;
            case 'certificate':
                $params = [
                    'printgrade' => $instance->printgrade,
                ];
                break;
            case 'questionnaire':
                $params = [
                    'sid' => $instance->sid,
                    'duedate' => $instance->closedate,
                ];
                break;
            case 'scorm':
                $params = [
                    'completionstatusrequired' => $instance->completionstatusrequired,
                    'completionscorerequired' => $instance->completionscorerequired,
                    'completionstatusallscos' => $instance->completionstatusallscos,
                    'duedate' => $instance->timeclose,
                ];
                break;
            case 'lti':
                $params = [
                    'course' => $instance->course,
                    'intro' => str_replace('"', "'", $instance->intro),
                    'introformat' => $instance->introformat,
                    'timecreated' => $instance->timecreated,
                    'timemodified' => $instance->timemodified,
                    'typeid' => $instance->typeid,
                    'toolurl' => $instance->toolurl,
                    'securetoolurl' => $instance->securetoolurl,
                    'instructorchoicesendname' => $instance->instructorchoicesendname,
                    'instructorchoicesendemailaddr' => $instance->instructorchoicesendemailaddr,
                    'instructorchoiceallowroster' => $instance->instructorchoiceallowroster,
                    'instructorchoiceallowsetting' => $instance->instructorchoiceallowsetting,
                    'instructorcustomparameters' => $instance->instructorcustomparameters,
                    'instructorchoiceacceptgrades' => $instance->instructorchoiceacceptgrades,
                    'grade' => $instance->grade,
                    'launchcontainer' => $instance->launchcontainer,
                    'resourcekey' => $instance->resourcekey,
                    'password' => $instance->password,
                    'debuglaunch' => $instance->debuglaunch,
                    'showtitlelaunch' => $instance->showtitlelaunch,
                    'showdescriptionlaunch' => $instance->showdescriptionlaunch,
                    'servicesalt' => $instance->servicesalt,
                    'icon' => $instance->icon,
                    'secureicon' => $instance->secureicon,
                ];
                break;
            case 'choice':
                $params = [
                    'duedate' => $instance->timeclose,
                ];
                break;
            case 'data':
                $params = [
                    'duedate' => $instance->timeavailableto,
                ];
                break;
            case 'feedback':
                $params = [
                    'duedate' => $instance->timeclose,
                ];
                break;
            case 'lesson':
                $params = [
                    'duedate' => $instance->deadline,
                ];
                break;
            case 'workshop':
                $params = [
                    'duedate' => $instance->submissionend,
                ];
                break;
            default:
                $params = [];
        }

        return (count($params)) ? json_encode($params) : '';
    }

    /**
     * Get assignment submission types.
     *
     * @param $instance
     * @return string
     * @throws \dml_exception
     */
    private static function get_assignment_submissiontypes($instance) {
        global $DB;

        $assignconfig = $DB->get_records_menu(
            'assign_plugin_config',
            ['assignment' => $instance->id, 'subtype' => 'assignsubmission', 'name' => 'enabled'],
            'plugin',
            'plugin, value'
        );

        $plugins = [];

        if (count($assignconfig)) {
            foreach ($assignconfig as $pluginname => $value) {
                if ((int)$value) {
                    $plugins[$pluginname] = $pluginname;
                }
            }
        }

        return (count($plugins)) ? implode(',', $plugins) : '';
    }
}

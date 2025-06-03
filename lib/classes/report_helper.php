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
 * Report plugins helper class
 *
 * @package core
 * @subpackage report
 * @copyright 2021 Sujith Haridasan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;
use moodle_url;
use url_select;

/**
 * A helper class with static methods to help report plugins
 *
 * @package core
 * @copyright 2021 Sujith Haridasan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_helper {
    /**
     * Print the selector dropdown
     *
     * @param string $pluginname The report plugin where the header is modified
     * @return void
     */
    public static function print_report_selector(string $pluginname):void {
        global $OUTPUT, $PAGE;

        if ($reportnode = $PAGE->settingsnav->find('coursereports', \navigation_node::TYPE_CONTAINER)) {

            $menuarray = \core\navigation\views\secondary::create_menu_element([$reportnode]);
            if (empty($menuarray)) {
                return;
            }

            $coursereports = get_string('reports');
            $activeurl = '';
            if (isset($menuarray[0])) {
                // Remove the reports entry.
                $result = array_search($coursereports, $menuarray[0][$coursereports]);
                unset($menuarray[0][$coursereports][$result]);

                // Find the active node.
                foreach ($menuarray[0] as $key => $value) {
                    $check = array_search($pluginname, $value);
                    if ($check !== false) {
                        $activeurl = $check;
                    }
                }
            } else {
                $result = array_search($coursereports, $menuarray);
                unset($menuarray[$result]);

                $check = array_search($pluginname, $menuarray);
                if ($check !== false) {
                    $activeurl = $check;
                }

            }

            $select = new url_select($menuarray, $activeurl, null, 'choosecoursereport');
            $select->set_label(get_string('reporttype'), ['class' => 'accesshide']);
            echo \html_writer::tag('div', $OUTPUT->render($select), ['class' => 'tertiary-navigation']);
        }
        echo $OUTPUT->heading($pluginname, 2, 'mb-3');
    }

    /**
     * Save the last selected report in the session
     *
     * @deprecated since Moodle 4.0
     * @param int $id The course id
     * @param moodle_url $url The moodle url
     * @return void
     */
    public static function save_selected_report(int $id, moodle_url $url):void {
        global $USER;

        debugging('save_selected_report() has been deprecated because it is no longer used and will be '.
            'removed in future versions of Moodle', DEBUG_DEVELOPER);

        // Last selected report.
        if (!isset($USER->course_last_report)) {
            $USER->course_last_report = [];
        }
        $USER->course_last_report[$id] = $url;
    }

    /**
     * Check if the user is in a valid group for the course (i.e. if the user is in a group in SEPARATEGROUPS mode)
     *
     * @param context $context context for the course or module: if context is a course context, the course group mode is used,
     * if it is a module context, the module effective group mode is used (combined with the current user).
     * @param int|null $userid user id to check, if null the current user is used
     * @return bool true if the user is in a valid group (i.e. belongs to a group in SEPARATEGROUPS MODE), false otherwise
     */
    public static function has_valid_group(\context $context, ?int $userid = null): bool {
        global $USER;

        $userid = $userid ?? $USER->id;

        if ($context instanceof \context_course) {
            $courseid = $context->instanceid;
            $course = get_course($courseid);
            $groupmode = $course->groupmode;
        } else if ($context instanceof \context_module) {
            $courseid = $context->get_course_context()->instanceid;
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($context->instanceid);
            $groupmode = $cm->effectivegroupmode;
        } else {
            return true; // No groups in system context.
        }

        if ($groupmode != SEPARATEGROUPS) {
            return true; // No groups or visible all groups.
        }

        if (!has_capability('moodle/site:accessallgroups', $context, $userid)) {
            $usergroups = groups_get_all_groups($courseid, $userid);
            if (empty($usergroups)) {
                return false;
            }
        }

        return true;
    }
}

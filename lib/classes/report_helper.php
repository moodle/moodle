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
use context_course;
use moodle_url;
use stdClass;

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
    public static function print_report_selector(string $pluginname): void {
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
            $selectmenu = new \core\output\select_menu('reporttype', $menuarray, $activeurl);
            $selectmenu->set_label(get_string('reporttype'), ['class' => 'sr-only']);
            $options = \html_writer::tag(
                'div',
                $OUTPUT->render_from_template('core/tertiary_navigation_selector', $selectmenu->export_for_template($OUTPUT)),
                ['class' => 'row pb-3']
            );
            echo \html_writer::tag(
                'div',
                $options,
                ['class' => 'tertiary-navigation full-width-bottom-border ml-0', 'id' => 'tertiary-navigation']);
        } else {
            echo $OUTPUT->heading($pluginname, 2, 'mb-3');
        }
    }

    /**
     * Save the last selected report in the session
     *
     * @deprecated since Moodle 4.0
     * @param int $id The course id
     * @param moodle_url $url The moodle url
     * @return void
     */
    public static function save_selected_report(int $id, moodle_url $url): void {
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
     * Retrieve the right SQL / params for the group filter depending on the filterparams, course and group settings.
     *
     * Addionnaly, it will return the list of users visible by the current user so
     * it can be used to filter out records that are not visible. This is mainly
     * because we cannot use joins as the log tables can be in two different databases.
     *
     * @param stdClass $filterparams
     * @return array
     */
    public static function get_group_filter(stdClass $filterparams): array {
        global $DB, $USER;
        $useridfilter = null;
        // First and just in case we are in separate group, just set the $useridfilter to the list
        // of users visible by this user.
        $courseid = $filterparams->courseid ?? SITEID;
        $courseid = $courseid ?: SITEID; // Make sure that if courseid is set to 0 we use SITEID.
        $course = get_course($courseid);
        $groupmode = groups_get_course_groupmode($course);
        $groupid = $filterparams->groupid ?? 0;
        if ($groupmode == SEPARATEGROUPS || $groupid) {
            $context = context_course::instance($courseid);
            if ($groupid) {
                $cgroups = [(int) $groupid];
            } else {
                $cgroups = groups_get_all_groups(
                    $courseid,
                    has_capability('moodle/site:accessallgroups', $context) ? 0 : $USER->id
                );
                $cgroups = array_keys($cgroups);
                // If that's the case, limit the users to be in the groups only, defined by the filter.
                if (has_capability('moodle/site:accessallgroups', $context) || empty($cgroups)) {
                    $cgroups[] = USERSWITHOUTGROUP;
                }
            }
            // If that's the case, limit the users to be in the groups only, defined by the filter.
            [$groupmembersql, $groupmemberparams] = groups_get_members_ids_sql($cgroups, $context);
            $groupusers = $DB->get_fieldset_sql($groupmembersql, $groupmemberparams);
            $useridfilter = array_fill_keys($groupusers, true);
        }
        $joins = [];
        $params = [];
        if (empty($filterparams->userid)) {
            if ($groupid) {
                if ($thisgroupusers = groups_get_members($groupid)) {
                    [$sql, $sqlfilterparams] = $DB->get_in_or_equal(
                        array_keys($thisgroupusers),
                        SQL_PARAMS_NAMED,
                    );
                    $joins[] = "userid {$sql}";
                    $params = $sqlfilterparams;
                } else {
                    $joins[] = 'userid = 0'; // No users in groups, so we want something that will always be false.
                }
            }
        } else {
            $joins[] = "userid = :userid";
            $params['userid'] = $filterparams->userid;
        }

        return [
            'joins' => $joins,
            'params' => $params,
            'useridfilter' => $useridfilter,
        ];
    }
}

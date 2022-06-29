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
}

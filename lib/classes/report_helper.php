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
            if ($children = $reportnode->children) {
                // Menu to select report pages to navigate.
                $activeurl = '';
                foreach ($children as $key => $node) {
                    $name = $node->text;

                    $url = $node->action()->out(false);
                    $menu[$url] = $name;
                    if ($name === $pluginname) {
                        $activeurl = $url;
                    }
                }
            }

            if (!empty($menu)) {
                $select = new url_select($menu, $activeurl, null, 'choosecoursereport');
                $select->set_label(get_string('report'), ['class' => 'accesshide']);
                $select->attributes['style'] = "margin-bottom: 1.5rem";
                $select->class .= " mb-4";
                echo $OUTPUT->render($select);
            }
        }
    }

    /**
     * Save the last selected report in the session
     *
     * @param int $id The course id
     * @param moodle_url $url The moodle url
     * @return void
     */
    public static function save_selected_report(int $id, moodle_url $url):void {
        global $USER;

        // Last selected report.
        if (!isset($USER->course_last_report)) {
            $USER->course_last_report = [];
        }
        $USER->course_last_report[$id] = $url;
    }
}

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

namespace core_grades\output;

use moodle_url;
use html_writer;

/**
 * Renderable class for the action bar elements in the gradebook setup pages.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradebook_setup_action_bar extends action_bar {

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/gradebook_setup_action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        global $CFG;

        if ($this->context->contextlevel !== CONTEXT_COURSE) {
            return [];
        }
        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new general_action_bar($this->context,
            new moodle_url('/grade/edit/tree/index.php', ['id' => $courseid]), 'settings', 'setup');
        $data = $generalnavselector->export_for_template($output);

        // Add a button to the action bar with a link to the 'add grade item' page.
        $addgradeitemlink = new moodle_url('#');
        $addgradeitembutton = new \single_button(
            $addgradeitemlink,
            get_string('additem', 'grades'),
            'get',
            \single_button::BUTTON_SECONDARY,
            [
                'data-courseid' => $courseid,
                'data-itemid' => -1,
                'data-trigger' => 'add-item-form',
                'data-gprplugin' => 'tree'
            ]
        );
        $data['addgradeitembutton'] = $addgradeitembutton->export_for_template($output);

        // If outcomes are enabled, add a button to the action bar with a link to the 'add outcome item' page.
        if (!empty($CFG->enableoutcomes) && count(\grade_outcome::fetch_all_available($courseid)) > 0) {
            // Add a button to the action bar with a link to the 'add outcome item' page.
            $addoutcomeitem = new moodle_url('#');
            $addoutcomeitembutton = new \single_button(
                $addoutcomeitem,
                get_string('addoutcomeitem', 'grades'),
                'get',
                \single_button::BUTTON_SECONDARY,
                [
                    'class' => 'btn btn-secondary',
                    'data-courseid' => $courseid,
                    'data-itemid' => -1,
                    'data-trigger' => 'add-outcome-form',
                    'data-gprplugin' => 'tree'
                ]
            );
            $data['addoutcomeitembutton'] = $addoutcomeitembutton->export_for_template($output);
        }

        // Add a button to the action bar with a link to the 'add category' page.
        $addgradecategorybutton = new \single_button(
            $addgradeitemlink,
            get_string('addcategory', 'grades'),
            'get',
            \single_button::BUTTON_SECONDARY,
            [
                'data-courseid' => $courseid,
                'data-category' => -1,
                'data-trigger' => 'add-category-form',
                'data-gprplugin' => 'tree'
            ]
        );
        $data['addcategorybutton'] = $addgradecategorybutton->export_for_template($output);
        return $data;
    }
}

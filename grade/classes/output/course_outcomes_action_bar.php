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

/**
 * Renderable class for the action bar elements in the gradebook course outcomes page.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_outcomes_action_bar extends action_bar {

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/course_outcomes_action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        if ($this->context->contextlevel !== CONTEXT_COURSE) {
            return [];
        }
        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new general_action_bar($this->context,
            new moodle_url('/grade/edit/outcome/course.php', ['id' => $courseid]), 'outcome', 'course');
        $data = $generalnavselector->export_for_template($output);

        if (has_capability('moodle/grade:manageoutcomes', $this->context)) {
            // Add a button to the action bar with a link to the 'manage outcomes' page.
            $manageoutcomeslink = new moodle_url('/grade/edit/outcome/index.php', ['id' => $courseid]);
            $manageoutcomesbutton = new \single_button($manageoutcomeslink, get_string('manageoutcomes', 'grades'),
                'get', true);
            $data['manageoutcomesbutton'] = $manageoutcomesbutton->export_for_template($output);
        }

        return $data;
    }
}

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
 * Renderable class for the action bar elements in the gradebook scales page.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scales_action_bar extends action_bar {

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/scales_action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $data = [];
        $courseid = 0;
        // If in the course context, we should display the general navigation selector in gradebook.
        if ($this->context->contextlevel === CONTEXT_COURSE) {
            $courseid = $this->context->instanceid;
            // Get the data used to output the general navigation selector.
            $generalnavselector = new general_action_bar($this->context,
                new moodle_url('/grade/edit/scale/index.php', ['id' => $courseid]), 'scale', 'scale');
            $data = $generalnavselector->export_for_template($output);
        }
        // Add a button to the action bar with a link to the 'add new scale' page.
        $addnewscalelink = new moodle_url('/grade/edit/scale/edit.php', ['courseid' => $courseid]);
        $addnewscalebutton = new \single_button($addnewscalelink, get_string('scalescustomcreate'),
            'get', true);
        $data['addnewscalebutton'] = $addnewscalebutton->export_for_template($output);

        return $data;
    }
}

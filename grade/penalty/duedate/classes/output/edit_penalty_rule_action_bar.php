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

namespace gradepenalty_duedate\output;

use core\output\single_button;
use core\url;

/**
 * Renderable class for the action bar elements in the penalty rule page.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_penalty_rule_action_bar extends view_penalty_rule_action_bar {
    #[\Override]
    public function get_template(): string {
        return 'gradepenalty_duedate/edit_penalty_rule_action_bar';
    }

    #[\Override]
    public function export_for_template(\renderer_base $output): array {
         $data = [];

         $contextid = $this->context->id;

        // Title.
        $data['title'] = $output->heading($this->title);

        // Delete all rules button.
        $deleteallruleurl = new url($this->url->out(), [
            'contextid' => $contextid,
            'deleteallrules' => 1,
        ]);
        $deleteallrulebutton = new single_button(
            $deleteallruleurl,
            get_string('deleteallrules', 'gradepenalty_duedate'),
            'get',
            single_button::BUTTON_DANGER
        );
        $data['deleteallrulebutton'] = $deleteallrulebutton->export_for_template($output);

        return $data;
    }
}

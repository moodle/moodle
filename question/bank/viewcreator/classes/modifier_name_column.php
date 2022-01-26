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

namespace qbank_viewcreator;

use core_question\local\bank\column_base;

/**
 * A column type for the name of the question last modifier.
 *
 * @package   qbank_viewcreator
 * @copyright 2009 Tim Hunt
 * @author    2021 Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modifier_name_column extends column_base {

    public function get_name(): string {
        return 'modifiername';
    }

    protected function get_title(): string {
        return get_string('lastmodifiedby', 'question');
    }

    protected function display_content($question, $rowclasses): void {
        global $PAGE;
        $displaydata = [];

        if (!empty($question->modifierfirstname) && !empty($question->modifierlastname)) {
            $u = new \stdClass();
            $u = username_load_fields_from_object($u, $question, 'modifier');
            $displaydata['date'] = userdate($question->timemodified, get_string('strftimedatetime', 'langconfig'));
            $displaydata['modifier'] = fullname($u);
            echo $PAGE->get_renderer('qbank_viewcreator')->render_modifier_name($displaydata);
        }
    }

    public function get_extra_joins(): array {
        return ['um' => 'LEFT JOIN {user} um ON um.id = q.modifiedby'];
    }

    public function get_required_fields(): array {
        $allnames = \core_user\fields::get_name_fields();
        $requiredfields = [];
        foreach ($allnames as $allname) {
            $requiredfields[] = 'um.' . $allname . ' AS modifier' . $allname;
        }
        $requiredfields[] = 'q.timemodified';
        return $requiredfields;
    }

    public function is_sortable(): array {
        return [
            'firstname' => ['field' => 'um.firstname', 'title' => get_string('firstname')],
            'lastname' => ['field' => 'um.lastname', 'title' => get_string('lastname')],
            'timemodified' => ['field' => 'q.timemodified', 'title' => get_string('date')]
        ];
    }

}

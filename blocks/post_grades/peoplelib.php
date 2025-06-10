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

defined('MOODLE_INTERNAL') || die();

// Dynamic class definition... why not?
class post_grades_audit_people extends ues_people_element_output {
    public function __construct() {
        $str = get_string('student_audit', 'block_post_grades');
        parent::__construct('student_audit', $str);
    }

    public function format($user) {
        return empty($user->student_audit) ? 'N' : 'Y';
    }
}
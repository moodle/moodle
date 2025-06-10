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

class post_grades_degree extends post_grades_student_table implements post_filtered {
    public function can_post($section) {
        $students = $section->students();

        if (empty($students)) {
            return false;
        }

        $userid = function($student) {
            return $student->userid;
        };

        $filters = ues::where()
            ->id->in(array_map($userid, $students))
            ->user_degree->equal('Y');

        // Explicit boolean return.
        return ues_user::count($filters) ? true : false;
    }

    public function is_acceptable($student) {
        $user = ues_user::upgrade($student)->fill_meta();

        return isset($user->user_degree) && $user->user_degree == 'Y';
    }
}

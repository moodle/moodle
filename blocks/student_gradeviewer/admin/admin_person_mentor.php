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
 *
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class admin_person_mentor extends student_mentor_admin_page {
    public function __construct() {
        parent::__construct(
            'person_mentor',
            optional_param('path', 0, PARAM_INT)
        );

        $this->parents = array();
        if (has_capability($this->capabilities[0], $this->get_context())) {
            $this->parents[] = 'sports_mentor';
        }

        if (has_capability($this->capabilities[1], $this->get_context())) {
            $this->parents[] = 'academic_mentor';
        }
    }

    public function ui_filters() {
        global $OUTPUT;

        $options = array();

        $tonamed = function($user) {
            return fullname($user);
        };

        $touserid = function($assign) {
            return $assign->userid;
        };

        foreach ($this->parents as $parent) {
            $label = get_string($parent, 'block_student_gradeviewer');

            $assigns = $parent::get_all();

            if (empty($assigns)) {
                continue;
            }

            $userids = array_values(array_map($touserid, $assigns));

            $filters = ues::where()->id->in($userids);
            $users = ues_user::get_all($filters, 'firstname, lastname ASC');

            if (isset($users[$this->path])) {
                $selected = fullname($users[$this->path]);
            }

            $options[] = array($label => array_map($tonamed, $users));
        }

        $url = new moodle_url('/blocks/student_gradeviewer/admin.php', array(
            'type' => $this->get_type()
        ));

        $html = $OUTPUT->single_select($url, 'path', $options, $this->path);

        if (!empty($this->path)) {
            $assignment = get_string(
                'assigning_students',
                'block_student_gradeviewer', $selected
            );

            $html .= $OUTPUT->heading($assignment, 3);
        }

        return $html;
    }

    public function message_params($userid) {
        return array('userid' => $this->path, 'path' => $userid);
    }

    public function get_selected_users() {
        $filters = ues::where()->userid->equal($this->path);

        $selected = person_mentor::get_all($filters);

        $rtn = array();
        foreach ($selected as $assign) {
            $user = $assign->derive_path();
            $rtn[$assign->path] = fullname($user) . " ($user->email)";
        }

        return $rtn;
    }

    public function user_form() {
        global $OUTPUT;

        if (empty($this->path)) {
            $noone = get_string('na_person', 'block_student_gradeviewer');
            return $OUTPUT->box($OUTPUT->notification($noone));
        } else {
            return parent::user_form();
        }
    }
}

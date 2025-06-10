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

/**
 * Members view class deals with generating the HTM for the members table. It
 * can generate members table for either tutors or students in a course by
 * calling the "build_members_view" method. Defaults to rendering the students
 * members table if no display role is given.
 */
class members_view {
    public $course;
    public $coursemodule;
    public $turnitintooltwoassignment;
    public $turnitintooltwoview;

    public function __construct($course=null, $coursemodule=null, $turnitintooltwoview=null, $turnitintooltwoassignment=null) {
        $this->course = $course;
        $this->coursemodule = $coursemodule;
        $this->turnitintooltwoview = $turnitintooltwoview;
        $this->turnitintooltwoassignment = $turnitintooltwoassignment;
    }

    /**
     * Method that generates the members view HTML. Depending on the displayrole
     * passed will generate the HTML for the users with that role.
     * @return string Members HTML for a role
     */
    public function build_members_view($displayrole = "students") {
        $istutor = $this->is_tutor();

        if (!$istutor) {
            turnitintooltwo_print_error('permissiondeniederror', 'turnitintooltwo');
            exit();
        }

        $wrapperclass = $displayrole == "tutors" ? "members-instructors" : "members-students";

        // Wrapper element for strong CSS selectors.
        $output = html_writer::start_tag("div", array("class" => "mod_turnitintooltwo_members " . $wrapperclass));

        $output .= $this->build_intro_message($displayrole);
        $output .= $this->build_members_table($displayrole);
        $output .= $this->build_add_tutors_form($displayrole);

        $output .= html_writer::end_tag("div");

        return $output;
    }

    /**
     * Util method to check if the current user is an instructor by checking if
     * they can grade.
     * @return boolean Bool if the current user is an instructor
     */
    public function is_tutor () {
        return has_capability('mod/turnitintooltwo:grade', context_module::instance($this->coursemodule->id));
    }

    /**
     * Returns the Turnitin role for the display role passed in the query param
     * "do" (tutors or  students) to view.php.
     * @param  string $displayrole The do action passed to view.php when
     *                             displaying members
     * @return string              Turnitin role that maps to the display role
     */
    public function get_role_for_display_role ($displayrole) {
        return $displayrole == "tutors" ? 'Instructor' : 'Learner';
    }

    /**
     * Generates HTM for the message that is displayed above the members table.
     * This differs depending on if we're showing the student members or
     * instructor members.
     * @param  string $displayrole Role of the members we want to display for
     * @return string              HTML message to display before the members
     *                             table
     */
    public function build_intro_message ($displayrole = "students") {
        global $OUTPUT;

        if ($displayrole == "tutors") {
            $introtextkey = 'turnitintutors_desc';
        } else {
            $introtextkey = 'turnitinstudents_desc';
        }

        $introtext = get_string($introtextkey, 'turnitintooltwo');

        return $OUTPUT->box($introtext, 'message message-members-intro');
    }

    /**
     * Generates the HTML for the members table given a role will generate for
     * either the course students or instructors.
     * @param  string $role Members with this role to display
     * @return string       HTML of the members table
     */
    public function build_members_table ($displayrole="students") {
        $turnitintooltwoassignment = $this->turnitintooltwoassignment;
        $turnitintooltwoview = $this->turnitintooltwoview;
        $coursemodule = $this->coursemodule;
        $role = $this->get_role_for_display_role($displayrole);

        return $turnitintooltwoview->init_tii_member_by_role_table($coursemodule, $turnitintooltwoassignment, $role);
    }

    /**
     * Generates the HTML for the add tutors form that is displayed under the
     * members table. Only generates the form for the display role "tutors"
     * otherwise will return an empty string (i.e. display nothing).
     * @param  string $displayrole Which members table is being shown
     * @return string              HTML for add tutors form to display
     */
    public function build_add_tutors_form ($displayrole) {
        // early escape only show the add tutors in the tutors members list
        if ($displayrole != "tutors") {
            return "";
        }

        $tutors = $this->turnitintooltwoassignment->get_tii_users_by_role("Instructor", "mdl");
        return $this->turnitintooltwoview->show_add_tii_tutors_form($this->coursemodule, $tutors);
    }
}

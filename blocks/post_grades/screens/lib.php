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

// Screens can implement this interface if the the screen requires certain course data before application.
interface post_filtered {
    public function can_post($section);
}

abstract class post_grades_screen {

    public function get_return_state() {
        require_once(dirname(__FILE__) . '/returnlib.php');

        return new post_grades_good_return();

        $s = ues::gen_str('block_post_grades');

        // Will need this later.
        $sections = ues_section::from_course($this->course, true);
        $section = post_grades::find_section($this->group, $sections);

        // Filter Audits at the screen level.
        $auditers = post_grades::pull_auditing_students($section);
        foreach ($auditers as $audit) {
            unset($this->students[$audit->id]);
        }

        // Shim for 1.9.
        $course = $section->course()->fill_meta();
    }

    public abstract function html();
}

abstract class post_grades_student_table extends post_grades_screen {
    public function __construct($period, $course, $group) {
        $this->course = $course;
        $this->period = $period;
        $this->group = $group;

        $groupid = empty($this->group) ? 0 : $this->group->id;

        $this->context = context_course::instance($this->course->id);
        $graded = get_config('moodle', 'gradebookroles');
        $this->students = array();
        if (count(explode(',', $graded)) > 1) {
            $roleids = explode(',', $graded);
            foreach ($roleids as $roleid) {
                // Keeping the first user appearance.
                $this->students = $this->students + get_role_users(
                    $roleid, $this->context, false, '',
                    'u.id, u.lastname, u.firstname', null, $groupid
                );
            }
        } else {
            $roleids = $graded;
            $this->students = get_role_users($graded, $this->context,
                false, '', 'u.lastname, u.firstname', null, $groupid);
        }
    }

    public function constructor() {
        $class = get_class($this);

        return function($period, $course, $group) use ($class) {
            return new $class($period, $course, $group);
        };
    }

    public abstract function is_acceptable($student);

    public function html() {
        $table = new html_table();

        $table->head = array(
            get_string('lastname') . ', ' . get_string('firstname') . ' (' . get_string('alternatename') . ')',
            get_string('idnumber'),
            get_string('grade', 'grades')
        );

        $courseitem = grade_item::fetch(array(
            'itemtype' => 'course',
            'courseid' => $this->course->id
        ));

        foreach ($this->students as $student) {
            if (!$this->is_acceptable($student)) {
                continue;
            }

            $line = new html_table_row();

            if (isset($student->alternatename)) {
                $name = "$student->lastname, $student->alternatename ($student->firstname)";
            } else {
                $name = "$student->lastname, $student->firstname";
            }
            $url = new moodle_url('/grade/report/singleview/index.php', array(
                'item' => 'user',
                'itemid' => $student->id,
                'group' => $this->group->id,
                'id' => $this->course->id
            ));

            $gradegrade = grade_grade::fetch(array(
                'itemid' => $courseitem->id,
                'userid' => $student->id
            ));

            if (empty($gradegrade)) {
                $gradegrade = new grade_grade();
                $gradegrade->finalgrade = null;
            }

            // Don't bother showing incompletes.
            if ($gradegrade->is_overridden() and $gradegrade->finalgrade == null) {
                continue;
            }

            if ($gradegrade->itemid) {
                $courseitem->grademax = $gradegrade->get_grade_max();
            }

            $line->cells[] = html_writer::link($url, $name);
            $line->cells[] = $student->idnumber;
            $line->cells[] = grade_format_gradevalue(
                $gradegrade->finalgrade,
                $courseitem, true,
                $courseitem->get_displaytype()
            );

            $table->data[] = $line;
        }

        if (empty($table->data)) {
            global $OUTPUT;
            $post = get_string($this->period->post_type, 'block_post_grades');
            $msg = get_string('no_students', 'block_post_grades', $post);
            return $OUTPUT->notification($msg);
        } else {
            return html_writer::table($table);
        }
    }
}

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

require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();

abstract class post_grades {

    public static function pull_auditing_students($courseorsection) {
        $filters = ues::where()
            ->join('{enrol_ues_students}', 'stu')->on('id', 'userid')
            ->join('{enrol_ues_studentmeta}', 'stum')->on('stu.id', 'studentid')
            ->stum->name->equal('student_audit')
            ->stum->value->equal(1)
            ->stu->status->in(ues::ENROLLED, ues::PROCESSED);

        if ($courseorsection instanceof ues_section) {
            $filters->stu->sectionid->equal($courseorsection->id);
        } else {
            $sections = ues_section::from_course($courseorsection);
            $filters->stu->sectionid->in(array_keys($sections));
        }

        return ues_user::get_all($filters);
    }

    public static function valid_types() {
        $typekeys = array_keys(self::screens());

        $toname = function($key) {
            return get_string($key, 'block_post_grades');
        };

        return array_combine($typekeys, array_map($toname, $typekeys));
    }

    public static function active_periods_for_sections($sections) {
        global $DB;

        if (empty($sections)) {
            return null;
        }

        $semesterid = reset($sections)->semesterid;
        $now = time();

        $filters = ues::where()
            ->semesterid->equal($semesterid)
            ->start_time->less($now)
            ->end_time->greater($now);

        $periods = $DB->get_records_select(
            'block_post_grades_periods', $filters->sql(), null, 'start_time ASC'
        );

        return $periods;
    }

    public static function active_periods($course) {
        $sections = ues_section::from_course($course);

        return self::active_periods_for_sections($sections);
    }

    public static function is_active($course) {
        $period = self::active_periods($course);

        return !empty($period);
    }

    public static function screendir() {
        return dirname(__FILE__) . '/screens';
    }

    public static function screens() {
        $screendir = self::screendir();
        $screens = scandir($screendir);

        $return = array();
        foreach ($screens as $screen) {
            $lib = "$screendir/$screen/lib.php";
            if (!preg_match('/^\./', $screen) and file_exists($lib)) {
                $return[$screen] = $lib;
            }
        }

        return $return;
    }

    public static function screenclass($screen) {
        $screens = self::screens();

        if (!isset($screens[$screen])) {
            return null;
        }

        require_once(dirname(__FILE__) . '/screens/lib.php');
        require_once($screens[$screen]);

        return "post_grades_$screen";
    }

    public static function create($period, $course, $group) {
        $class = self::screenclass($period->post_type);
        if (is_null($class)) {
            throw new Exception(get_string('notactive', 'block_post_grades'));
        } else {
            return new $class($period, $course, $group);
        }
    }

    public static function valid_groups($course) {
        $groups = groups_get_all_groups($course->id);

        $pattern = '/\w{2,4} \d{4} \d{3}/';
        $validname = function($group) use ($pattern) {
            return preg_match($pattern, $group->name);
        };

        return array_filter($groups, $validname);
    }

    public static function find_section($group, $sections) {
        $found = false;
        foreach ($sections as $section) {
            if ($section->group()->id == $group->id) {
                $found = $section;
                break;
            }
        }

        return $found;
    }

    public static function already_posted_section($section, $period) {
        global $USER, $DB;

        $params = array(
            'userid' => $USER->id,
            'sectionid' => $section->id,
            'periodid' => $period->id
        );

        return $DB->record_exists('block_post_grades_postings', $params);
    }

    public static function already_posted($course, $group, $period) {
        $sections = ues_section::from_course($course, true);
        $section = self::find_section($group, $sections);

        return self::already_posted_section($section, $period);
    }

    public static function find_postings_by_shortname($shortname) {
        global $DB;
        $mainuserfields = user_picture::fields('u', array('id'), 'userid');

        $sql = 'SELECT post.id, ' . $mainuserfields . ',
                       sec.sec_number, p.post_type, c.fullname
            FROM {course} c,
                 {enrol_ues_sections} sec,
                 {block_post_grades_periods} p,
                 {block_post_grades_postings} post,
                 {user} u
           WHERE c.shortname LIKE "%'.$shortname.'%"
             AND sec.idnumber = c.idnumber
             AND sec.id = post.sectionid
             AND u.id = post.userid
             AND p.id = post.periodid
           ORDER BY p.post_type, sec.sec_number ASC, u.lastname DESC';

        return $DB->get_records_sql($sql);
    }
}
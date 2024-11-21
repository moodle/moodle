<?php

namespace local_intelliboard\reports;

trait report_trait
{
    /**
     * Get list of IDs of teacher courses
     *
     * @param $teacherid
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function get_teacher_courses($teacherid)
    {
        global $DB;

        $teacherrolesfilter = $DB->get_in_or_equal($this->get_teacher_roles(), SQL_PARAMS_NAMED, 'tr');

        $teachercourses = $DB->get_records_sql(
            "SELECT DISTINCT cx.instanceid
               FROM {role_assignments} ra
               JOIN {context} cx ON cx.id = ra.contextid AND cx.contextlevel = 50
               JOIN {role} r ON r.id = ra.roleid
              WHERE ra.userid = :teacher_id AND ra.roleid {$teacherrolesfilter[0]}",
            array_merge(["teacher_id" => $teacherid], $teacherrolesfilter[1])
        );

        return array_keys($teachercourses);
    }

    /**
     * Get list of IDs of course students
     *
     * @param $courses
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function get_courses_students($courses)
    {
        global $DB;

        if (!$courses) {
            return ["-1"];
        }

        $studentrolesfilter = $DB->get_in_or_equal($this->get_student_roles(), SQL_PARAMS_NAMED, 'st');
        $coursesfilter = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED, 'cr');

        $coursestudents = $DB->get_records_sql(
            "SELECT DISTINCT ra.userid
               FROM {role_assignments} ra
               JOIN {context} cx ON cx.id = ra.contextid AND cx.contextlevel = 50 AND cx.instanceid {$coursesfilter[0]}
               JOIN {role} r ON r.id = ra.roleid
              WHERE ra.roleid {$studentrolesfilter[0]}",
            array_merge($studentrolesfilter[1], $coursesfilter[1])
        );

        if (!$coursestudents) {
            return ["-1"];
        }

        return array_keys($coursestudents);
    }

    /**
     * Get list of IDs of teacher roles
     *
     * @return array
     * @throws \dml_exception
     */
    protected function get_teacher_roles()
    {
        $roles = explode(',', get_config('local_intelliboard', 'filter10'));

        if (!$roles) {
            return ["-1"];
        }

        return $roles;
    }

    /**
     * Get list of IDs of student roles
     *
     * @return array
     * @throws \dml_exception
     */
    protected function get_student_roles()
    {
        $roles = explode(',', get_config('local_intelliboard', 'filter11'));

        if (!$roles) {
            return ["-1"];
        }

        return $roles;
    }
}
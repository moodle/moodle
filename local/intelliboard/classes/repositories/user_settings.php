<?php

namespace local_intelliboard\repositories;

use local_intelliboard\helpers\DBHelper;

class user_settings
{
    public static function getInstructorDashboardCourses($userid)
    {
        global $DB;

        if (!get_config('local_intelliboard', 'instructor_course_visibility')) {
            $sqlcourseivsibility = " AND c.visible = 1";
        } else {
            $sqlcourseivsibility = "";
        }

        $teacherroles = get_config('local_intelliboard', 'filter10');
        list($rolefiltersql, $rolesfilterparams) = intelliboard_filter_in_sql(
            $teacherroles,
            'ra.roleid',
            ['userid' => $userid]
        );

        $numerictypecast = DBHelper::get_typecast("numeric");

        return $DB->get_records_sql(
            "SELECT DISTINCT c.*
               FROM {local_intelliboard_assign} lia
               JOIN {course} c ON c.id = lia.instance{$numerictypecast} {$sqlcourseivsibility}
               JOIN {context} cx ON cx.contextlevel = :coursecontextlevel AND cx.instanceid = c.id
               JOIN {role_assignments} ra ON ra.contextid = cx.id AND ra.userid = :userid2 {$rolefiltersql}
              WHERE lia.rel = 'instructordashboard' AND lia.type = 'courses' AND lia.userid = :userid",
            array_merge(
                ['userid' => $userid, 'coursecontextlevel' => CONTEXT_COURSE, 'userid2' => $userid],
                $rolesfilterparams
            )
        );
    }
}

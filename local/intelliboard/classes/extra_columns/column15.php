<?php

namespace local_intelliboard\extra_columns;

class column15 extends base_column {
    public function get_join() {
        global $DB;

        list($rolesinsql, $params) = $DB->get_in_or_equal(
            explode(',', $this->params->teacher_roles), SQL_PARAMS_NAMED, 'crs_tchrs_col15'
        );
        $coursetablealias = isset($this->fields[1]) ? $this->fields[1] : "c.id";
        $teachersgroupconcat = get_operator(
            'GROUP_CONCAT', "CONCAT(u.firstname, ' ', u.lastname)", ['separator' => ', ']
        );

        if ($this->params->function === 'report3' && !empty($this->params->custom2)) {
            $jointype = 'JOIN';
            list($teachersinsql, $teachersinparams) = $DB->get_in_or_equal(
                explode(',', $this->params->custom2), SQL_PARAMS_NAMED, 'crs_tchrs_in_col15'
            );
            $teacherssql = ' AND u.id ' . $teachersinsql;
            $params = array_merge($params, $teachersinparams);
        } else {
            $teacherssql = '';
            $jointype = 'LEFT JOIN';
        }

        return [
            "{$jointype} (SELECT cx.instanceid, {$teachersgroupconcat} as names
                          FROM {context} cx
                          JOIN {role_assignments} ra ON ra.contextid = cx.id AND ra.roleid {$rolesinsql}
                          JOIN {user} u ON u.id = ra.userid {$teacherssql}
                         WHERE cx.contextlevel = 50
                      GROUP BY cx.instanceid
                       ) course_teachers ON course_teachers.instanceid = {$coursetablealias}",
            $params
        ];
    }
}
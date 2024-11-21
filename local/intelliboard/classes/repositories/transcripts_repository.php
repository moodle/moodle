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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

namespace local_intelliboard\repositories;

use local_intelliboard\helpers\DBHelper;

class transcripts_repository {

    public static function get_transcripts_course_grades($params, $record = null) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/gradelib.php');

        $record = ($record) ? $record : new \stdClass();

        $grade = grade_get_course_grade($params['userid'], $params['courseid']);
        $record->formattedgrade = (!empty($grade->str_grade) and $grade->str_grade != 'Error') ? $grade->str_grade : '';
        $record->finalgrade     = (!empty($grade->grade)) ? $grade->grade : 0;
        $record->grademax       = (!empty($grade->item->grademax)) ? $grade->item->grademax : 0;
        $record->grademin       = (!empty($grade->item->grademin)) ? $grade->item->grademin : 0;

        $gradesdetails = $DB->get_records_sql("SELECT gg.id as gradeid, gi.id as gradeitemid
                                                      FROM {grade_grades} gg
                                                      JOIN {grade_items} gi ON gi.id = gg.itemid
                                                     WHERE gg.userid = :userid
                                                       AND gi.courseid = :courseid
                                                       AND gi.itemtype = :gradeitemtype
                                                  ORDER BY gg.timemodified DESC",
            ['userid' => $params['userid'], 'courseid' => $params['courseid'], 'gradeitemtype' => 'course']
        );
        if (count($gradesdetails)) {
            $gradedetails = reset($gradesdetails);

            $record->gradeitemid = (!empty($gradedetails->gradeitemid)) ? $gradedetails->gradeitemid : 0;
            $record->gradeid = (!empty($gradedetails->gradeid)) ? $gradedetails->gradeid : 0;
        }

        return $record;
    }

    public static function get_transcripts_module_grades($params, $record = null) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/gradelib.php');

        $record = ($record) ? $record : new \stdClass();

        $grades = grade_get_grades($params['courseid'], 'mod', $params['modname'], $params['instance'], $params['userid']);
        if (!empty($grades->items[0]->grades)) {
            $grade = reset($grades->items[0]->grades);

            $record->grademax       = (!empty($grade->item->grademax)) ? $grade->item->grademax : $grades->items[0]->grademax;
            $record->grademin       = (!empty($grade->item->grademin)) ? $grade->item->grademin : $grades->items[0]->grademin;
            $record->formattedgrade = (!empty($grade->str_grade) and $grade->str_grade != 'Error') ? $grade->str_grade : '';
            $record->finalgrade     = (!empty($grade->grade)) ? $grade->grade : 0;
            $record->gradeitemid    = $grades->items[0]->id;

            $gradegrades = $DB->get_records_sql("SELECT gg.id as gradeid
                                                       FROM {grade_grades} gg
                                                      WHERE gg.userid = :userid
                                                        AND gg.itemid = :itemid
                                                   ORDER BY gg.timemodified DESC",
                ['userid' => $params['userid'], 'itemid' => $record->gradeitemid]
            );
            if (count($gradegrades)) {
                $gradegrade = reset($gradegrades);
                $record->gradeid = (!empty($gradegrade->gradeid)) ? $gradegrade->gradeid : 0;
            }
        }

        return $record;
    }

    public static function get_transcripts_enrolments($params, $single = false) {
        global $DB;

        $rolessql = DBHelper::get_group_concat('roleid', ',');
        $groupssql = DBHelper::get_group_concat('g.id', ',');

        $sql = "SELECT ue.*, e.enrol, ra.rolesids, gr.groupsids
                            FROM {user_enrolments} ue
                       LEFT JOIN {enrol} e ON e.id = ue.enrolid
                       LEFT JOIN {context} ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = :contextlevel
                       LEFT JOIN (
                                    SELECT contextid, userid, $rolessql as rolesids
                                      FROM {role_assignments}
                                  GROUP BY userid, contextid
                                  ) ra ON ra.contextid = ctx.id AND ra.userid = ue.userid
                      LEFT JOIN (
                                    SELECT g.courseid, gm.userid, $groupssql as groupsids
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                  GROUP BY g.courseid, gm.userid
                                ) gr ON gr.courseid = e.courseid AND gr.userid = ue.userid
                           WHERE ue.id > 0";
        $sqlparams = ['contextlevel' => CONTEXT_COURSE];

        if (!empty($params['userid'])) {
            $sql .= " AND ue.userid = :userid";
            $sqlparams['userid'] = $params['userid'];
        }

        if (!empty($params['courseid'])) {
            $sql .= " AND e.courseid = :courseid";
            $sqlparams['courseid'] = $params['courseid'];
        }

        if (!empty($params['ueid'])) {
            $sql .= " AND ue.id = :ueid";
            $sqlparams['ueid'] = $params['ueid'];
        }

        if ($single) {
            return $DB->get_record_sql($sql, $sqlparams);
        }

        return $DB->get_records_sql($sql, $sqlparams);
    }

}

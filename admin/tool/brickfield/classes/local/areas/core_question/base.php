<?php
// This file is part of the Query submission plugin
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

namespace tool_brickfield\local\areas\core_question;

use core\event\question_created;
use core\event\question_updated;
use tool_brickfield\area_base;

/**
 * Base class for various question-related areas
 *
 * This is an abstract class so it will be skipped by manager when it finds all areas
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends area_base {

    /**
     * Find recordset of the relevant areas.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        global $DB;

        if (($event instanceof question_created) || ($event instanceof question_updated)) {
            $rs = $DB->get_recordset_sql(
                "SELECT {$this->get_type()} AS type,
                ctx.id AS contextid,
                {$this->get_standard_area_fields_sql()}
                t.id AS itemid,
                {$this->get_course_and_cat_sql($event)}
                t.{$this->get_fieldname()} AS content
            FROM {question} t
            INNER JOIN {question_categories} qc ON qc.id = t.category
            INNER JOIN {context} ctx ON ctx.id = qc.contextid
            WHERE (t.id = :refid)
            ORDER BY t.id",
                [
                    'refid' => $event->objectid,
                ]);
            return $rs;
        }
        return null;
    }

    /**
     * Find recordset of the course areas.
     * @param int $courseid
     * @return \moodle_recordset
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_course_areas(int $courseid): ?\moodle_recordset {
        global $DB;

        $coursecontext = \context_course::instance($courseid);
        return $DB->get_recordset_sql(
            "SELECT {$this->get_type()} AS type,
                ctx.id AS contextid,
                {$this->get_standard_area_fields_sql()}
                t.id AS itemid,
                {$courseid} AS courseid,
                null AS categoryid,
                t.{$this->get_fieldname()} AS content
            FROM {question} t
            INNER JOIN {question_categories} qc ON qc.id = t.category
            INNER JOIN {context} ctx ON ctx.id = qc.contextid
            WHERE (ctx.contextlevel = :ctxcourse AND ctx.id = qc.contextid AND ctx.instanceid = :courseid) OR
                (ctx.contextlevel = :module AND {$DB->sql_like('ctx.path', ':coursecontextpath')})
            ORDER BY t.id ASC",
            [
                'ctxcourse' => CONTEXT_COURSE,
                'courseid' => $courseid,
                'module' => CONTEXT_MODULE,
                'coursecontextpath' => $DB->sql_like_escape($coursecontext->path) . '/%',
            ]);
    }

    /**
     * Return an array of area objects that contain content at the site and system levels only. This would be question content from
     * question categories at the system context only.
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    public function find_system_areas(): ?\moodle_recordset {
        global $DB;

        $select = 'SELECT ' . $this->get_type() . ' AS type, qc.contextid AS contextid, ' . $this->get_standard_area_fields_sql() .
            ' t.id AS itemid, ' . SITEID . ' as courseid, cc.id as categoryid,' .
            ' t.'.$this->get_fieldname().' AS content ';
        $from = 'FROM {question} t ' .
            'INNER JOIN {question_categories} qc ON qc.id = t.category ' .
            'INNER JOIN {context} ctx ON ctx.id = qc.contextid ' .
            'LEFT JOIN {course_categories} cc ON cc.id = ctx.instanceid AND ctx.contextlevel = :coursecat ';
        $where = 'WHERE (ctx.contextlevel = :syscontext) OR (ctx.contextlevel = :coursecat2) ';
        $order = 'ORDER BY t.id';
        $params = [
            'syscontext' => CONTEXT_SYSTEM,
            'coursecat' => CONTEXT_COURSECAT,
            'coursecat2' => CONTEXT_COURSECAT,
        ];

        return $DB->get_recordset_sql($select . $from . $where . $order, $params);
    }

    /**
     * Returns the moodle_url of the page to edit the error.
     * @param \stdClass $componentinfo
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public static function get_edit_url(\stdClass $componentinfo): \moodle_url {
        $questionid = $componentinfo->itemid;
        // Question answers are editable on main question page
        // Hence, use refid for these links.
        if ($componentinfo->tablename == 'question_answers') {
            $questionid = $componentinfo->refid;
        }
        // Default to SITEID if courseid is null, i.e. system or category level questions.
        $thiscourseid = ($componentinfo->courseid !== null) ? $componentinfo->courseid : SITEID;
        return new \moodle_url('/question/question.php', ['courseid' => $thiscourseid, 'id' => $questionid]);
    }

    /**
     * Determine the course and category id SQL depending on the specific context associated with question data.
     * @param \core\event\base $event
     * @return string
     * @throws \dml_exception
     */
    protected function get_course_and_cat_sql(\core\event\base $event): string {
        global $DB;

        $courseid = 'null';
        $catid = 'null';

        $sql = "
                SELECT ctx.instanceid, cm.course as courseid, ctx.contextlevel
                FROM {question} q
                INNER JOIN {question_categories} qc ON qc.id = q.category
                INNER JOIN {context} ctx ON ctx.id = qc.contextid
                LEFT JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :coursemodule
                WHERE q.id = :refid
            ";
        $params = [
            'coursemodule' => CONTEXT_MODULE,
            'refid' => $event->objectid,
        ];

        if ($record = $DB->get_record_sql($sql, $params)) {
            if ($record->contextlevel == CONTEXT_MODULE) {
                $courseid = $record->courseid;
            } else if ($record->contextlevel == CONTEXT_COURSE) {
                $courseid = $record->instanceid;
            } else if ($record->contextlevel == CONTEXT_COURSECAT) {
                $catid = $record->instanceid;
            } else if ($record->contextlevel == CONTEXT_SYSTEM) {
                $courseid = 1;
            }
        }

        return "
            {$courseid} AS courseid,
            {$catid} AS categoryid,
        ";
    }
}

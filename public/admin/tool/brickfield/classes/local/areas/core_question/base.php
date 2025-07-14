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
 * Base class for various question-related areas.
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
     *
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        global $DB;
        if (($event instanceof question_created) || ($event instanceof question_updated)) {
            $sql = "SELECT {$this->get_type()} AS type,
                       ctx.id AS contextid,
                       {$this->get_standard_area_fields_sql()}
                       q.id AS itemid,
                       {$this->get_course_and_cat_sql($event)}
                       q.{$this->get_fieldname()} AS content
                  FROM {question} q
            INNER JOIN {question_versions} qv
                    ON qv.questionid = q.id
            INNER JOIN {question_bank_entries} qbe
                    ON qbe.id = qv.questionbankentryid
            INNER JOIN {question_categories} qc
                    ON qc.id = qbe.questioncategoryid
            INNER JOIN {context} ctx
                    ON ctx.id = qc.contextid
                 WHERE (q.id = :refid)
              ORDER BY q.id";

            $rs = $DB->get_recordset_sql($sql, ['refid' => $event->objectid]);
            return $rs;
        }
        return null;
    }

    /**
     * Find recordset of the course areas.
     *
     * @param int $courseid
     * @return \moodle_recordset
     */
    public function find_course_areas(int $courseid): ?\moodle_recordset {
        global $DB;
        $coursecontext = \context_course::instance($courseid);
        $param = [
            'module' => CONTEXT_MODULE,
            'coursecontextpath' => $DB->sql_like_escape($coursecontext->path) . '/%',
        ];

        $sql = "SELECT {$this->get_type()} AS type,
                       ctx.id AS contextid,
                       {$this->get_standard_area_fields_sql()}
                       q.id AS itemid,
                       {$courseid} AS courseid,
                       null AS categoryid,
                       q.{$this->get_fieldname()} AS content
                  FROM {question} q
            INNER JOIN {question_versions} qv
                    ON qv.questionid = q.id
            INNER JOIN {question_bank_entries} qbe
                    ON qbe.id = qv.questionbankentryid
            INNER JOIN {question_categories} qc
                    ON qc.id = qbe.questioncategoryid
            INNER JOIN {context} ctx
                    ON ctx.id = qc.contextid
                 WHERE ctx.contextlevel = :module
                   AND {$DB->sql_like('ctx.path', ':coursecontextpath')}
              ORDER BY q.id ASC";

        return $DB->get_recordset_sql($sql, $param);
    }

    /**
     * Return an array of area objects that contain content at the site and system levels only. This would be question content from
     * question categories at the system context only.
     *
     * @return \moodle_recordset
     * @deprecated since Moodle 5.0.
     * @todo MDL-82413 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(null, since: '5.0', reason: 'This method should not be used', mdl: 'MDL-71378')]
    public function find_system_areas(): ?\moodle_recordset {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
        global $DB;
        $params = [
            'syscontext' => CONTEXT_SYSTEM,
            'coursecat' => CONTEXT_COURSECAT,
            'coursecat2' => CONTEXT_COURSECAT,
        ];

        $sql = "SELECT {$this->get_type()} AS type,
                       qc.contextid AS contextid,
                       {$this->get_standard_area_fields_sql()}
                       q.id AS itemid,
                       " . SITEID . "  as courseid,
                       cc.id as categoryid,
                       q.{$this->get_fieldname()} AS content
                  FROM {question} q
            INNER JOIN {question_versions} qv
                    ON qv.questionid = q.id
            INNER JOIN {question_bank_entries} qbe
                    ON qbe.id = qv.questionbankentryid
            INNER JOIN {question_categories} qc
                    ON qc.id = qbe.questioncategoryid
            INNER JOIN {context} ctx
                    ON ctx.id = qc.contextid
             LEFT JOIN {course_categories} cc
                    ON cc.id = ctx.instanceid
                   AND ctx.contextlevel = :coursecat
                 WHERE (ctx.contextlevel = :syscontext)
                    OR (ctx.contextlevel = :coursecat2)
              ORDER BY q.id";

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Returns the moodle_url of the page to edit the error.
     *
     * @param \stdClass $componentinfo
     * @return \moodle_url
     */
    public static function get_edit_url(\stdClass $componentinfo): \moodle_url {
        $questionid = $componentinfo->itemid;
        // Question answers are editable on main question page.
        // Hence, use refid for these links.
        if ($componentinfo->tablename === 'question_answers') {
            $questionid = $componentinfo->refid;
        }
        // Default to SITEID if courseid is null, i.e. system or category level questions.
        $thiscourseid = ($componentinfo->courseid !== null) ? $componentinfo->courseid : SITEID;
        return new \moodle_url('/question/bank/editquestion/question.php', ['courseid' => $thiscourseid, 'id' => $questionid]);
    }

    /**
     * Determine the course and category id SQL depending on the specific context associated with question data.
     *
     * @param \core\event\base $event
     * @return string
     */
    protected function get_course_and_cat_sql(\core\event\base $event): string {
        $courseid = 'null';
        $catid = 'null';

        if ($record = self::get_course_and_category(CONTEXT_MODULE, $event->objectid)) {
            $courseid = $record->courseid;
        }

        return "
            {$courseid} AS courseid,
            {$catid} AS categoryid,
        ";
    }

    /**
     * Get the course and category data for the question.
     *
     * @param int $coursemodule
     * @param int $refid
     * @return \stdClass|false
     */
    public static function get_course_and_category($coursemodule, $refid) {
        global $DB;

        if ($coursemodule !== CONTEXT_MODULE) {
            debugging("Invalid contextlevel: ($coursemodule}", DEBUG_DEVELOPER);
        }

        $sql = 'SELECT ctx.instanceid,
                       cm.course as courseid,
                       ctx.contextlevel
                  FROM {question} q
            INNER JOIN {question_versions} qv
                    ON qv.questionid = q.id
            INNER JOIN {question_bank_entries} qbe
                    ON qbe.id = qv.questionbankentryid
            INNER JOIN {question_categories} qc
                    ON qc.id = qbe.questioncategoryid
            INNER JOIN {context} ctx
                    ON ctx.id = qc.contextid
            INNER JOIN {course_modules} cm
                    ON cm.id = ctx.instanceid
                   AND ctx.contextlevel = :coursemodule
                 WHERE q.id = :refid';
        $params = [
                'coursemodule' => $coursemodule,
                'refid' => $refid
        ];
        return $DB->get_record_sql($sql, $params);
    }
}

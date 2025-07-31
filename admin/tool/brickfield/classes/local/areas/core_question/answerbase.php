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

/**
 * Base class for various question-related areas.
 *
 * This is an abstract class so it will be skipped by manager when it finds all areas.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class answerbase extends base {

    /**
     * Get table name reference.
     *
     * @return string
     */
    public function get_ref_tablename(): string {
        return 'question';
    }

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
                           a.id AS itemid,
                           {$this->get_reftable_field_sql()}
                           q.id AS refid,
                           {$this->get_course_and_cat_sql($event)}
                           a.{$this->get_fieldname()} AS content
                      FROM {question} q
                INNER JOIN {question_answers} a
                        ON a.question = q.id
                INNER JOIN {question_versions} qv
                        ON qv.questionid = q.id
                INNER JOIN {question_bank_entries} qbe
                        ON qbe.id = qv.questionbankentryid
                INNER JOIN {question_categories} qc
                        ON qc.id = qbe.questioncategoryid
                INNER JOIN {context} ctx
                        ON ctx.id = qc.contextid
                     WHERE (q.id = :refid)
                  ORDER BY a.id";

            $rs = $DB->get_recordset_sql($sql, ['refid' => $event->objectid]);
            return $rs;

        }
        return null;
    }

    /**
     * Return an array of area objects that contain content at the site and system levels only. This would be question content from
     * question categories at the system context, or course category context.
     *
     * @return mixed
     * @deprecated since Moodle 5.0.
     * @todo MDL-82413 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(null, since: '5.0', reason: 'This method should not be used', mdl: 'MDL-71378')]
    public function find_system_areas(): ?\moodle_recordset {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
        global $DB;
        $params = [
            'syscontext' => CONTEXT_SYSTEM,
            'coursecat' => CONTEXT_COURSECAT,
            'coursecat2' => CONTEXT_COURSECAT,
        ];

        $sql = "SELECT {$this->get_type()} AS type,
                       qc.contextid AS contextid,
                       {$this->get_standard_area_fields_sql()}
                       a.id AS itemid,
                       {$this->get_reftable_field_sql()}
                       q.id AS refid,
                       " . SITEID . "  as courseid,
                       cc.id as categoryid,
                       a.{$this->get_fieldname()} AS content
                  FROM {question} q
            INNER JOIN {question_answers} a
                    ON a.question = q.id
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
              ORDER BY a.id";

        return $DB->get_recordset_sql($sql, $params);
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
                       a.id AS itemid,
                       {$this->get_reftable_field_sql()}
                       q.id AS refid,
                       {$courseid} AS courseid,
                       a.{$this->get_fieldname()} AS content
                  FROM {question} q
            INNER JOIN {question_answers} a
                    ON a.question = q.id
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
              ORDER BY a.id ASC";

        return $DB->get_recordset_sql($sql, $param);
    }
}

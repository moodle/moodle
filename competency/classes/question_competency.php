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
 * Class for loading/storing competencies from the DB.
 *
 * @package     core_competency
 * @author      2019 Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @author      2015 Damyon Wiese (was based on original code from Damyon Wiese)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use stdClass;
use lang_string;

/**
 * Class for loading/storing question_competencies from the DB.
 *
 * @author      2019 Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @author      2015 Damyon Wiese (was based on original code from Damyon Wiese)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_competency extends persistent {

    const TABLE = 'competency_questioncomp';

    /** Course competency ruleoutcome constant. */
    const OUTCOME_NONE = 0;
    /** Course competency ruleoutcome constant. */
    const OUTCOME_EVIDENCE = 1;
    /** Course competency ruleoutcome constant. */
    const OUTCOME_RECOMMEND = 2;
    /** Course competency ruleoutcome constant. */
    const OUTCOME_COMPLETE = 3;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'qid' => array(
                'type' => PARAM_INT
            ),
            'competencyid' => array(
                'type' => PARAM_INT
            ),
            'sortorder' => array(
                'type' => PARAM_INT
            ),
            'ruleoutcome' => array(
                'choices' => array(self::OUTCOME_NONE,
                    self::OUTCOME_EVIDENCE,
                    self::OUTCOME_RECOMMEND,
                    self::OUTCOME_COMPLETE
                ),
                'default' => self::OUTCOME_EVIDENCE,
                'type' => PARAM_INT,
            ),
        );
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        if (($this->get('id') && $this->get('sortorder') === null) || !$this->get('id')) {
            $this->set('sortorder', $this->count_records(array('qid' => $this->get('qid'))));
        }
    }

    /**
     * Return a list of rules.
     *
     * @return array Indexed by outcome value.
     */
    public static function get_ruleoutcome_list() {
        static $list = null;

        if ($list === null) {
            $list = array(
                self::OUTCOME_NONE => self::get_ruleoutcome_name(self::OUTCOME_NONE),
                self::OUTCOME_EVIDENCE => self::get_ruleoutcome_name(self::OUTCOME_EVIDENCE),
                self::OUTCOME_RECOMMEND => self::get_ruleoutcome_name(self::OUTCOME_RECOMMEND),
                self::OUTCOME_COMPLETE => self::get_ruleoutcome_name(self::OUTCOME_COMPLETE));
        }

        return $list;
    }

    /**
     * Human readable rule name.
     *
     * @param int $ruleoutcome The value of ruleoutcome.
     * @return lang_string
     */
    public static function get_ruleoutcome_name($ruleoutcome) {

        switch ($ruleoutcome) {
            case self::OUTCOME_NONE:
                $strname = 'none';
                break;
            case self::OUTCOME_EVIDENCE:
                $strname = 'evidence';
                break;
            case self::OUTCOME_RECOMMEND:
                $strname = 'recommend';
                break;
            case self::OUTCOME_COMPLETE:
                $strname = 'complete';
                break;
            default:
                throw new \moodle_exception('errorcompetencyrule', 'core_competency', '', $ruleoutcome);
                break;
        }

        return new lang_string('questioncompetencyoutcome_' . $strname, 'core_competency');
    }

    /**
     * Validate qid ID.
     *
     * @param int $data The CM ID.
     * @return true|lang_string
     */
    protected function validate_qid($data) {
        global $DB;
        if (!$DB->record_exists('question', array('id' => $data))) {
            return new lang_string('invalidquestionid', 'error');
        }
        return true;
    }

    /**
     * Validate competency ID.
     *
     * @param int $data The competency ID.
     * @return true|lang_string
     */
    protected function validate_competencyid($data) {
        if (!competency::record_exists($data)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Return the module IDs and visible flags that include this competency in a single course.
     *
     * @param int $competencyid The competency id
     * @param int $courseid The course ID.
     * @return array of ints (qids)
     */
    public static function list_questions($competencyid, $courseid) {
        global $DB;
// todo: fixme
        $results = $DB->get_records_sql('SELECT questions.id as id
                                           FROM {' . self::TABLE . '} qcomp
                                           JOIN {question} questions
                                             ON qcomp.qid = questions.id
                                          WHERE qcomp.competencyid = ? AND questions.course = ?',
                                          array($competencyid, $courseid));

        return array_keys($results);
    }

    /**
     * Count the competencies in this course module.
     *
     * @param int $qid The course module id.
     * @return int
     */
    public static function count_competencies($qid) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} questioncomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.qid = ? ';
        $params = array($qid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the competencies in this course module.
     *
     * @param int $qid The course module id
     * @return competency[] Indexed by competency ID.
     */
    public static function list_competencies($qid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} questioncomp
                    ON questioncomp.competencyid = comp.id
                 WHERE questioncomp.qid = ?
                 ORDER BY questioncomp.sortorder ASC';
        $params = array($qid);

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            $comp = new competency(0, $result);
            $instances[$comp->get('id')] = $comp;
        }
        $results->close();

        return $instances;
    }

    /**
     * Get a single competency from the course module (only if it is really in the course module).
     *
     * @param int $qid The course module id
     * @param int $competencyid The competency id
     * @return competency
     */
    public static function get_competency($qid, $competencyid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} crsqcomp
                    ON crsqcomp.competencyid = comp.id
                 WHERE crsqcomp.qid = ? AND crsqcomp.competencyid = ?';
        $params = array($qid, $competencyid);

        $result = $DB->get_record_sql($sql, $params);
        if (!$result) {
            throw new \coding_exception('The competency does not belong to this course module: ' . $competencyid . ', ' . $qid);
        }

        return new competency(0, $result);
    }

    /**
     * Hook to execute after delete.
     *
     * @param bool $result Whether or not the delete was successful.
     * @return void
     */
    protected function after_delete($result) {
        global $DB;
        if (!$result) {
            return;
        }

        $table = '{' . self::TABLE . '}';
        $sql = "UPDATE $table SET sortorder = sortorder -1 WHERE qid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get('qid'), $this->get('sortorder')));
    }

    /**
     * List the question_competencies in this course module.
     *
     * @param int $qid The course module id
     * @return question_competency[]
     */
    public static function list_question_competencies($qid) {
        global $DB;

        $sql = 'SELECT courseqcomp.*
                  FROM {' . self::TABLE . '} courseqcomp
                  JOIN {' . competency::TABLE . '} comp
                    ON courseqcomp.competencyid = comp.id
                 WHERE courseqcomp.qid = ?
                 ORDER BY courseqcomp.sortorder ASC';
        $params = array($qid);

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new question_competency(0, $result));
        }
        $results->close();

        return $instances;
    }

    /**
     * List the relationship objects for a competency in a course.
     *
     * @param int $competencyid The competency ID.
     * @param int $courseid The course ID.
     * @return question_competency[]
     */
    public static function get_records_by_competencyid_in_course($competencyid, $courseid) {
        global $DB;
//todo: fix me
        $sql = 'SELECT cmc.*
                  FROM {' . self::TABLE . '} cmc
                  JOIN {course_module} cm
                    ON cm.course = ?
                   AND cmc.qid = cm.id
                 WHERE cmc.competencyid = ?
              ORDER BY cmc.sortorder ASC';
        $params = array($courseid, $competencyid);

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            $instances[$result->id] = new question_competency(0, $result);
        }
        $results->close();

        return $instances;
    }

    public static function update_question_competencies($question, $fromform) {
        $existing = question_competency::list_question_competencies($question->id);

        $existingids = array();
        foreach ($existing as $cmc) {
            $existingids[] = $cmc->get('competencyid');
        }

        $newids = isset($fromform->competencies) ? $fromform->competencies : array();

        $removed = array_diff($existingids, $newids);
        $added = array_diff($newids, $existingids);

        foreach ($removed as $removedid) {
            api::remove_competency_from_question($question->id, $removedid);
        }
        foreach ($added as $addedid) {
            api::add_competency_to_question($question->id, $addedid);
        }

        if (isset($fromform->competency_rule)) {
            // Now update the rules for each course_module_competency.
            $current = api::list_question_competencies_in_question($question->id);
            foreach ($current as $questioncompetency) {
                api::set_question_competency_ruleoutcome($questioncompetency, $fromform->competency_rule);
            }
        }
    }

}

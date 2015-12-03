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
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use stdClass;
use lang_string;

/**
 * Class for loading/storing course_competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency extends persistent {

    const TABLE = 'tool_lp_course_competency';

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
            'courseid' => array(
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
        if (($this->get_id() && $this->get_sortorder() === null) || !$this->get_id()) {
            $this->set('sortorder', $this->count_records(array('courseid' => $this->get_courseid())));
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
                throw new \moodle_exception('errorcoursecompetencyrule', 'tool_lp', '', $rule);
                break;
        }

        return new lang_string('coursecompetencyoutcome_' . $strname, 'tool_lp');
    }

    /**
     * Validate course ID.
     *
     * @return true|lang_string
     */
    protected function validate_courseid($data) {
        global $DB;
        if (!$DB->record_exists('course', array('id' => $data))) {
            return new lang_string('invalidcourseid', 'error');
        }
        return true;
    }

    /**
     * Validate competency ID.
     *
     * @return true|lang_string
     */
    protected function validate_competencyid($data) {
        if (!competency::record_exists($data)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Return the course IDs and visible flags that include this competency.
     *
     * Only the ids and visible flag are returned, for the full records use list_courses.
     *
     * @param int $competencyid The competency id
     * @return array containing courseid and visible.
     */
    public static function list_courses_min($competencyid) {
        global $DB;

        $results = $DB->get_records_sql('SELECT course.id as id, course.visible as visible
                                           FROM {' . self::TABLE . '} coursecomp
                                           JOIN {course} course
                                             ON coursecomp.courseid = course.id
                                          WHERE coursecomp.competencyid = ? ', array($competencyid));

        return $results;
    }

    /**
     * Return partial course records foreach course that contains this competency.
     *
     * @param int $competencyid The competency id
     * @return array[stdClass] Array of course records containg id, visible, shortname, idnumber, fullname
     */
    public static function list_courses($competencyid) {
        global $DB;

        $results = $DB->get_records_sql('SELECT course.id, course.visible, course.shortname, course.idnumber, course.fullname
                                           FROM {course} course
                                           JOIN {' . self::TABLE . '} coursecomp
                                             ON coursecomp.courseid = course.id
                                          WHERE coursecomp.competencyid = ? ', array($competencyid));

        return $results;
    }

    /**
     * Count the competencies in this course.
     *
     * @param int $courseid The course id
     * @param bool $onlyvisible If true, only count visible competencies in this course.
     * @return int
     */
    public static function count_competencies($courseid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} coursecomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ? ';
        $params = array($courseid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the competencies in this course.
     *
     * @param int $courseid The course id
     * @param bool $onlyvisible If true, only count visible competencies in this course.
     * @return competency[] Indexed by competency ID.
     */
    public static function list_competencies($courseid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} coursecomp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ?';
        $params = array($courseid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $sql .= ' ORDER BY coursecomp.sortorder ASC';
        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            $comp = new competency(0, $result);
            $instances[$comp->get_id()] = $comp;
        }
        $results->close();

        return $instances;
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
        $sql = "UPDATE $table SET sortorder = sortorder -1  WHERE courseid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get_courseid(), $this->get_sortorder()));
    }

    /**
     * List the course_competencies in this course.
     *
     * @param int $courseid The course id
     * @param bool $onlyvisible If true, only count visible competencies in this course.
     * @return course_competency[]
     */
    public static function list_course_competencies($courseid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT coursecomp.*
                  FROM {' . self::TABLE . '} coursecomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ?';
        $params = array($courseid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $sql .= ' ORDER BY coursecomp.sortorder ASC';
        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new course_competency(0, $result));
        }
        $results->close();

        return $instances;
    }

}

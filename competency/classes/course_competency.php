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
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;

use coding_exception;
use lang_string;
use core_course\external\course_summary_exporter;

/**
 * Class for loading/storing course_competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency extends persistent {

    const TABLE = 'competency_coursecomp';

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
        if (($this->get('id') && $this->get('sortorder') === null) || !$this->get('id')) {
            $this->set('sortorder', $this->count_records(array('courseid' => $this->get('courseid'))));
        }
    }

    /**
     * Return the courses where both competency and user are.
     *
     * A user is considered being in a course when they are enrolled, the enrolment is valid,
     * the enrolment instance is enabled, and the enrolment plugin is enabled..
     *
     * @param int $competencyid The competency ID.
     * @param int $userid The user ID.
     * @return array Indexed by course ID.
     */
    public static function get_courses_with_competency_and_user($competencyid, $userid) {
        global $CFG, $DB;

        if (!$plugins = explode(',', $CFG->enrol_plugins_enabled)) {
            return array();
        }

        $ctxfields = \context_helper::get_preload_record_columns_sql('ctx');
        list($plugins, $params) = $DB->get_in_or_equal($plugins, SQL_PARAMS_NAMED, 'ee');
        $params['competencyid'] = $competencyid;
        $params['userid'] = $userid;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
        $params['active'] = ENROL_USER_ACTIVE;
        $params['contextlevel'] = CONTEXT_COURSE;

        // Heavily based on enrol_get_shared_courses().
        $sql = "SELECT c.*, $ctxfields
                  FROM {course} c
                  JOIN {" . static::TABLE . "} cc
                    ON cc.courseid = c.id
                   AND cc.competencyid = :competencyid
                  JOIN (
                    SELECT DISTINCT c.id
                      FROM {enrol} e
                      JOIN {user_enrolments} ue
                        ON ue.enrolid = e.id
                       AND ue.status = :active
                       AND ue.userid = :userid
                      JOIN {course} c
                        ON c.id = e.courseid
                     WHERE e.status = :enabled
                       AND e.enrol $plugins
                  ) ec ON ec.id = c.id
             LEFT JOIN {context} ctx
                    ON ctx.instanceid = c.id
                   AND ctx.contextlevel = :contextlevel
              ORDER BY c.id";

        $courses = $DB->get_records_sql($sql, $params);
        array_map('context_helper::preload_from_record', $courses);
        return $courses;
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
                throw new \moodle_exception('errorcoursecompetencyrule', 'core_competency', '', $ruleoutcome);
                break;
        }

        return new lang_string('coursecompetencyoutcome_' . $strname, 'core_competency');
    }

    /**
     * Validate course ID.
     *
     * @param int $data The course ID.
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

        // We need all the course summary exporter properties, plus category.
        $coursefields = course_summary_exporter::properties_definition();
        $coursefields = array_map(function(string $field): string {
            return "course.{$field}";
        }, array_keys($coursefields));

        $results = $DB->get_records_sql('SELECT ' . implode(',', $coursefields) . ', course.category
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
     * @return int
     */
    public static function count_competencies($courseid) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} coursecomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ? ';
        $params = array($courseid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the competencies in this course.
     *
     * @param int $courseid The course id
     * @return competency[] Indexed by competency ID.
     */
    public static function list_competencies($courseid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} coursecomp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ?';
        $params = array($courseid);

        $sql .= ' ORDER BY coursecomp.sortorder ASC';
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
     * Get a single competency from the course (only if it is really in the course).
     *
     * @param int $courseid The course id
     * @param int $competencyid The competency id
     * @return competency
     */
    public static function get_competency($courseid, $competencyid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} crscomp
                    ON crscomp.competencyid = comp.id
                 WHERE crscomp.courseid = ? AND crscomp.competencyid = ?';
        $params = array($courseid, $competencyid);

        $result = $DB->get_record_sql($sql, $params);
        if (!$result) {
            throw new coding_exception('The competency does not belong to this course: ' . $competencyid . ', ' . $courseid);
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
        $sql = "UPDATE $table SET sortorder = sortorder -1  WHERE courseid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get('courseid'), $this->get('sortorder')));
    }

    /**
     * Get the specified course_competency in this course.
     *
     * @param int $courseid The course id
     * @param int $competencyid The competency id
     * @return course_competency
     */
    public static function get_course_competency($courseid, $competencyid) {
        global $DB;

        $sql = 'SELECT crscomp.*
                  FROM {' . self::TABLE . '} crscomp
                 WHERE crscomp.courseid = ? AND crscomp.competencyid = ?';
        $params = array($courseid, $competencyid);

        $result = $DB->get_record_sql($sql, $params);
        if (!$result) {
            throw new coding_exception('The competency does not belong to this course: ' . $competencyid . ', ' . $courseid);
        }

        return new course_competency(0, $result);
    }

    /**
     * List the course_competencies in this course.
     *
     * @param int $courseid The course id
     * @return course_competency[]
     */
    public static function list_course_competencies($courseid) {
        global $DB;

        $sql = 'SELECT coursecomp.*
                  FROM {' . self::TABLE . '} coursecomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ?';
        $params = array($courseid);

        $sql .= ' ORDER BY coursecomp.sortorder ASC';
        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new course_competency(0, $result));
        }
        $results->close();

        return $instances;
    }

    /**
     * Check if course competency has records for competencies.
     *
     * @param array $competencyids Array of competencies ids.
     * @return boolean Return true if one or more than a competency was found in a course.
     */
    public static function has_records_for_competencies($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);
        return self::record_exists_select("competencyid $insql", $params);
    }

}

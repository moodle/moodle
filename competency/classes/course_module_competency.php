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
defined('MOODLE_INTERNAL') || die();

use stdClass;
use lang_string;

/**
 * Class for loading/storing course_module_competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_competency extends persistent {

    const TABLE = 'competency_modulecomp';

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
            'cmid' => array(
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
            $this->set('sortorder', $this->count_records(array('cmid' => $this->get('cmid'))));
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

        return new lang_string('coursemodulecompetencyoutcome_' . $strname, 'core_competency');
    }

    /**
     * Validate cmid ID.
     *
     * @param int $data The CM ID.
     * @return true|lang_string
     */
    protected function validate_cmid($data) {
        global $DB;
        if (!$DB->record_exists('course_modules', array('id' => $data))) {
            return new lang_string('invalidmodule', 'error');
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
     * @return array of ints (cmids)
     */
    public static function list_course_modules($competencyid, $courseid) {
        global $DB;

        $results = $DB->get_records_sql('SELECT coursemodules.id as id
                                           FROM {' . self::TABLE . '} modcomp
                                           JOIN {course_modules} coursemodules
                                             ON modcomp.cmid = coursemodules.id
                                          WHERE modcomp.competencyid = ? AND coursemodules.course = ?',
                                          array($competencyid, $courseid));

        return array_keys($results);
    }

    /**
     * Count the competencies in this course module.
     *
     * @param int $cmid The course module id.
     * @return int
     */
    public static function count_competencies($cmid) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} coursemodulecomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.cmid = ? ';
        $params = array($cmid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the competencies in this course module.
     *
     * @param int $cmid The course module id
     * @return competency[] Indexed by competency ID.
     */
    public static function list_competencies($cmid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} coursemodulecomp
                    ON coursemodulecomp.competencyid = comp.id
                 WHERE coursemodulecomp.cmid = ?
                 ORDER BY coursemodulecomp.sortorder ASC';
        $params = array($cmid);

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
     * @param int $cmid The course module id
     * @param int $competencyid The competency id
     * @return competency
     */
    public static function get_competency($cmid, $competencyid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} crsmodcomp
                    ON crsmodcomp.competencyid = comp.id
                 WHERE crsmodcomp.cmid = ? AND crsmodcomp.competencyid = ?';
        $params = array($cmid, $competencyid);

        $result = $DB->get_record_sql($sql, $params);
        if (!$result) {
            throw new \coding_exception('The competency does not belong to this course module: ' . $competencyid . ', ' . $cmid);
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
        $sql = "UPDATE $table SET sortorder = sortorder -1 WHERE cmid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get('cmid'), $this->get('sortorder')));
    }

    /**
     * List the course_module_competencies in this course module.
     *
     * @param int $cmid The course module id
     * @return course_module_competency[]
     */
    public static function list_course_module_competencies($cmid) {
        global $DB;

        $sql = 'SELECT coursemodcomp.*
                  FROM {' . self::TABLE . '} coursemodcomp
                  JOIN {' . competency::TABLE . '} comp
                    ON coursemodcomp.competencyid = comp.id
                 WHERE coursemodcomp.cmid = ?
                 ORDER BY coursemodcomp.sortorder ASC';
        $params = array($cmid);

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new course_module_competency(0, $result));
        }
        $results->close();

        return $instances;
    }

    /**
     * List the relationship objects for a competency in a course.
     *
     * @param int $competencyid The competency ID.
     * @param int $courseid The course ID.
     * @return course_module_competency[]
     */
    public static function get_records_by_competencyid_in_course($competencyid, $courseid) {
        global $DB;

        $sql = 'SELECT cmc.*
                  FROM {' . self::TABLE . '} cmc
                  JOIN {course_modules} cm
                    ON cm.course = ?
                   AND cmc.cmid = cm.id
                 WHERE cmc.competencyid = ?
              ORDER BY cmc.sortorder ASC';
        $params = array($courseid, $competencyid);

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            $instances[$result->id] = new course_module_competency(0, $result);
        }
        $results->close();

        return $instances;
    }

}

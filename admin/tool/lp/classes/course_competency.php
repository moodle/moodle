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

/**
 * Class for loading/storing course_competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency extends persistent {

    const TABLE = 'tool_lp_course_competency';

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
        );
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        // During create.
        if (!$this->get_id()) {
            if ($this->get_sortorder() === null) {
                // Get a sortorder if it wasn't set.
                $this->set('sortorder', $this->count_records(array('courseid' => $this->get_courseid())));
            }
        }
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
     * @return competency[]
     */
    public static function list_competencies($courseid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} coursecomp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ?
              ORDER BY coursecomp.sortorder ASC';
        $params = array($courseid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }
        $results->close();

        return $instances;
    }

}

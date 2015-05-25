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

    /** @var int $courseid The course id */
    private $courseid = 0;

    /** @var int $competencyid The competency id */
    private $competencyid = 0;

    /** @var int $sortorder A number used to influence sorting */
    private $sortorder = 0;

    /**
     * Method that provides the table name matching this class.
     *
     * @return string
     */
    public function get_table_name() {
        return 'tool_lp_course_competency';
    }

    /**
     * Get the competency id
     *
     * @return int The competency id
     */
    public function get_competencyid() {
        return $this->competencyid;
    }

    /**
     * Set the competency id
     *
     * @param int $competencyid The competency id
     */
    public function set_competencyid($competencyid) {
        $this->competencyid = $competencyid;
    }

    /**
     * Get the sort order index.
     *
     * @return string The sort order index
     */
    public function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * Set the sort order index.
     *
     * @param string $sortorder The sort order index
     */
    public function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
    }

    /**
     * Get the course id
     *
     * @return int The course id
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Set the course id
     *
     * @param int $courseid The course id
     */
    public function set_courseid($courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param stdClass $record A DB record.
     * @return course_competency
     */
    public function from_record($record) {
        if (isset($record->id)) {
            $this->set_id($record->id);
        }
        if (isset($record->courseid)) {
            $this->set_courseid($record->courseid);
        }
        if (isset($record->competencyid)) {
            $this->set_competencyid($record->competencyid);
        }
        if (isset($record->sortorder)) {
            $this->set_sortorder($record->sortorder);
        }
        if (isset($record->timecreated)) {
            $this->set_timecreated($record->timecreated);
        }
        if (isset($record->timemodified)) {
            $this->set_timemodified($record->timemodified);
        }
        if (isset($record->usermodified)) {
            $this->set_usermodified($record->usermodified);
        }
        return $this;
    }

    /**
     * Create a DB record from this class.
     *
     * @return stdClass
     */
    public function to_record() {
        $record = new stdClass();
        $record->id = $this->get_id();
        $record->courseid = $this->get_courseid();
        $record->competencyid = $this->get_competencyid();
        $record->sortorder = $this->get_sortorder();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();

        return $record;
    }

    /**
     * Return the course ids and visible flags that include this competency. Only the ids and visible flag are returned,
     * for the full records use list_courses.
     *
     * @param int $competencyid The competency id
     * @return int
     */
    public function list_courses_min($competencyid) {
        global $DB;

        $results = $DB->get_records_sql('SELECT course.id as id, course.visible as visible
                                           FROM {' . $this->get_table_name() . '} coursecomp
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
    public function list_courses($competencyid) {
        global $DB;

        $results = $DB->get_records_sql('SELECT course.id, course.visible, course.shortname, course.idnumber, course.fullname
                                           FROM {course} course
                                           JOIN {' . $this->get_table_name() . '} coursecomp
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
    public function count_competencies($courseid, $onlyvisible) {
        global $DB;

        $competency = new competency();
        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . $this->get_table_name() . '} coursecomp
                  JOIN {' . $competency->get_table_name() . '} comp
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
     * @return array[competency]
     */
    public function list_competencies($courseid, $onlyvisible) {
        global $DB;

        $competency = new competency();
        $sql = 'SELECT comp.*
                  FROM {' . $competency->get_table_name() . '} comp
                  JOIN {' . $this->get_table_name() . '} coursecomp
                    ON coursecomp.competencyid = comp.id
                 WHERE coursecomp.courseid = ?
              ORDER BY coursecomp.sortorder ASC';
        $params = array($courseid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }

        return $instances;
    }

    /**
     * Add a default for the sortorder field to the default create logic.
     *
     * @return persistent
     */
    public function create() {
        $this->sortorder = $this->count_records(array('courseid' => $this->get_courseid()));
        return parent::create();
    }

}

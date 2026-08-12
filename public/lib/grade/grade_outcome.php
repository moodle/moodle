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
 * Definition of grade outcome class
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('grade_object.php');

/**
 * Class representing a grade outcome.
 *
 * It is responsible for handling its DB representation, modifying and returning its metadata.
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_outcome extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    public $table = 'grade_outcomes';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'courseid', 'shortname', 'fullname', 'scaleid','description',
                                 'descriptionformat', 'timecreated', 'timemodified', 'usermodified');

    /**
     * The course this outcome belongs to.
     * @var int $courseid
     */
    public $courseid;

    /**
     * The shortname of the outcome.
     * @var string $shortname
     */
    public $shortname;

    /**
     * The fullname of the outcome.
     * @var string $fullname
     */
    public $fullname;

    /**
     * A full grade_scale object referenced by $this->scaleid.
     * @var object $scale
     */
    public $scale;

    /**
     * The id of the scale referenced by this outcome, or null if this outcome has no scale.
     * @var int|null $scaleid
     */
    public $scaleid;

    /**
     * The description of this outcome - FORMAT_MOODLE.
     * @var string $description
     */
    public $description;

    /**
     * The userid of the person who last modified this outcome.
     *
     * @var int $usermodified
     */
    public $usermodified;

    /** @var int Identifier of the text format to be used. */
    public $descriptionformat = FORMAT_MOODLE;

    /**
     * Deletes this outcome from the database.
     *
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function delete($source=null) {
        global $DB;
        $gocids = $DB->get_fieldset_select('grade_outcomes_courses', 'id', 'outcomeid = ?', [$this->id]);
        if (!empty($gocids)) {
            [$insql, $inparams] = $DB->get_in_or_equal($gocids);
            $DB->delete_records_select('grade_outcomes_modules', "outcomecourseid $insql", $inparams);
        }
        if (!empty($this->courseid)) {
            $DB->delete_records('grade_outcomes_courses', array('outcomeid' => $this->id, 'courseid' => $this->courseid));
        }
        if (parent::delete($source)) {
            $context = context_system::instance();
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'grade', 'outcome', $this->id);
            foreach ($files as $file) {
                $file->delete();
            }
            return true;
        }
        return false;
    }

    /**
     * Records this scale-less outcome as used by a course module in grade_outcomes_modules.
     *
     * @param int $courseid The course ID.
     * @param int $cmid The course-module ID.
     * @return bool True on success, false if the outcome has a scale.
     * @throws \dml_exception If any database errors are encountered.
     */
    public function add_outcome_to_module(int $courseid, int $cmid): bool {
        global $DB, $USER;

        if (!empty($this->scaleid)) {
            // Scaled outcomes are tracked via grade_items; do not use this table.
            return false;
        }

        // Ensure the outcome is registered in this course (creates a grade_outcomes_courses row if
        // needed) and retrieve its id for use as the FK in grade_outcomes_modules.
        $this->use_in($courseid);
        $goc = $DB->get_record('grade_outcomes_courses', ['courseid' => $courseid, 'outcomeid' => $this->id], 'id', MUST_EXIST);

        if ($DB->record_exists('grade_outcomes_modules', ['outcomecourseid' => $goc->id, 'cmid' => $cmid])) {
            return true; // Already associated.
        }

        $record                  = new stdClass();
        $record->outcomecourseid = $goc->id;
        $record->cmid            = $cmid;
        $record->usercreated     = $USER->id;
        $record->timecreated     = time();
        $DB->insert_record('grade_outcomes_modules', $record);

        return true;
    }

    /**
     * Removes this scale-less outcome's record for a course module from grade_outcomes_modules.
     *
     * @param int $courseid The course ID.
     * @param int $cmid The course-module ID.
     */
    public function remove_outcome_from_module(int $courseid, int $cmid): void {
        global $DB;

        $goc = $DB->get_record(
            'grade_outcomes_courses',
            ['courseid' => $courseid, 'outcomeid' => $this->id],
            'id',
            IGNORE_MISSING
        );

        if (!$goc) {
            return;
        }

        $DB->delete_records('grade_outcomes_modules', [
            'outcomecourseid' => $goc->id,
            'cmid' => $cmid,
        ]);
    }

    /**
     * Returns the IDs of all scale-less outcomes associated with a course module.
     *
     * @param int $cmid Course module ID.
     * @param int $courseid Course ID.
     * @return int[] List of outcome IDs.
     */
    public static function get_outcomes_in_module(int $cmid, int $courseid): array {
        global $DB;

        $sql = "SELECT goc.outcomeid
                  FROM {grade_outcomes_modules} gom
                  JOIN {grade_outcomes_courses} goc ON goc.id = gom.outcomecourseid
                 WHERE gom.cmid = :cmid AND goc.courseid = :courseid";
        $ids = $DB->get_fieldset_sql($sql, ['cmid' => $cmid, 'courseid' => $courseid]);
        return array_map('intval', $ids);
    }

    /**
     * Returns all course module IDs mapped to a course outcome.
     *
     * Scaled outcomes are mapped via grade_items, while scale-less outcomes are mapped via
     * grade_outcomes_modules.
     *
     * @param int $outcomeid Outcome ID.
     * @param int $courseid Course ID.
     * @return int[] List of unique course module IDs.
     */
    public static function get_modules_mapped_to_course_outcomes(int $outcomeid, int $courseid): array {
        global $DB;

        $scaledsql = "SELECT cm.id
                        FROM {grade_items} gi
                        JOIN {modules} mo ON mo.name = gi.itemmodule
                        JOIN {course_modules} cm ON cm.module = mo.id
                         AND cm.instance = gi.iteminstance
                         AND cm.course = gi.courseid
                       WHERE gi.courseid = :courseid
                         AND gi.itemtype = 'mod'
                         AND gi.outcomeid = :outcomeid";
        $scaledcmids = $DB->get_fieldset_sql($scaledsql, ['courseid' => $courseid, 'outcomeid' => $outcomeid]);

        $scalelesssql = "SELECT gom.cmid
                           FROM {grade_outcomes_modules} gom
                           JOIN {grade_outcomes_courses} goc ON goc.id = gom.outcomecourseid
                          WHERE goc.courseid = :courseid
                            AND goc.outcomeid = :outcomeid";
        $scalelesscmids = $DB->get_fieldset_sql($scalelesssql, ['courseid' => $courseid, 'outcomeid' => $outcomeid]);

        $cmids = array_merge($scaledcmids, $scalelesscmids);
        return array_map('intval', array_values(array_unique($cmids)));
    }

    /**
     * Records this object in the Database, sets its id to the returned value, and returns that value.
     * If successful this function also fetches the new object data from database and stores it
     * in object properties.
     *
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @param bool $isbulkupdate If bulk grade update is happening.
     * @return int PK ID if successful, false otherwise
     */
    public function insert($source = null, $isbulkupdate = false) {
        global $DB;

        $this->timecreated = $this->timemodified = time();
        if (empty($this->scaleid)) {
            $this->scaleid = null;
        }

        if ($result = parent::insert($source)) {
            if (!empty($this->courseid)) {
                $goc = new stdClass();
                $goc->courseid = $this->courseid;
                $goc->outcomeid = $this->id;
                $DB->insert_record('grade_outcomes_courses', $goc);
            }
        }
        return $result;
    }

    /**
     * In addition to update() it also updates grade_outcomes_courses if needed
     *
     * @param string $source from where was the object inserted
     * @param bool $isbulkupdate If bulk grade update is happening.
     * @return bool success
     */
    public function update($source = null, $isbulkupdate = false) {
        $this->timemodified = time();
        if (empty($this->scaleid)) {
            $this->scaleid = null;
        }

        if ($result = parent::update($source)) {
            if (!empty($this->courseid)) {
                $this->use_in($this->courseid);
            }
        }
        return $result;
    }

    /**
     * Mark outcome as used in a course
     *
     * @param int $courseid
     * @return False if invalid courseid requested
     */
    public function use_in($courseid) {
        global $DB;
        if (!empty($this->courseid) and $courseid != $this->courseid) {
            return false;
        }

        if (!$DB->record_exists('grade_outcomes_courses', array('courseid' => $courseid, 'outcomeid' => $this->id))) {
            $goc = new stdClass();
            $goc->courseid  = $courseid;
            $goc->outcomeid = $this->id;
            $DB->insert_record('grade_outcomes_courses', $goc);
        }
        return true;
    }

    /**
     * Finds and returns a grade_outcome instance based on params.
     *
     * @static
     * @param array $params associative arrays varname=>value
     * @return object grade_outcome instance or false if none found.
     */
    public static function fetch($params) {
        return grade_object::fetch_helper('grade_outcomes', 'grade_outcome', $params);
    }

    /**
     * Finds and returns all grade_outcome instances based on params.
     *
     * @static
     * @param array $params associative arrays varname=>value
     * @return array array of grade_outcome insatnces or false if none found.
     */
    public static function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_outcomes', 'grade_outcome', $params);
    }

    /**
     * Instantiates a grade_scale object whose data is retrieved from the database
     *
     * @return grade_scale
     */
    public function load_scale() {
        if (empty($this->scale->id) or $this->scale->id != $this->scaleid) {
            $this->scale = grade_scale::fetch(array('id'=>$this->scaleid));
            if ($this->scale) {
                $this->scale->load_items();
            }
        }
        return $this->scale;
    }

    /**
     * Finds and returns all used outcome IDs in a course.
     *
     * @param int $courseid The course ID.
     * @return int[] Flat array of outcome IDs in use (unique, values only).
     */
    public static function get_used_outcomes_in_course(int $courseid): array {
        global $DB;

        $scaled = $DB->get_fieldset_sql(
            "SELECT outcomeid
               FROM {grade_items}
              WHERE courseid = ?
                    AND outcomeid IS NOT NULL",
            [$courseid]
        );

        $scaleless = $DB->get_fieldset_sql(
            "SELECT goc.outcomeid
               FROM {grade_outcomes_modules} gom
               JOIN {grade_outcomes_courses} goc ON goc.id = gom.outcomecourseid
              WHERE goc.courseid = ?",
            [$courseid]
        );

        $ids = array_merge($scaled, $scaleless);

        return array_map('intval', array_unique($ids));
    }

    /**
     * Static function returning all global outcomes
     *
     * @static
     * @return array
     */
    public static function fetch_all_global() {
        if (!$outcomes = grade_outcome::fetch_all(array('courseid'=>null))) {
            $outcomes = array();
        }
        return $outcomes;
    }

    /**
     * Static function returning all local course outcomes
     *
     * @static
     * @param int $courseid
     * @return array
     */
    public static function fetch_all_local($courseid) {
        if (!$outcomes =grade_outcome::fetch_all(array('courseid'=>$courseid))) {
            $outcomes = array();
        }
        return $outcomes;
    }

    /**
     * Static method that returns all outcomes available in course
     *
     * @static
     * @param int $courseid
     * @return array
     */
    public static function fetch_all_available($courseid) {
        global $CFG, $DB;

        $result = array();
        $params = array($courseid);
        $sql = "SELECT go.*
                  FROM {grade_outcomes} go, {grade_outcomes_courses} goc
                 WHERE go.id = goc.outcomeid AND goc.courseid = ?
              ORDER BY go.id ASC";

        if ($datas = $DB->get_records_sql($sql, $params)) {
            foreach($datas as $data) {
                $instance = new grade_outcome();
                grade_object::set_properties($instance, $data);
                $result[$instance->id] = $instance;
            }
        }
        return $result;
    }


    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     *
     * @return string name
     */
    public function get_name() {
        // Grade outcomes can be created at site or course context, so set the filter context appropriately.
        $context = empty($this->courseid) ? context_system::instance() : context_course::instance($this->courseid);
        return format_string($this->fullname, false, ["context" => $context]);
    }

    /**
     * Returns unique outcome short name.
     *
     * @return string name
     */
    public function get_shortname() {
        return $this->shortname;
    }

    /**
     * Returns the formatted grade description with URLs converted
     *
     * @return string
     */
    public function get_description() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $options = new stdClass;
        $options->noclean = true;
        $systemcontext = context_system::instance();
        $description = file_rewrite_pluginfile_urls($this->description, 'pluginfile.php', $systemcontext->id, 'grade', 'outcome', $this->id);
        return format_text($description, $this->descriptionformat, $options);
    }

    /**
     * Checks if outcome can be deleted.
     *
     * @return bool
     */
    public function can_delete() {
        if ($this->get_item_uses_count()) {
            return false;
        }
        if (empty($this->courseid)) {
            if ($this->get_course_uses_count()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the number of places where outcome is used.
     *
     * @return int
     */
    public function get_course_uses_count() {
        global $DB;

        if (!empty($this->courseid)) {
            return 1;
        }

        return $DB->count_records('grade_outcomes_courses', array('outcomeid' => $this->id));
    }

    /**
     * Returns the number of activities that use this outcome.
     *
     * @return int
     */
    public function get_item_uses_count() {
        global $DB;

        if (!empty($this->scaleid)) {
            // Scaled outcomes are tracked through grade items.
            return $DB->count_records('grade_items', ['outcomeid' => $this->id]);
        }

        // Scale-less outcomes are tracked through outcome-module associations.
        $sql = "SELECT COUNT(gom.id)
                  FROM {grade_outcomes_modules} gom
                  JOIN {grade_outcomes_courses} goc ON goc.id = gom.outcomecourseid
                 WHERE goc.outcomeid = :outcomeid";
        return $DB->count_records_sql($sql, ['outcomeid' => $this->id]);
    }

    /**
     * Computes then returns extra information about this outcome and other objects that are linked to it.
     * The average of all grades that use this outcome, for all courses (or 1 course if courseid is given) can
     * be requested, and is returned as a float if requested alone. If the list of items that use this outcome
     * is also requested, then a single array is returned, which contains the grade_items AND the average grade
     * if such is still requested (array('items' => array(...), 'avg' => 2.30)). This combining of two
     * methods into one is to save on DB queries, since both queries are similar and can be performed together.
     *
     * @param int $courseid An optional courseid to narrow down the average to 1 course only
     * @param bool $average Whether or not to return the average grade for this outcome
     * @param bool $items Whether or not to return the list of items using this outcome
     * @return float
     */
    public function get_grade_info($courseid=null, $average=true, $items=false) {
        global $CFG, $DB;

        if (!isset($this->id)) {
            debugging("You must setup the outcome's id before calling its get_grade_info() method!");
            return false; // id must be defined for this to work
        }

        if ($average === false && $items === false) {
            debugging('Either the 1st or 2nd param of grade_outcome::get_grade_info() must be true, or both, but not both false!');
            return false;
        }

        $params = array($this->id);

        $wheresql = '';
        if (!is_null($courseid)) {
            $wheresql = " AND {grade_items}.courseid = ? ";
            $params[] = $courseid;
        }

        $selectadd = '';
        if ($items !== false) {
            $selectadd = ", {grade_items}.* ";
        }

        $sql = "SELECT finalgrade $selectadd
                  FROM {grade_grades}, {grade_items}, {grade_outcomes}
                 WHERE {grade_outcomes}.id = {grade_items}.outcomeid
                   AND {grade_items}.id = {grade_grades}.itemid
                   AND {grade_outcomes}.id = ?
                   $wheresql";

        $grades = $DB->get_records_sql($sql, $params);
        $retval = array();

        if ($average !== false && count($grades) > 0) {
            $count = 0;
            $total = 0;

            foreach ($grades as $k => $grade) {
                // Skip null finalgrades
                if (!is_null($grade->finalgrade)) {
                    $total += $grade->finalgrade;
                    $count++;
                }
                unset($grades[$k]->finalgrade);
            }

            $retval['avg'] = $total / $count;
        }

        if ($items !== false) {
            foreach ($grades as $grade) {
                $retval['items'][$grade->id] = new grade_item($grade);
            }
        }

        return $retval;
    }
}

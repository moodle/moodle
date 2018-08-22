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
 * Definition of grade scale class
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('grade_object.php');

/**
 * Class representing a grade scale.
 *
 * It is responsible for handling its DB representation, modifying and returning its metadata.
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_scale extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    public $table = 'scale';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'courseid', 'userid', 'name', 'scale', 'description', 'descriptionformat', 'timemodified');

    /**
     * The course this scale belongs to.
     * @var int $courseid
     */
    public $courseid;

    /**
     * The ID of the user who created the scale
     * @var int $userid
     */
    public $userid;

    /**
     * The name of the scale.
     * @var string $name
     */
    public $name;

    /**
     * The items in this scale.
     * @var array $scale_items
     */
    public $scale_items = array();

    /**
     * A string representation of the scale items (a comma-separated list).
     * @var string $scale
     */
    public $scale;

    /**
     * A description for this scale.
     * @var string $description
     */
    public $description;

    /**
     * Finds and returns a grade_scale instance based on params.
     *
     * @static
     * @param array $params associative arrays varname=>value
     * @return object grade_scale instance or false if none found.
     */
    public static function fetch($params) {
        return grade_object::fetch_helper('scale', 'grade_scale', $params);
    }

    /**
     * Finds and returns all grade_scale instances based on params.
     *
     * @static
     * @param array $params associative arrays varname=>value
     * @return array array of grade_scale instances or false if none found.
     */
    public static function fetch_all($params) {
        return grade_object::fetch_all_helper('scale', 'grade_scale', $params);
    }

    /**
     * Records this object in the Database, sets its id to the returned value, and returns that value.
     * If successful this function also fetches the new object data from database and stores it
     * in object properties.
     *
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    public function insert($source=null) {
        $this->timecreated = time();
        $this->timemodified = time();
        return parent::insert($source);
    }

    /**
     * In addition to update() it also updates grade_outcomes_courses if needed
     *
     * @param string $source from where was the object inserted
     * @return bool success
     */
    public function update($source=null) {
        $this->timemodified = time();
        return parent::update($source);
    }

    /**
     * Deletes this outcome from the database.
     *
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function delete($source=null) {
        global $DB;
        if (parent::delete($source)) {
            $context = context_system::instance();
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'grade', 'scale', $this->id);
            foreach ($files as $file) {
                $file->delete();
            }
            return true;
        }
        return false;
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     *
     * @return string name
     */
    public function get_name() {
        // Grade scales can be created at site or course context, so set the filter context appropriately.
        $context = empty($this->courseid) ? context_system::instance() : context_course::instance($this->courseid);
        return format_string($this->name, false, ['context' => $context]);
    }

    /**
     * Loads the scale's items into the $scale_items array.
     * There are three ways to achieve this:
     * 1. No argument given: The $scale string is already loaded and exploded to an array of items.
     * 2. A string is given: A comma-separated list of items is exploded into an array of items.
     * 3. An array of items is given and saved directly as the array of items for this scale.
     *
     * @param mixed $items Could be null, a string or an array. The method behaves differently for each case.
     * @return array The resulting array of scale items or null if the method failed to produce one.
     */
    public function load_items($items=NULL) {
        if (empty($items)) {
            $this->scale_items = explode(',', $this->scale);
        } elseif (is_array($items)) {
            $this->scale_items = $items;
        } else {
            $this->scale_items = explode(',', $items);
        }

        // Trim whitespace around each value
        foreach ($this->scale_items as $key => $val) {
            $this->scale_items[$key] = trim($val);
        }

        return $this->scale_items;
    }

    /**
     * Compacts (implodes) the array of items in $scale_items into a comma-separated string, $scale.
     * There are three ways to achieve this:
     * 1. No argument given: The $scale_items array is already loaded and imploded to a string of items.
     * 2. An array is given and is imploded into a string of items.
     * 3. A string of items is given and saved directly as the $scale variable.
     * NOTE: This method is the exact reverse of load_items, and their input/output should be interchangeable. However,
     * because load_items() trims the whitespace around the items, when the string is reconstructed these whitespaces will
     * be missing. This is not an issue, but should be kept in mind when comparing the two strings.
     *
     * @param mixed $items Could be null, a string or an array. The method behaves differently for each case.
     * @return array The resulting string of scale items or null if the method failed to produce one.
     */
    public function compact_items($items=NULL) {
        if (empty($items)) {
            $this->scale = implode(',', $this->scale_items);
        } elseif (is_array($items)) {
            $this->scale = implode(',', $items);
        } else {
            $this->scale = $items;
        }

        return $this->scale;
    }

    /**
     * When called on a loaded scale object (with a valid id) and given a float grade between
     * the grademin and grademax, this method returns the scale item that falls closest to the
     * float given (which is usually an average of several grades on a scale). If the float falls
     * below 1 but above 0, it will be rounded up to 1.
     *
     * @param float $grade
     * @return string
     */
    public function get_nearest_item($grade) {
        global $DB;
        // Obtain nearest scale item from average
        $scales_array = $DB->get_records('scale', array('id' => $this->id));
        $scale = $scales_array[$this->id];
        $scales = explode(",", $scale->scale);

        // this could be a 0 when summed and rounded, e.g, 1, no grade, no grade, no grade
        if ($grade < 1) {
            $grade = 1;
        }

        return $scales[$grade-1];
    }

    /**
     * Static function returning all global scales
     *
     * @return object
     */
    public static function fetch_all_global() {
        return grade_scale::fetch_all(array('courseid'=>0));
    }

    /**
     * Static function returning all local course scales
     *
     * @param int $courseid The course ID
     * @return array Returns an array of grade_scale instances
     */
    public static function fetch_all_local($courseid) {
        return grade_scale::fetch_all(array('courseid'=>$courseid));
    }

    /**
     * Checks if this is the last scale on the site.
     *
     * @return bool
     */
    public function is_last_global_scale() {
        return ($this->courseid == 0) && (count(self::fetch_all_global()) == 1);
    }

    /**
     * Checks if scale can be deleted.
     *
     * @return bool
     */
    public function can_delete() {
        return !$this->is_used() && !$this->is_last_global_scale();
    }

    /**
     * Returns if scale used anywhere - activities, grade items, outcomes, etc.
     *
     * @return bool
     */
    public function is_used() {
        global $DB;
        global $CFG;

        // count grade items excluding the
        $params = array($this->id);
        $sql = "SELECT COUNT(id) FROM {grade_items} WHERE scaleid = ? AND outcomeid IS NULL";
        if ($DB->count_records_sql($sql, $params)) {
            return true;
        }

        // count outcomes
        $sql = "SELECT COUNT(id) FROM {grade_outcomes} WHERE scaleid = ?";
        if ($DB->count_records_sql($sql, $params)) {
            return true;
        }

        // Ask the competency subsystem.
        if (\core_competency\api::is_scale_used_anywhere($this->id)) {
            return true;
        }

        // Ask all plugins if the scale is used anywhere.
        $pluginsfunction = get_plugins_with_function('scale_used_anywhere');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                if ($pluginfunction($this->id)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the formatted grade description with URLs converted
     *
     * @return string
     */
    public function get_description() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $systemcontext = context_system::instance();
        $options = new stdClass;
        $options->noclean = true;
        $description = file_rewrite_pluginfile_urls($this->description, 'pluginfile.php', $systemcontext->id, 'grade', 'scale', $this->id);
        return format_text($description, $this->descriptionformat, $options);
    }
}

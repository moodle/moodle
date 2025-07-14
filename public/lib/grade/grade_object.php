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
 * Definition of a grade object class for grade item, grade category etc to inherit from
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * An abstract object that holds methods and attributes common to all grade_* objects defined here.
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_object {
    /**
     * The database table this grade object is stored in
     * @var string $table
     */
    public $table;

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'timecreated', 'timemodified', 'hidden');

    /**
     * Array of optional fields with default values - usually long text information that is not always needed.
     * If you want to create an instance without optional fields use: new grade_object($only_required_fields, false);
     * @var array $optional_fields
     */
    public $optional_fields = array();

    /**
     * The PK.
     * @var int $id
     */
    public $id;

    /**
     * The first time this grade_object was created.
     * @var int $timecreated
     */
    public $timecreated;

    /**
     * The last time this grade_object was modified.
     * @var int $timemodified
     */
    public $timemodified;

    /**
     * 0 if visible, 1 always hidden or date not visible until
     * @var int $hidden
     */
    var $hidden = 0;

    /**
     * Constructor. Optionally (and by default) attempts to fetch corresponding row from the database
     *
     * @param array $params An array with required parameters for this grade object.
     * @param bool $fetch Whether to fetch corresponding row from the database or not,
     *        optional fields might not be defined if false used
     */
    public function __construct($params=NULL, $fetch=true) {
        if (!empty($params) and (is_array($params) or is_object($params))) {
            if ($fetch) {
                if ($data = $this->fetch($params)) {
                    grade_object::set_properties($this, $data);
                } else {
                    grade_object::set_properties($this, $this->optional_fields);//apply defaults for optional fields
                    grade_object::set_properties($this, $params);
                }

            } else {
                grade_object::set_properties($this, $params);
            }

        } else {
            grade_object::set_properties($this, $this->optional_fields);//apply defaults for optional fields
        }
    }

    /**
     * Makes sure all the optional fields are loaded.
     *
     * If id present, meaning the instance exists in the database, then data will be fetched from the database.
     * Defaults are used for new instances.
     */
    public function load_optional_fields() {
        global $DB;
        foreach ($this->optional_fields as $field=>$default) {
            if (property_exists($this, $field)) {
                continue;
            }
            if (empty($this->id)) {
                $this->$field = $default;
            } else {
                $this->$field = $DB->get_field($this->table, $field, array('id', $this->id));
            }
        }
    }

    /**
     * Finds and returns a grade_object instance based on params.
     *
     * @static
     * @abstract
     * @param array $params associative arrays varname=>value
     * @return object grade_object instance or false if none found.
     */
    public static function fetch($params) {
        throw new coding_exception('fetch() method needs to be overridden in each subclass of grade_object');
    }

    /**
     * Finds and returns all grade_object instances based on $params.
     *
     * @static
     * @abstract
     * @throws coding_exception Throws a coding exception if fetch_all() has not been overriden by the grade object subclass
     * @param array $params Associative arrays varname=>value
     * @return array|bool Array of grade_object instances or false if none found.
     */
    public static function fetch_all($params) {
        throw new coding_exception('fetch_all() method needs to be overridden in each subclass of grade_object');
    }

    /**
     * Factory method which uses the parameters to retrieve matching instances from the database
     *
     * @param string $table The table to retrieve from
     * @param string $classname The name of the class to instantiate
     * @param array $params An array of conditions like $fieldname => $fieldvalue
     * @return mixed An object instance or false if not found
     */
    protected static function fetch_helper($table, $classname, $params) {
        if ($instances = grade_object::fetch_all_helper($table, $classname, $params)) {
            if (count($instances) > 1) {
                // we should not tolerate any errors here - problems might appear later
                throw new \moodle_exception('morethanonerecordinfetch', 'debug');
            }
            return reset($instances);
        } else {
            return false;
        }
    }

    /**
     * Factory method which uses the parameters to retrieve all matching instances from the database
     *
     * @param string $table The table to retrieve from
     * @param string $classname The name of the class to instantiate
     * @param array $params An array of conditions like $fieldname => $fieldvalue
     * @param string $sortby The SQL sort order. E.g. 'id ASC'
     * @return array|bool Array of object instances (sorted by $sortby) or false if not found
     */
    public static function fetch_all_helper($table, $classname, $params, string $sortby = 'id ASC') {
        global $DB; // Need to introspect DB here.

        $instance = new $classname();

        $classvars = (array)$instance;
        $params    = (array)$params;

        $wheresql = array();
        $newparams = array();

        $columns = $DB->get_columns($table); // Cached, no worries.

        foreach ($params as $var=>$value) {
            if (!in_array($var, $instance->required_fields) and !array_key_exists($var, $instance->optional_fields)) {
                continue;
            }
            if (!array_key_exists($var, $columns)) {
                continue;
            }
            if (is_null($value)) {
                $wheresql[] = " $var IS NULL ";
            } else {
                if ($columns[$var]->meta_type === 'X') {
                    // We have a text/clob column, use the cross-db method for its comparison.
                    $wheresql[] = ' ' . $DB->sql_compare_text($var) . ' = ' . $DB->sql_compare_text('?') . ' ';
                } else {
                    // Other columns (varchar, integers...).
                    $wheresql[] = " $var = ? ";
                }
                $newparams[] = $value;
            }
        }

        if (empty($wheresql)) {
            $wheresql = '';
        } else {
            $wheresql = implode("AND", $wheresql);
        }

        global $DB;
        $rs = $DB->get_recordset_select($table, $wheresql, $newparams, $sortby);
        //returning false rather than empty array if nothing found
        if (!$rs->valid()) {
            $rs->close();
            return false;
        }

        $result = array();
        foreach($rs as $data) {
            $instance = new $classname();
            grade_object::set_properties($instance, $data);
            $result[$instance->id] = $instance;
        }
        $rs->close();
        return $result;
    }

    /**
     * Updates this object in the Database, based on its object variables. ID must be set.
     *
     * @param string $source from where was the object updated (mod/forum, manual, etc.)
     * @param bool $isbulkupdate If bulk grade update is happening.
     * @return bool success
     */
    public function update($source = null, $isbulkupdate = false) {
        global $USER, $CFG, $DB;

        if (empty($this->id)) {
            debugging('Can not update grade object, no id!');
            return false;
        }

        $data = $this->get_record_data();

        $DB->update_record($this->table, $data);

        $historyid = null;
        if (empty($CFG->disablegradehistory)) {
            unset($data->timecreated);
            $data->action       = GRADE_HISTORY_UPDATE;
            $data->oldid        = $this->id;
            $data->source       = $source;
            $data->timemodified = time();
            $data->loggeduser   = $USER->id;
            $historyid = $DB->insert_record($this->table.'_history', $data);
        }

        $this->notify_changed(false, $isbulkupdate);

        $this->update_feedback_files($historyid);

        return true;
    }

    /**
     * Deletes this object from the database.
     *
     * @param string $source From where was the object deleted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function delete($source=null) {
        global $USER, $CFG, $DB;

        if (empty($this->id)) {
            debugging('Can not delete grade object, no id!');
            return false;
        }

        $data = $this->get_record_data();

        if ($DB->delete_records($this->table, array('id'=>$this->id))) {
            if (empty($CFG->disablegradehistory)) {
                unset($data->id);
                unset($data->timecreated);
                $data->action       = GRADE_HISTORY_DELETE;
                $data->oldid        = $this->id;
                $data->source       = $source;
                $data->timemodified = time();
                $data->loggeduser   = $USER->id;
                $DB->insert_record($this->table.'_history', $data);
            }

            $this->notify_changed(true);

            $this->delete_feedback_files();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns object with fields and values that are defined in database
     *
     * @return stdClass
     */
    public function get_record_data() {
        $data = new stdClass();

        foreach ($this as $var=>$value) {
            if (in_array($var, $this->required_fields) or array_key_exists($var, $this->optional_fields)) {
                if (is_object($value) or is_array($value)) {
                    debugging("Incorrect property '$var' found when inserting grade object");
                } else {
                    $data->$var = $value;
                }
            }
        }
        return $data;
    }

    /**
     * Records this object in the Database, sets its id to the returned value, and returns that value.
     * If successful this function also fetches the new object data from database and stores it
     * in object properties.
     *
     * @param string $source From where was the object inserted (mod/forum, manual, etc.)
     * @param string $isbulkupdate If bulk grade update is happening.
     * @return int The new grade object ID if successful, false otherwise
     */
    public function insert($source = null, $isbulkupdate = false) {
        global $USER, $CFG, $DB;

        if (!empty($this->id)) {
            debugging("Grade object already exists!");
            return false;
        }

        $data = $this->get_record_data();

        $this->id = $DB->insert_record($this->table, $data);

        // set all object properties from real db data
        $this->update_from_db();

        $data = $this->get_record_data();

        $historyid = null;
        if (empty($CFG->disablegradehistory)) {
            unset($data->timecreated);
            $data->action       = GRADE_HISTORY_INSERT;
            $data->oldid        = $this->id;
            $data->source       = $source;
            $data->timemodified = time();
            $data->loggeduser   = $USER->id;
            $historyid = $DB->insert_record($this->table.'_history', $data);
        }

        $this->notify_changed(false, $isbulkupdate);

        $this->add_feedback_files($historyid);

        return $this->id;
    }

    /**
     * Using this object's id field, fetches the matching record in the DB, and looks at
     * each variable in turn. If the DB has different data, the db's data is used to update
     * the object. This is different from the update() function, which acts on the DB record
     * based on the object.
     *
     * @return bool True if successful
     */
    public function update_from_db() {
        if (empty($this->id)) {
            debugging("The object could not be used in its state to retrieve a matching record from the DB, because its id field is not set.");
            return false;
        }
        global $DB;
        if (!$params = $DB->get_record($this->table, array('id' => $this->id))) {
            debugging("Object with this id:{$this->id} does not exist in table:{$this->table}, can not update from db!");
            return false;
        }

        grade_object::set_properties($this, $params);

        return true;
    }

    /**
     * Given an associated array or object, cycles through each key/variable
     * and assigns the value to the corresponding variable in this object.
     *
     * @param grade_object $instance The object to set the properties on
     * @param array $params An array of properties to set like $propertyname => $propertyvalue
     * @return array|stdClass Either an associative array or an object containing property name, property value pairs
     */
    public static function set_properties(&$instance, $params) {
        $params = (array) $params;
        foreach ($params as $var => $value) {
            if (in_array($var, $instance->required_fields) or array_key_exists($var, $instance->optional_fields)) {
                $instance->$var = $value;
            }
        }
    }

    /**
     * Called immediately after the object data has been inserted, updated, or
     * deleted in the database. Default does nothing, can be overridden to
     * hook in special behaviour.
     *
     * @param bool $deleted
     */
    protected function notify_changed($deleted) {
    }

    /**
     * Handles adding feedback files in the gradebook.
     *
     * @param int|null $historyid
     */
    protected function add_feedback_files(?int $historyid = null) {
    }

    /**
     * Handles updating feedback files in the gradebook.
     *
     * @param int|null $historyid
     */
    protected function update_feedback_files(?int $historyid = null) {
    }

    /**
     * Handles deleting feedback files in the gradebook.
     */
    protected function delete_feedback_files() {
    }

    /**
     * Returns the current hidden state of this grade_item
     *
     * This depends on the grade object hidden setting and the current time if hidden is set to a "hidden until" timestamp
     *
     * @return bool Current hidden state
     */
    function is_hidden() {
        return ($this->hidden == 1 or ($this->hidden != 0 and $this->hidden > time()));
    }

    /**
     * Check grade object hidden status
     *
     * @return bool True if a "hidden until" timestamp is set, false if grade object is set to always visible or always hidden.
     */
    function is_hiddenuntil() {
        return $this->hidden > 1;
    }

    /**
     * Check a grade item hidden status.
     *
     * @return int 0 means visible, 1 hidden always, a timestamp means "hidden until"
     */
    function get_hidden() {
        return $this->hidden;
    }

    /**
     * Set a grade object hidden status
     *
     * @param int $hidden 0 means visiable, 1 means hidden always, a timestamp means "hidden until"
     * @param bool $cascade Ignored
     */
    function set_hidden($hidden, $cascade=false) {
        $this->hidden = $hidden;
        $this->update();
    }

    /**
     * Returns whether the grade object can control the visibility of the grades.
     *
     * @return bool
     */
    public function can_control_visibility() {
        return true;
    }
}

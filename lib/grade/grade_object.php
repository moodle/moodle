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
 * Definitions of grade object class
 *
 * @package    core
 * @subpackage grade
 * @copyright  2006 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * An abstract object that holds methods and attributes common to all grade_* objects defined here.
 * @abstract
 */
abstract class grade_object {
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
     * Constructor. Optionally (and by default) attempts to fetch corresponding row from DB.
     * @param array $params an array with required parameters for this grade object.
     * @param boolean $fetch Whether to fetch corresponding row from DB or not,
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
     * If id present (==instance exists in db) fetches data from db.
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
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_object instance or false if none found.
     */
    public static function fetch($params) {
        throw new coding_exception('fetch() method needs to be overridden in each subclass of grade_object');
    }

    /**
     * Finds and returns all grade_object instances based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_object instances or false if none found.
     */
    public static function fetch_all($params) {
        throw new coding_exception('fetch_all() method needs to be overridden in each subclass of grade_object');
    }

    /**
     * Factory method - uses the parameters to retrieve matching instance from the DB.
     * @static final protected
     * @return mixed object instance or false if not found
     */
    protected static function fetch_helper($table, $classname, $params) {
        if ($instances = grade_object::fetch_all_helper($table, $classname, $params)) {
            if (count($instances) > 1) {
                // we should not tolerate any errors here - problems might appear later
                print_error('morethanonerecordinfetch','debug');
            }
            return reset($instances);
        } else {
            return false;
        }
    }

    /**
     * Factory method - uses the parameters to retrieve all matching instances from the DB.
     * @static final protected
     * @return mixed array of object instances or false if not found
     */
    public static function fetch_all_helper($table, $classname, $params) {
        $instance = new $classname();

        $classvars = (array)$instance;
        $params    = (array)$params;

        $wheresql = array();
        $newparams = array();

        foreach ($params as $var=>$value) {
            if (!in_array($var, $instance->required_fields) and !array_key_exists($var, $instance->optional_fields)) {
                continue;
            }
            if (is_null($value)) {
                $wheresql[] = " $var IS NULL ";
            } else {
                $wheresql[] = " $var = ? ";
                $newparams[] = $value;
            }
        }

        if (empty($wheresql)) {
            $wheresql = '';
        } else {
            $wheresql = implode("AND", $wheresql);
        }

        global $DB;
        $rs = $DB->get_recordset_select($table, $wheresql, $newparams);
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
     * @param string $source from where was the object updated (mod/forum, manual, etc.)
     * @return boolean success
     */
    public function update($source=null) {
        global $USER, $CFG, $DB;

        if (empty($this->id)) {
            debugging('Can not update grade object, no id!');
            return false;
        }

        $data = $this->get_record_data();

        $DB->update_record($this->table, $data);

        if (empty($CFG->disablegradehistory)) {
            unset($data->timecreated);
            $data->action       = GRADE_HISTORY_UPDATE;
            $data->oldid        = $this->id;
            $data->source       = $source;
            $data->timemodified = time();
            $data->loggeduser   = $USER->id;
            $DB->insert_record($this->table.'_history', $data);
        }

        $this->notify_changed(false);
        return true;
    }

    /**
     * Deletes this object from the database.
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return boolean success
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
            return true;

        } else {
            return false;
        }
    }

    /**
     * Returns object with fields and values that are defined in database
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
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    public function insert($source=null) {
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

        if (empty($CFG->disablegradehistory)) {
            unset($data->timecreated);
            $data->action       = GRADE_HISTORY_INSERT;
            $data->oldid        = $this->id;
            $data->source       = $source;
            $data->timemodified = time();
            $data->loggeduser   = $USER->id;
            $DB->insert_record($this->table.'_history', $data);
        }

        $this->notify_changed(false);
        return $this->id;
    }

    /**
     * Using this object's id field, fetches the matching record in the DB, and looks at
     * each variable in turn. If the DB has different data, the db's data is used to update
     * the object. This is different from the update() function, which acts on the DB record
     * based on the object.
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
     * @static final
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
    function notify_changed($deleted) {
    }

    /**
     * Returns the hidden state of this grade_item
     * @return boolean hidden state
     */
    function is_hidden() {
        return ($this->hidden == 1 or ($this->hidden != 0 and $this->hidden > time()));
    }

    /**
     * Check grade hidden status. Uses data from both grade item and grade.
     * @return boolean true if hiddenuntil, false if not
     */
    function is_hiddenuntil() {
        return $this->hidden > 1;
    }

    /**
     * Check grade item hidden status.
     * @return int 0 means visible, 1 hidden always, timestamp hidden until
     */
    function get_hidden() {
        return $this->hidden;
    }

    function set_hidden($hidden, $cascade=false) {
        $this->hidden = $hidden;
        $this->update();
    }
}

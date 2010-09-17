<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * An abstract object that holds methods and attributes common to all grade_* objects defined here.
 * @abstract
 */
class grade_object {
    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    var $required_fields = array('id', 'timecreated', 'timemodified');

    /**
     * Array of optional fields with default values - usually long text information that is not always needed.
     * If you want to create an instance without optional fields use: new grade_object($only_required_fields, false);
     * @var array $optional_fields
     */
    var $optional_fields = array();

    /**
     * The PK.
     * @var int $id
     */
    var $id;

    /**
     * The first time this grade_object was created.
     * @var int $timecreated
     */
    var $timecreated;

    /**
     * The last time this grade_object was modified.
     * @var int $timemodified
     */
    var $timemodified;

    /**
     * Constructor. Optionally (and by default) attempts to fetch corresponding row from DB.
     * @param array $params an array with required parameters for this grade object.
     * @param boolean $fetch Whether to fetch corresponding row from DB or not,
     *        optional fields might not be defined if false used
     */
    function grade_object($params=NULL, $fetch=true) {
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
    function load_optional_fields() {
        foreach ($this->optional_fields as $field=>$default) {
            if (array_key_exists($field, $this)) {
                continue;
            }
            if (empty($this->id)) {
                $this->$field = $default;
            } else {
                $this->$field = get_field($this->table, $field, 'id', $this->id);
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
    function fetch($params) {
        error('Abstract method fetch() not overridden in '.get_class($this));
    }

    /**
     * Finds and returns all grade_object instances based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_object insatnces or false if none found.
     */
    function fetch_all($params) {
        error('Abstract method fetch_all() not overridden in '.get_class($this));
    }

    /**
     * Factory method - uses the parameters to retrieve matching instance from the DB.
     * @static final protected
     * @return mixed object instance or false if not found
     */
    function fetch_helper($table, $classname, $params) {
        if ($instances = grade_object::fetch_all_helper($table, $classname, $params)) {
            if (count($instances) > 1) {
                // we should not tolerate any errors here - problems might appear later
                error('Found more than one record in fetch() !');
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
    function fetch_all_helper($table, $classname, $params) {
        $instance = new $classname();

        $classvars = (array)$instance;
        $params    = (array)$params;

        $wheresql = array();

        // remove incorrect params
        foreach ($params as $var=>$value) {
            if (!in_array($var, $instance->required_fields) and !array_key_exists($var, $instance->optional_fields)) {
                continue;
            }
            if (is_null($value)) {
                $wheresql[] = " $var IS NULL ";
            } else {
                $value = addslashes($value);
                $wheresql[] = " $var = '$value' ";
            }
        }

        if (empty($wheresql)) {
            $wheresql = '';
        } else {
            $wheresql = implode("AND", $wheresql);
        }

        if ($datas = get_records_select($table, $wheresql, 'id')) {
            $result = array();
            foreach($datas as $data) {
                $instance = new $classname();
                grade_object::set_properties($instance, $data);
                $result[$instance->id] = $instance;
            }
            return $result;

        } else {
            return false;
        }
    }

    /**
     * Updates this object in the Database, based on its object variables. ID must be set.
     * @param string $source from where was the object updated (mod/forum, manual, etc.)
     * @return boolean success
     */
    function update($source=null) {
        global $USER, $CFG;

        if (empty($this->id)) {
            debugging('Can not update grade object, no id!');
            return false;
        }

        $data = $this->get_record_data();

        if (!update_record($this->table, addslashes_recursive($data))) {
            return false;
        }

        if (empty($CFG->disablegradehistory)) {
            unset($data->timecreated);
            $data->action       = GRADE_HISTORY_UPDATE;
            $data->oldid        = $this->id;
            $data->source       = $source;
            $data->timemodified = time();
            $data->loggeduser   = $USER->id;
            insert_record($this->table.'_history', addslashes_recursive($data));
        }

        return true;
    }

    /**
     * Deletes this object from the database.
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function delete($source=null) {
        global $USER, $CFG;

        if (empty($this->id)) {
            debugging('Can not delete grade object, no id!');
            return false;
        }

        $data = $this->get_record_data();

        if (delete_records($this->table, 'id', $this->id)) {
            if (empty($CFG->disablegradehistory)) {
                unset($data->id);
                unset($data->timecreated);
                $data->action       = GRADE_HISTORY_DELETE;
                $data->oldid        = $this->id;
                $data->source       = $source;
                $data->timemodified = time();
                $data->loggeduser   = $USER->id;
                insert_record($this->table.'_history', addslashes_recursive($data));
            }
            return true;

        } else {
            return false;
        }
    }

    /**
     * Returns object with fields and values that are defined in database
     */
    function get_record_data() {
        $data = new object();
        // we need to do this to prevent infinite loops in addslashes_recursive - grade_item -> category ->grade_item
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
    function insert($source=null) {
        global $USER, $CFG;

        if (!empty($this->id)) {
            debugging("Grade object already exists!");
            return false;
        }

        $data = $this->get_record_data();

        if (!$this->id = insert_record($this->table, addslashes_recursive($data))) {
            debugging("Could not insert object into db");
            return false;
        }

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
            insert_record($this->table.'_history', addslashes_recursive($data));
        }

        return $this->id;
    }

    /**
     * Using this object's id field, fetches the matching record in the DB, and looks at
     * each variable in turn. If the DB has different data, the db's data is used to update
     * the object. This is different from the update() function, which acts on the DB record
     * based on the object.
     */
    function update_from_db() {
        if (empty($this->id)) {
            debugging("The object could not be used in its state to retrieve a matching record from the DB, because its id field is not set.");
            return false;
        }

        if (!$params = get_record($this->table, 'id', $this->id)) {
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
    function set_properties(&$instance, $params) {
        $params = (array) $params;
        foreach ($params as $var => $value) {
            if (in_array($var, $instance->required_fields) or array_key_exists($var, $instance->optional_fields)) {
                $instance->$var = $value;
            }
        }
    }
}
?>

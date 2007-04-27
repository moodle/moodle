<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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
class grade_object
{
    /**
     * The table name
     * @var string $table
     */
    var $table = null;
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'required_fields');

    /**
     * Array of required fields (keys) and their default values (values).
     * @var array $required_fields
     */
    var $required_fields = array();
    
    /**
     * The PK.
     * @var int $id 
     */
    var $id;
    
    /**
     * The first time this grade_calculation was created.
     * @var int $timecreated
     */
    var $timecreated;
    
    /**
     * The last time this grade_calculation was modified.
     * @var int $timemodified
     */
    var $timemodified;

    /**
     * Finds and returns a grade_object based on its ID number.
     * 
     * @abstract
     * @param int $id
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @return object grade_object or false if none found.
     */
    function get_by_id($id, $static=false)
    {
        // Implemented in child objects 
    }
    

    /**
     * Finds and returns a grade_object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_object or false if none found.
     */
    function get_record($static=false, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*")
    { 
        // Implemented in child objects 
    }
    
    /**
     * Updates this object in the Database, based on its object variables. ID must be set.
     *
     * @return boolean
     */
    function update()
    {
        $this->set_defaults();
        $result = update_record($this->table, $this);
        if ($result) {
            $this->timemodified = mktime();
        }
        return $result;
    }

    /**
     * Deletes this object from the database.
     */
    function delete()
    {
        return delete_records($this->table, 'id', $this->id);
    }
    
    /**
     * Replaces NULL values with defaults defined in the DB, for required fields.
     * This should use the DB table METADATA, but to start with I am hard-coding it.
     *
     * @return void
     */
    function set_defaults()
    {
        foreach ($this->required_fields as $field => $default) {
            if (is_null($this->$field)) {
                $this->$field = $default;
            }
        }
    }
    
    /**
     * Records this object in the Database, sets its id to the returned value, and returns that value.
     * @return int PK ID if successful, false otherwise
     */
    function insert()
    {
        $this->set_defaults();
        $this->set_timecreated();
        $this->id = insert_record($this->table, $this, true);
        return $this->id;
    }
    
    /**
     * Uses the variables of this object to retrieve all matching objects from the DB.
     * @return array $objects
     */
    function get_records_select()
    {
        $variables = get_object_vars($this);
        $wheresql = '';
        
        foreach ($variables as $var => $value) {
            if (!empty($value) && !in_array($var, $this->nonfields)) {
                $wheresql .= " $var = '$value' AND ";
            }
        }
        
        // Trim trailing AND
        $wheresql = substr($wheresql, 0, strrpos($wheresql, 'AND'));

        return get_records_select($this->table, $wheresql, 'id');
    }
  
    /**
     * If this object hasn't yet been saved in DB (empty $id), this method sets the timecreated variable
     * to the current or given time. If a value is already set in DB,
     * this method will do nothing, unless the $override parameter is set to true. This is to avoid
     * unintentional overwrites. 
     *
     * @param int $timestamp Optional timestamp to override current timestamp.
     * @param boolean $override Whether to override an existing value for this field in the DB.
     * @return boolean True if successful, false otherwise.
     */
    function set_timecreated($timestamp = null, $override = false)
    {
        if (empty($timestamp)) {
            $timestamp = mktime();
        }
        
        if (empty($this->id)) {
            $this->timecreated = $timestamp;
            $this->timemodified = $timestamp;
        } else { 
            $current_time = get_field($this->table, 'timecreated', 'id', $this->id);

            if (empty($current_time) || $override) {
                $this->timecreated = $timestamp;
                $this->timemodified = $timestamp;
                return $this->timecreated;
            } else {                
                return false;
            } 
        }
        return $this->timecreated;
    }
}
?>

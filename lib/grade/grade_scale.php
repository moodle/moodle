<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com       //
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

require_once('grade_object.php');

/**
 * Class representing a grade scale. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_scale extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'scale';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    var $required_fields = array('id', 'courseid', 'userid', 'name', 'scale', 'description', 'timemodified');

    /**
     * The course this scale belongs to.
     * @var int $courseid
     */
    var $courseid;

    var $userid;

    /**
     * The name of the scale.
     * @var string $name
     */
    var $name;

    /**
     * The items in this scale.
     * @var array $scale_items
     */
    var $scale_items = array();

    /**
     * A string representatin of the scale items (a comma-separated list).
     * @var string $scale
     */
    var $scale;

    /**
     * A description for this scale.
     * @var string $description
     */
    var $description;

    /**
     * Finds and returns a grade_scale instance based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_scale instance or false if none found.
     */
    function fetch($params) {
        return grade_object::fetch_helper('scale', 'grade_scale', $params);
    }

    /**
     * Finds and returns all grade_scale instances based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_scale insatnces or false if none found.
     */
    function fetch_all($params) {
        return grade_object::fetch_all_helper('scale', 'grade_scale', $params);
    }

    /**
     * Records this object in the Database, sets its id to the returned value, and returns that value.
     * If successful this function also fetches the new object data from database and stores it
     * in object properties.
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    function insert($source=null) {
        $this->timecreated = time();
        $this->timemodified = time();
        return parent::insert($source);
    }

    /**
     * In addition to update() it also updates grade_outcomes_courses if needed
     * @param string $source from where was the object inserted
     * @return boolean success
     */
    function update($source=null) {
        $this->timemodified = time();
        return parent::update($source);
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     * @return string name
     */
    function get_name() {
        return format_string($this->name);
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
    function load_items($items=NULL) {
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
    function compact_items($items=NULL) {
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
     * @param float $grade
     * @return string
     */
    function get_nearest_item($grade) {
        // Obtain nearest scale item from average
        $scales_array = get_records_list('scale', 'id', $this->id);
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
     * @return object
     */
    function fetch_all_global() {
        return grade_scale::fetch_all(array('courseid'=>0));
    }

    /**
     * Static function returning all local course scales
     * @return object
     */
    function fetch_all_local($courseid) {
        return grade_scale::fetch_all(array('courseid'=>$courseid));
    }

    /**
     * Checks if scale can be deleted.
     * @return boolean
     */
    function can_delete() {
        return !$this->is_used();
    }

    /**
     * Returns if scale used anywhere - activities, grade items, outcomes, etc.
     * @return bool
     */
    function is_used() {
        global $CFG;

        // count grade items excluding the
        $sql = "SELECT COUNT(id) FROM {$CFG->prefix}grade_items WHERE scaleid = {$this->id} AND outcomeid IS NULL";
        if (count_records_sql($sql)) {
            return true;
        }

        // count outcomes
        $sql = "SELECT COUNT(id) FROM {$CFG->prefix}grade_outcomes WHERE scaleid = {$this->id}";
        if (count_records_sql($sql)) {
            return true;
        }

        $legacy_mods = false;
        if ($mods = get_records('modules', 'visible', 1)) {
            foreach ($mods as $mod) {
                //Check cm->name/lib.php exists
                if (file_exists($CFG->dirroot.'/mod/'.$mod->name.'/lib.php')) {
                    include_once($CFG->dirroot.'/mod/'.$mod->name.'/lib.php');
                    $function_name = $mod->name.'_scale_used_anywhere';
                    $old_function_name = $mod->name.'_scale_used';
                    if (function_exists($function_name)) {
                        if ($function_name($this->id)) {
                            return true;
                        }

                    } else if (function_exists($old_function_name)) {
                        $legacy_mods = true;
                        debugging('Please notify the developer of module "'.$mod->name.'" that new function module_scale_used_anywhere() should be implemented.', DEBUG_DEVELOPER);
                        break;
                    }
                }
            }
        }

        // some mods are missing the new xxx_scale_used_anywhere() - use the really slow old way
        if ($legacy_mods) {
            if (!empty($this->courseid)) {
                if (course_scale_used($this->courseid,$this->id)) {
                    return true;
                }
            } else {
                $courses = array();
                if (site_scale_used($this->id,$courses)) {
                    return true;
                }
            }
        }

        return false;
    }
}
?>

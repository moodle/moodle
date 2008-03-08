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

require_once('grade_object.php');

/**
 * Class representing a grade outcome. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_outcome extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_outcomes';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    var $required_fields = array('id', 'courseid', 'shortname', 'fullname', 'scaleid',
                                 'description', 'timecreated', 'timemodified', 'usermodified');

    /**
     * The course this outcome belongs to.
     * @var int $courseid
     */
    var $courseid;

    /**
     * The shortname of the outcome.
     * @var string $shortname
     */
    var $shortname;

    /**
     * The fullname of the outcome.
     * @var string $fullname
     */
    var $fullname;

    /**
     * A full grade_scale object referenced by $this->scaleid.
     * @var object $scale
     */
    var $scale;

    /**
     * The id of the scale referenced by this outcome.
     * @var int $scaleid
     */
    var $scaleid;

    /**
     * The description of this outcome - FORMAT_MOODLE.
     * @var string $description
     */
    var $description;

    /**
     * The userid of the person who last modified this outcome.
     * @var int $usermodified
     */
    var $usermodified;

    /**
     * Deletes this outcome from the database.
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function delete($source=null) {
        if (!empty($this->courseid)) {
            delete_records('grade_outcomes_courses', 'outcomeid', $this->id, 'courseid', $this->courseid);
        }
        return parent::delete($source);
    }

    /**
     * Records this object in the Database, sets its id to the returned value, and returns that value.
     * If successful this function also fetches the new object data from database and stores it
     * in object properties.
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    function insert($source=null) {

        $this->timecreated = $this->timemodified = time();

        if ($result = parent::insert($source)) {
            if (!empty($this->courseid)) {
                $goc = new object();
                $goc->courseid = $this->courseid;
                $goc->outcomeid = $this->id;
                insert_record('grade_outcomes_courses', $goc);
            }
        }
        return $result;
    }

    /**
     * In addition to update() it also updates grade_outcomes_courses if needed
     * @param string $source from where was the object inserted
     * @return boolean success
     */
    function update($source=null) {
        $this->timemodified = time();

        if ($result = parent::update($source)) {
            if (!empty($this->courseid)) {
                $this->use_in($this->courseid);
            }
        }
        return $result;
    }

    /**
     * Mark outcome as used in course
     * @param int $courseid
     * @return succes - false if incorrect courseid requested
     */
    function use_in($courseid) {
        if (!empty($this->courseid) and $courseid != $this->courseid) {
            return false;
        }

        if (!record_exists('grade_outcomes_courses', 'courseid', $courseid, 'outcomeid', $this->id)) {
            $goc = new object();
            $goc->courseid  = $courseid;
            $goc->outcomeid = $this->id;
            return (bool)insert_record('grade_outcomes_courses', $goc);
        }
        return true;
    }

    /**
     * Finds and returns a grade_outcome instance based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_outcome instance or false if none found.
     */
    function fetch($params) {
        return grade_object::fetch_helper('grade_outcomes', 'grade_outcome', $params);
    }

    /**
     * Finds and returns all grade_outcome instances based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_outcome insatnces or false if none found.
     */
    function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_outcomes', 'grade_outcome', $params);
    }

    /**
     * Instantiates a grade_scale object whose data is retrieved from the
     * @return object grade_scale
     */
    function load_scale() {
        if (empty($this->scale->id) or $this->scale->id != $this->scaleid) {
            $this->scale = grade_scale::fetch(array('id'=>$this->scaleid));
            $this->scale->load_items();
        }
        return $this->scale;
    }

    /**
     * Static function returning all global outcomes
     * @static
     * @return object
     */
    function fetch_all_global() {
        if (!$outcomes = grade_outcome::fetch_all(array('courseid'=>null))) {
            $outcomes = array();
        }
        return $outcomes;
    }

    /**
     * Static function returning all local course outcomes
     * @static
     * @param int $courseid
     * @return object
     */
    function fetch_all_local($courseid) {
        if (!$outcomes =grade_outcome::fetch_all(array('courseid'=>$courseid))) {
            $outcomes = array();
        }
        return $outcomes;
    }

    /**
     * Static method - returns all outcomes available in course
     * @static
     * @param int $courseid
     * @return array
     */
    function fetch_all_available($courseid) {
        global $CFG;

        $result = array();
        $sql = "SELECT go.*
                  FROM {$CFG->prefix}grade_outcomes go, {$CFG->prefix}grade_outcomes_courses goc
                 WHERE go.id = goc.outcomeid AND goc.courseid = {$courseid}
              ORDER BY go.id ASC";

        if ($datas = get_records_sql($sql)) {
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
     * @return string name
     */
    function get_name() {
        return format_string($this->fullname);
    }

    /**
     * Returns unique outcome short name.
     * @return string name
     */
    function get_shortname() {
        return $this->shortname;
    }

    /**
     * Checks if outcome can be deleted.
     * @return boolean
     */
    function can_delete() {
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
     * @return int
     */
    function get_course_uses_count() {
        global $CFG;

        if (!empty($this->courseid)) {
            return 1;
        }

        return count_records('grade_outcomes_courses', 'outcomeid', $this->id);
    }

    /**
     * Returns the number of places where outcome is used.
     * @return int
     */
    function get_item_uses_count() {
        return count_records('grade_items', 'outcomeid', $this->id);
    }

    /**
     * Computes then returns extra information about this outcome and other objects that are linked to it.
     * The average of all grades that use this outcome, for all courses (or 1 course if courseid is given) can
     * be requested, and is returned as a float if requested alone. If the list of items that use this outcome
     * is also requested, then a single array is returned, which contains the grade_items AND the average grade
     * if such is still requested (array('items' => array(...), 'avg' => 2.30)). This combining of two
     * methods into one is to save on DB queries, since both queries are similar and can be performed together.
     * @param int $courseid An optional courseid to narrow down the average to 1 course only
     * @param bool $average Whether or not to return the average grade for this outcome
     * @param bool $items Whether or not to return the list of items using this outcome
     * @return float
     */
    function get_grade_info($courseid=null, $average=true, $items=false) {
        global $CFG;

        if (!isset($this->id)) {
            debugging("You must setup the outcome's id before calling its get_grade_info() method!");
            return false; // id must be defined for this to work
        }

        if ($average === false && $items === false) {
            debugging('Either the 1st or 2nd param of grade_outcome::get_grade_info() must be true, or both, but not both false!');
            return false;
        }

        $wheresql = '';
        if (!is_null($courseid)) {
            $wheresql = " AND {$CFG->prefix}grade_items.courseid = $courseid ";
        }

        $selectadd = '';
        if ($items !== false) {
            $selectadd = ", {$CFG->prefix}grade_items.* ";
        }

        $sql = "SELECT finalgrade $selectadd
                  FROM {$CFG->prefix}grade_grades, {$CFG->prefix}grade_items, {$CFG->prefix}grade_outcomes
                 WHERE {$CFG->prefix}grade_outcomes.id = {$CFG->prefix}grade_items.outcomeid
                   AND {$CFG->prefix}grade_items.id = {$CFG->prefix}grade_grades.itemid
                   AND {$CFG->prefix}grade_outcomes.id = $this->id
                   $wheresql";

        $grades = get_records_sql($sql);
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
?>

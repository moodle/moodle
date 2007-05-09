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

require_once('grade_object.php');

class grade_grades_raw extends grade_object {
    
    /**
     * The DB table.
     * @var string $table
     */
    var $table = 'grade_grades_raw';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'scale');
    
    /**
     * The id of the grade_item this raw grade belongs to.
     * @var int $itemid
     */
    var $itemid;
    
    /**
     * The id of the user this raw grade belongs to.
     * @var int $userid
     */
    var $userid;
    
    /**
     * The grade value of this raw grade, if such was provided by the module.
     * @var float $gradevalue
     */
    var $gradevalue;
   
    /**
     * The maximum allowable grade when this grade was created.
     * @var float $grademax
     */
    var $grademax;

    /**
     * The minimum allowable grade when this grade was created.
     * @var float $grademin
     */
    var $grademin;

    /**
     * id of the scale, if this grade is based on a scale.
     * @var int $scaleid
     */
    var $scaleid;
   
    /**
     * A grade_scale object (referenced by $this->scaleid).
     * @var object $scale
     */
    var $scale;

    /**
     * The userid of the person who last modified this grade.
     * @var int $usermodified
     */
    var $usermodified;

    /**
     * Additional textual information about this grade. It can be automatically generated 
     * from the module or entered manually by the teacher. This is kept in its own table
     * for efficiency reasons, so it is encapsulated in its own object, and included in this raw grade object.
     * @var object $grade_grades_text
     */
    var $grade_grades_text;

    /**
     * Constructor. Extends the basic functionality defined in grade_object.
     * @param array $params Can also be a standard object.
     * @param boolean $fetch Wether or not to fetch the corresponding row from the DB.
     */
    function grade_grades_raw($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
    }
    
    /**
     * Instantiates a grade_scale object whose data is retrieved from the DB, 
     * if this raw grade's scaleid variable is set.
     * @return object grade_scale
     */
    function load_scale() {
        if (!empty($this->scaleid)) {
            $this->scale = grade_scale::fetch('id', $this->scaleid);
            $this->scale->load_items();
        } 
        return $this->scale;
    }
    
    /**
     * Loads the grade_grades_text object linked to this grade (through the intersection of itemid and userid), and
     * saves it as a class variable for this final object.
     * @return object
     */
    function load_text() {
        if (empty($this->grade_grades_text)) {
            return $this->grade_grades_text = grade_grades_text::fetch('itemid', $this->itemid, 'userid', $this->userid);
        } 
    }

    /**
     * Finds and returns a grade_grades_raw object based on 1-3 field values.
     * @static
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_category object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($object = get_record('grade_grades_raw', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_grades_raw') {
                foreach ($object as $param => $value) {
                    $this->$param = $value;
                }

                return $this;
            } else {
                $object = new grade_grades_raw($object);

                $object->load_text();
                return $object;
            }
        } else {
            return false;
        }
    } 
    
    /**
     * Updates this grade with the given textual information. This will create a new grade_grades_text entry
     * if none was previously in DB for this raw grade, or will update the existing one.
     * @param string $information Further info like forum rating distribution 4/5/7/0/1
     * @param int $informationformat Text format for information
     * @param string $feedback Manual feedback from the teacher. Could be a code like 'mi'.
     * @param int $feedbackformat Text format for the feedback
     * @return boolean Success or Failure
     */
    function annotate($information, $informationformat=FORMAT_PLAIN, $feedback=NULL, $feedbackformat=FORMAT_PLAIN) {
        $grade_text = new grade_grades_text();

        $grade_text->gradesid          = $this->id;
        $grade_text->information       = $information;
        $grade_text->informationformat = $informationformat;
        $grade_text->feedback          = $feedback;
        $grade_text->feedbackformat    = $feedbackformat;

        $result = true;

        if (empty($this->text)) {
            $result = $grade_text->insert();
        } else {
            $result = $grade_text->update();
        }

        $this->text = $grade_text;

        return $result;
    }

    /**
     * In addition to the normal updating set up in grade_object, this object also records
     * its pre-update value and its new value in the grade_history table.
     *
     * @param float $newgrade The new gradevalue of this object
     * @param string $howmodified What caused the modification? manual/module/import/cron...
     * @param string $note A note attached to this modification.
     * @return boolean Success or Failure.
     */
    function update($newgrade, $howmodified='manual', $note=NULL) {
        global $USER;
        $oldgrade = $this->gradevalue;
        $this->gradevalue = $newgrade;
        
        if (!empty($this->scale->id)) {
            $this->scaleid = $this->scale->id;
            $this->grademin = 0;
            $this->scale->load_items();
            $this->grademax = count($this->scale->scale_items);
        }

        $result = parent::update();
       
        // Update grade_grades_text if changed
        if (!empty($this->text)) {
            $grade_text = grade_grades_text::fetch('gradesid', $this->id);
            if ($this->text != $grade_text && $this->text->id == $grade_text->id) {
                $result = $result & $this->text->update();
            }
        }

        if ($result) {
            // TODO Handle history recording error, such as displaying a notice, but still return true
            grade_history::insert_change($this, $oldgrade, $howmodified, $note);
            return true;
        } else {
            return false;
        } 
    }
    
    /**
     * In addition to perform parent::insert(), this infers the grademax from the scale if such is given, and
     * sets grademin to 0 if scale is given.
     * @return int ID of the new grades_grade_raw record.
     */
    function insert() {
        // Retrieve scale and infer grademax from it
        if (!empty($this->scaleid)) {
            $this->load_scale();
            $this->scale->load_items();
            $this->grademax = count ($this->scale->scale_items);
            $this->grademin = 0;
        }

        return parent::insert();
    }
}

?>

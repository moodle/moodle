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

/**
 * A text string used to compute the value displayed by a grade_item.
 * There can be only one grade_text per grade_item (one-to-one).
 */
class grade_grades_text extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_grades_text';

    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields');
    
    /**
     * The grade_item.id this text refers to.
     * @var int $itemid
     */
    var $itemid;

    /**
     * The grade_item object referenced by $this->itemid.
     * @var object $grade_item
     */
    var $grade_item;

    /**
     * The user.id this text refers to.
     * @var int $userid
     */
    var $userid;

    /**
     * Further information like forum rating distribution 4/5/7/0/1
     * @var string $information
     */
    var $information;

    /**
     * Text format for information (FORMAT_PLAIN, FORMAT_HTML etc...).
     * @var int $informationformat
     */
    var $informationformat;

    /**
     * Manual feedback from the teacher. This could be a code like 'mi'.
     * @var string $feedback
     */
    var $feedback;

    /**
     * Text format for feedback (FORMAT_PLAIN, FORMAT_HTML etc...).
     * @var int $feedbackformat
     */
    var $feedbackformat;

    /**
     * The userid of the person who last modified this text.
     * @var int $usermodified
     */
    var $usermodified;
    
    /**
     * Finds and returns a grade_text object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_text object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_text = get_record('grade_grades_text', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_grades_text') {
                foreach ($grade_text as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $grade_text = new grade_grades_text($grade_text);
                return $grade_text;
            }
        } else {
            debugging("No grade_grades_text matching your criteria in the database.");
            return false;
        }
    }

    /**
     * Loads the grade_item object referenced by $this->itemid and saves it as $this->grade_item for easy access.
     * @return object grade_item.
     */
    function load_grade_item() {
        if (empty($this->grade_item) && !empty($this->itemid)) {
            $this->grade_item = grade_item::fetch('id', $this->itemid);
        }
        return $this->grade_item;
    }
}
?>

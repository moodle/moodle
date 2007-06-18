<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2007  Martin Dougiamas  http://dougiamas.com       //
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
 * A calculation string used to compute the value displayed by a grade_item.
 * There can be only one grade_calculation per grade_item (one-to-one).
 */
class grade_calculation extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_calculations';

    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'formula', 'useditems', 'grade_item');

    /**
     * A reference to the grade_item this calculation belongs to.
     * @var int $itemid
     */
    var $itemid;

    /**
     * The string representation of the calculation.
     * @var string $calculation
     */
    var $calculation;

    /**
     * The userid of the person who last modified this calculation.
     * @var int $usermodified
     */
    var $usermodified;

    /**
     * Calculation formula object
     */
    var $formula;

    /**
     * List of other items this calculation depends on
     */
    var $useditems;

    /**
     * Grade item object
     */
    var $grade_item;

    /**
     * Get associated grade_item object
     * @return object
     */
    function get_grade_item() {
        return grade_item::fetch('id', $this->itemid);
    }

    /**
     * Applies the formula represented by this object. The parameteres are taken from final
     * grades of grade items in current course only.
     * @return boolean false if error
     */
    function compute() {
        global $CFG;
        require_once($CFG->libdir.'/mathslib.php');

        if (empty($this->id) or empty($this->itemid)) {
            debugging('Can not initialize calculation!');
            return false;
        }

        // init grade_item
        $this->grade_item = $this->get_grade_item();

        //init used items
        $this->useditems = $this->dependson();

        // init maths library
        $this->formula = new calc_formula($this->calculation);

        // where to look for final grades
        $gis = implode(',', array_merge($this->useditems, array($this->itemid)));

        $sql = "SELECT f.*
                  FROM {$CFG->prefix}grade_grades_final f, {$CFG->prefix}grade_items gi
                 WHERE gi.id = f.itemid AND gi.courseid={$this->grade_item->courseid} AND gi.id IN ($gis)
              ORDER BY f.userid";

        $return = true;

        if ($rs = get_recordset_sql($sql)) {
            if ($rs->RecordCount() > 0) {
                $prevuser = 0;
                $grades   = array();
                $final    = null;
                while ($subfinal = rs_fetch_next_record($rs)) {
                    if ($subfinal->userid != $prevuser) {
                        if (!$this->_use_formula($prevuser, $grades, $final)) {
                            $return = false;
                        }
                        $prevuser = $subfinal->userid;
                        $grades   = array();
                        $final    = null;
                    }
                    if ($subfinal->itemid == $this->grade_item->id) {
                        $final = grade_grades_final::fetch('id', $subfinal->id);
                    }
                    $grades['gi'.$subfinal->itemid] = $subfinal->gradevalue;
                }
                if (!$this->_use_formula($prevuser, $grades, $final)) {
                    $return = false;
                }
            }
        }

        return $return;
    }

    /**
     * internal function - does the final grade calculation
     */
    function _use_formula($userid, $params, $final) {
        if (empty($userid)) {
            return true;
        }

        // add missing final grade values - use 0
        foreach($this->useditems as $gi) {
            if (!array_key_exists('gi'.$gi, $params)) {
                $params['gi'.$gi] = 0;
            } else {
                $params['gi'.$gi] = (float)$params['gi'.$gi];
            }
        }

        // can not use own final grade during calculation
        unset($params['gi'.$this->grade_item->id]);

        // do the calculation
        $this->formula->set_params($params);
        $result = $this->formula->evaluate();


        // insert final grade if needed
        if (empty($final)) {
            $final = new grade_grades_final(array('itemid'=>$this->grade_item->id, 'userid'=>$userid), false);
            $final->insert();
        }

        // store the result
        if ($result === false) {
            $final->gradevalue = null;
            $final->update();
            return false;

        } else {
            // normalize
            $result = bounded_number($this->grade_item->grademin, $result, $this->grade_item->grademax);
            if ($this->grade_item->gradetype == GRADE_TYPE_SCALE) {
                $result = round($result+0.00001); // round upwards
            }

            $final->gradevalue = $result;
            $final->update();
            return true;
        }
    }

    /**
     * Finds out on which other items does this calculation depend
     * @return array of grade_item ids this one depends on
     */
    function dependson() {
        preg_match_all('/gi([0-9]+)/i', $this->calculation, $matches);
        return ($matches[1]);
    }

    /**
     * Finds and returns a grade_calculation object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_calculation object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") {
        if ($grade_calculation = get_record('grade_calculations', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_calculation') {
                print_object($this);
                foreach ($grade_calculation as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $grade_calculation = new grade_calculation($grade_calculation);
                return $grade_calculation;
            }
        } else {
            return false;
        }
    }
}
?>

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
global $db;
/**
 * Class representing a grade item. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_item extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_items';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'calculation', 'grade_grades_raw', 'grade_grades_final', 'scale', 'category', 'outcome');
  
    /**
     * The course this grade_item belongs to.
     * @var int $courseid
     */
    var $courseid;
    
    /**
     * The category this grade_item belongs to (optional).
     * @var int $categoryid 
     */
    var $categoryid;
    
    /**
     * The grade_category object referenced by $this->categoryid or $this->iteminstance (itemtype must be == 'category' in that case).
     * @var object $category 
     */
    var $category;
    
    /**
     * A grade_category object this item used to belong to before getting updated. Will be deleted shortly.
     * @var object $old_parent
     */
    var $old_parent;

    /**
     * The name of this grade_item (pushed by the module).
     * @var string $itemname
     */
    var $itemname;
    
    /**
     * e.g. 'mod', 'blocks', 'import', 'calculate' etc...
     * @var string $itemtype 
     */
    var $itemtype;
    
    /**
     * The module pushing this grade (e.g. 'forum', 'quiz', 'assignment' etc).
     * @var string $itemmodule
     */
    var $itemmodule;
    
    /**
     * ID of the item module
     * @var int $iteminstance
     */
    var $iteminstance;
    
    /**
     * Number of the item in a series of multiple grades pushed by an activity.
     * @var int $itemnumber
     */
    var $itemnumber;
    
    /**
     * Info and notes about this item.
     * @var string $iteminfo
     */
    var $iteminfo;

    /**
     * Arbitrary idnumber provided by the module responsible.
     * @var string $idnumber
     */
    var $idnumber;

    /**
     * The type of grade (0 = none, 1 = value, 2 = scale, 3 = text)
     * @var int $gradetype
     */
    var $gradetype;
    
    /**
     * Maximum allowable grade.
     * @var float $grademax
     */
    var $grademax;
    
    /**
     * Minimum allowable grade.
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
     * The id of the optional grade_outcome associated with this grade_item.
     * @var int $outcomeid
     */
    var $outcomeid;

    /**
     * The grade_outcome this grade is associated with, if applicable.
     * @var object $outcome
     */
    var $outcome;
    
    /**
     * grade required to pass. (grademin < gradepass <= grademax)
     * @var float $gradepass
     */
    var $gradepass;
    
    /**
     * Multiply all grades by this number.
     * @var float $multfactor
     */
    var $multfactor;
    
    /**
     * Add this to all grades.
     * @var float $plusfactor
     */
    var $plusfactor;
    
    /**
     * Sorting order of the columns.
     * @var int $sortorder
     */
    var $sortorder;
    
    /**
     * Date until which to hide this grade_item. If null, 0 or false, grade_item is not hidden. Hiding prevents viewing.
     * @var int $hidden
     */
    var $hidden;
    
    /**
     * Date until which to lock this grade_item. If null, 0 or false, grade_item is not locked. Locking prevents updating.
     * @var int $locked
     */
    var $locked = false;
    
    /**
     * Whether or not the module instance referred to by this grade_item has been deleted.
     * @var int $deleted
     */
    var $deleted;

    /**
     * If set, the whole column will be recalculated, then this flag will be switched off.
     * @var boolean $needsupdate
     */
    var $needsupdate;

    /**
     * Calculation string used for this item.
     * @var string $calculation
     */
    var $calculation;
    
    /**
     * Array of grade_grades_raw objects linked to this grade_item. They are indexed by userid.
     * @var array $grade_grades_raw
     */
    var $grade_grades_raw = array();

    /**
     * Array of grade_grades_final objects linked to this grade_item. They are indexed by userid.
     * @var array $grade_grades_final
     */
    var $grade_grades_final = array();

    /**
     * Constructor. Extends the basic functionality defined in grade_object.
     * @param array $params Can also be a standard object.
     * @param boolean $fetch Wether or not to fetch the corresponding row from the DB.
     */
    function grade_item($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
    }
   
    /**
     * In addition to update() as defined in grade_object, handle the grade_outcome and grade_scale objects.
     */
    function update() {
        // If item is flagged as deleted, only update that flag in DB. The other changes are ignored.
        if (!empty($this->deleted) && $this->deleted) {
            return set_field('grade_items', 'deleted', 1, 'id', $this->id);
        }

        if (!empty($this->outcome->id)) {
            $this->outcomeid = $this->outcome->id;
        }

        if (!isset($this->gradetype)) {
            $this->gradetype = GRADE_TYPE_VALUE;
        }

        if (empty($this->scaleid) and !empty($this->scale->id)) {
            $this->scaleid = $this->scale->id;
        }

        // Retrieve scale and infer grademax from it
        if ($this->gradetype == GRADE_TYPE_SCALE and !empty($this->scaleid)) {
            $this->load_scale();

            if (!method_exists($this->scale, 'load_items')) {
                debugging("The scale referenced by this grade_item ($this->scaleid) does not exist in the database. Grademax cannot be infered from the missing scale.");
                return false;
            }

            $this->scale->load_items();
            $this->grademax = count ($this->scale->scale_items);
            $this->grademin = 0;
        } else {
            $this->scaleid = NULL;
            $this->scale = NULL;
        }
        
        $qualifies = $this->qualifies_for_update();

        $result = parent::update();
        
        if ($result && $qualifies) {
            $category = $this->get_category();
            
            if (!empty($category)) {
                $result = $result && $category->flag_for_update();
            }
        }

        return $result;
    }

    /**
     * Compares the values held by this object with those of the matching record in DB, and returns
     * whether or not these differences are sufficient to justify an update of all parent objects.
     * This assumes that this object has an id number and a matching record in DB. If not, it will return false.
     * @return boolean
     */
    function qualifies_for_update() {
        if (empty($this->id)) {
            return false;
        }

        $db_item = new grade_item(array('id' => $this->id));
        
        $gradetypediff = $db_item->gradetype != $this->gradetype;
        $grademaxdiff = $db_item->grademax != $this->grademax;
        $grademindiff = $db_item->grademin != $this->grademin;
        $scaleiddiff = $db_item->scaleid != $this->scaleid;
        $outcomeiddiff = $db_item->outcomeid != $this->outcomeid;
        $multfactordiff = $db_item->multfactor != $this->multfactor;
        $plusfactordiff = $db_item->plusfactor != $this->plusfactor;
        $needsupdatediff = $db_item->needsupdate != $this->needsupdate;

        if ($gradetypediff || $grademaxdiff || $grademindiff || $scaleiddiff || $outcomeiddiff ||
            $multfactordiff || $plusfactordiff || $needsupdatediff) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Finds and returns a grade_item object based on 1-3 field values.
     *
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_item object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_item = get_record('grade_items', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_item') {
                foreach ($grade_item as $param => $value) {
                    $this->$param = $value;
                }

                return $this;
            } else {
                $grade_item = new grade_item($grade_item);
                return $grade_item;
            }
        } else { 
            return false;
        }
    }

    /**
     * If parent::delete() is successful, send flag_for_update message to parent category.
     * @return boolean Success or failure.
     */
    function delete() {
        $result = parent::delete();
        if ($result) {
            $category = $this->get_category();
            if (!empty($category)) {
                return $category->flag_for_update();
            }
        }
        return $result;
    }
    
    /**
     * In addition to perform parent::insert(), this calls the grade_item's category's (if applicable) flag_for_update() method.
     * @return int ID of the new grade_item record.
     */
    function insert() {
        global $CFG;

        if (!isset($this->gradetype)) {
            $this->gradetype = GRADE_TYPE_VALUE;
        }

        if (empty($this->scaleid) and !empty($this->scale->id)) {
            $this->scaleid = $this->scale->id;
        }

        // Retrieve scale and infer grademax from it
        if ($this->gradetype == GRADE_TYPE_SCALE and !empty($this->scaleid)) {
            $this->load_scale();
            $this->scale->load_items();
            $this->grademax = count ($this->scale->scale_items);
            $this->grademin = 0;
        } else {
            $this->scaleid = NULL;
            $this->scale = NULL;
        }

        // If not set, infer courseid from referenced category
        if (empty($this->courseid) && (!empty($this->iteminstance) || !empty($this->categoryid))) {
            $this->load_category();
            $this->courseid = $this->category->courseid;
        }
       
        // If sortorder not given, extrapolate one
        if (empty($this->sortorder)) {
            $last_sortorder = get_field_select('grade_items', 'MAX(sortorder)', '');
            if (!empty($last_sortorder)) {
                $this->sortorder = $last_sortorder + 1;
            } else {
                $this->sortorder = 1;
            }
        }

        // If not set, generate an idnumber from itemmodule and iteminstance
        if (empty($this->idnumber)) {
            if (!empty($this->itemmodule) && !empty($this->iteminstance)) {
                $this->idnumber = "$this->itemmodule.$this->iteminstance";
            } else { // No itemmodule or iteminstance, generate a random idnumber
                $this->idnumber = rand(0,9999999999); // TODO replace rand() with proper random generator 
            }
        }
        
        // If a grade_item already exists with these itemtype, itemmodule and iteminstance
        // but not itemnumber, generate an itemnumber.  
        if (empty($this->itemnumber) && !empty($this->itemtype) && !empty($this->itemmodule) && !empty($this->iteminstance)) {
            $existing_item = get_record('grade_items', 
                'iteminstance', $this->iteminstance, 
                'itemmodule', $this->itemmodule, 
                'itemtype', $this->itemtype);

            if (empty($existing_item->itemnumber)) {
                $existing_item->itemnumber = 0;
            }

            $this->itemnumber = $existing_item->itemnumber + 1;
        }


        $result = parent::insert();

        // Notify parent category of need to update. Note that a grade_item may not have a categoryid.
        if ($result) {
            $category = $this->get_category();
            if (!empty($category)) {
                if (!$category->flag_for_update()) {
                    debugging("Could not notify parent category of the need to update its final grades.");
                    return false;
                }
            }
        } else {
            debugging("Could not insert this grade_item in the database!");
        }

        return $result;
    }

    /**
     * Takes an array of grade_grades_raw objects, indexed by userid, and saves each as a raw grade
     * under this grade_item. This replaces any existing grades, after having logged each change in the history table.
     * @param array $raw_grades
     * @return boolean success or failure
     */
    function save_raw($raw_grades, $howmodified='module', $note=NULL) {
        if (!empty($raw_grades) && is_array($raw_grades)) {
            $this->load_raw();
            
            foreach ($raw_grades as $userid => $raw_grade) {
                if (!empty($this->grade_grades_raw[$userid])) {
                    $raw_grade->update($raw_grade->gradevalue, $howmodified, $note);
                } else {
                    $raw_grade->itemid = $this->id;
                    if ($raw_grade->gradevalue > $raw_grade->grademax) {
                        die("raw GRADE EXCEEDED grademax FIRST");
                    }
                    $raw_grade->insert();
                }

                $this->grade_grades_raw[$userid] = $raw_grade;
            }
        } else {
            debugging("The data given to grade_item::save_raw($raw_grades) was not valid, it must be an array of raw grades.");
            return false;
        }
    }

    /**
     * Once the raw_grades are imported or entered, this method uses the grade_item's calculation and rules to 
     * generate final grade entries in the DB.
     * @return array final grade objects (grade_grades_final).
     */
    function generate_final() {
        if (empty($this->grade_grades_raw)) {
            $this->load_raw();
        }
        
        $success = true;
        
        foreach ($this->grade_grades_raw as $raw_grade) {
            $final_grade = new grade_grades_final();
            $final_grade->gradevalue = $this->adjust_grade($raw_grade);
            $final_grade->itemid = $this->id;
            $final_grade->userid = $raw_grade->userid;
            
            if ($final_grade->gradevalue > $this->grademax) {
                debugging("FINAL GRADE EXCEEDED grademax FIRST");
                return false;
            }
            $success = $success & $final_grade->insert();
            $this->grade_grades_final[$final_grade->userid] = $final_grade;
        }
        
        return $success;
    }

    /**
     * Returns the locked state of this grade_item (if the grade_item is locked OR no specific
     * $userid is given) or the locked state of a specific grade within this item if a specific
     * $userid is given and the grade_item is unlocked.
     *
     * @param int $userid
     * @return boolean Locked state
     */
    function is_locked($userid=NULL) {
        if ($this->locked || empty($userid)) {
            return $this->locked; // This could be true or false (false only if no $userid given)
        } else {
            $final = $this->get_final($userid);
            return $final->locked;
        }
    }
    
    /**
     * Locks or unlocks this grade_item and (optionally) all its associated final grades. 
     * @param boolean $update_final Whether to update final grades too
     * @param boolean $new_state Optional new state. Will use inverse of current state otherwise.
     * @return int Number of final grades changed, or false if error occurred during update.
     */
    function toggle_locking($update_final=false, $new_state=NULL) {
        $this->locked = !$this->locked;
        
        if (!empty($new_state)) {
            $this->locked = $new_state;
        }

        if (!$this->update()) {
            debugging("Could not update this grade_item's locked state in the database.");
            return false;
        }
        
        $count = 0;
        
        if ($update_final) {
            $this->load_final();
            foreach ($this->grade_grades_final as $id => $final) {
                $final->locked = $this->locked;
                if (!$final->update()) {
                    debugging("Could not update this grade_item's final grade's locked state in the database.");
                    return false;
                }
                $count++;
            }
            $this->load_final();
        }

        return $count;
    }

    /**
     * Locks or unlocks this grade_item and (optionally) all its associated final grades. 
     * @param boolean $update_final Whether to update final grades too
     * @param boolean $new_state Optional new state. Will use inverse of current state otherwise.
     * @return int Number of final grades changed, or false if error occurred during update.
     */
    function toggle_hiding($update_final=false, $new_state=NULL) {
        $this->hidden = !$this->hidden;
        
        if (!empty($new_state)) {
            $this->hidden = $new_state;
        }

        if (!$this->update()) {
            debugging("Could not update this grade_item's hidden state in the database.");
            return false;
        }
        
        $count = 0;
        
        if ($update_final) {
            $this->load_final();
            foreach ($this->grade_grades_final as $id => $final) {
                $final->hidden = $this->hidden;
                if (!$final->update()) {
                    debugging("Could not update this grade_item's final grade's hidden state in the database.");
                    return false;
                }
                $count++;
            }
            $this->load_final();
        }

        return $count;
    }
    
    
    /**
     * Performs the necessary calculations on the grades_final referenced by this grade_item,
     * and stores the results in grade_grades_final. Performs this for only one userid if 
     * requested. Also resets the needs_update flag once successfully performed.
     *
     * @param int $userid
     * @return int Number of grades updated, or false if error
     */
    function update_final_grade($userid=NULL) {
        if (empty($this->grade_grades_final)) {
            $this->load_final();
        }
        if (empty($this->grade_grades_raw)) {
            $this->load_raw();
        }
        
        $count = 0;
        
        $grade_final_array = array();
        $grade_raw_array   = array();

        if (!empty($userid)) {
            $grade_final_array[$userid] = $this->grade_grades_final[$userid];
            $grade_raw_array[$userid] = $this->grade_grades_raw[$userid];
        } else {
            $grade_final_array = $this->grade_grades_final;
            $grade_raw_array = $this->grade_grades_raw;
        }

        // The following code assumes that there is a grade_final object in DB for every
        // grade_raw object. This assumption depends on the correct creation of grade_final entries.
        // This also assumes that the two arrays $this->grade_grades_raw and its final counterpart are
        // indexed by userid, not sequentially or by grade_id
        if (count($this->grade_grades_final) != count($this->grade_grades_raw)) {
            $this->generate_final();
        }

        foreach ($grade_raw_array as $userid => $raw) {
            $newgradevalue = $raw->gradevalue;
            
            if (!empty($this->calculation)) {
                $this->upgrade_calculation_to_object();
                $newgradevalue = $this->calculation->compute($raw->gradevalue);
            }
            
            $final = $this->grade_grades_final[$userid];

            $final->gradevalue = $this->adjust_grade($raw, $newgradevalue);
            
            if ($final->update()) {
                $count++;
            } else {
                debugging("Could not update a final grade in this grade_item.");
                return false;
            }
        }

        return $count;
    }

    /**
     * Use this when the calculation object is a stdClass (rare) and you need it to have full
     * object status (with methods and all).
     */
    function upgrade_calculation_to_object() {
        if (!is_a($this->calculation, 'grade_calculation')) {
            $this->calculation = new grade_calculation($this->calculation, false);
        }
    }

    /**
     * Given a float grade value or integer grade scale, applies a number of adjustment based on 
     * grade_item variables and returns the result.
     * @param object $grade_raw The raw object to compare with this grade_item's rules
     * @param mixed $gradevalue The new gradevalue (after calculations are performed).
     *                          If null, the raw_grade's gradevalue will be used.
     * @return mixed 
     */
    function adjust_grade($grade_raw, $gradevalue=NULL) {
        $raw_offset = 0;
        $item_offset = 0;
        
        if ($this->gradetype == GRADE_TYPE_VALUE) { // Dealing with numerical grade
            if (empty($gradevalue)) {
                $gradevalue = $grade_raw->gradevalue;
            }

        } elseif($this->gradetype == GRADE_TYPE_SCALE) { // Dealing with a scale value
            if (empty($gradevalue)) {
                $gradevalue = $grade_raw->gradevalue;
            }
            
            // In case the scale objects haven't been loaded, do it now
            if (empty($grade_raw->scale)) {
                $grade_raw->load_scale();
            }
            
            if (empty($this->scale)) {
                $this->load_scale();
            }

            $grade_raw->grademax = count($grade_raw->scale->scale_items) - 1;
            $this->grademax = count($this->scale->scale_items) - 1;
            $grade_raw->grademin = 0;
            $this->grademin = 0;

        } elseif ($this->gradetype != GRADE_TYPE_TEXT) { // Something's wrong, the raw grade has no value!?
            return "Error: The gradeitem did not have a valid gradetype value, was $this->gradetype instead";
        }
           
        // Standardise score to the new grade range
        $gradevalue = standardise_score($gradevalue, $grade_raw->grademin, 
                $grade_raw->grademax, $this->grademin, $this->grademax);

        // Apply factors, depending on whether it's a scale or value
        if ($this->gradetype == GRADE_TYPE_VALUE) {
            // Apply other grade_item factors
            $gradevalue *= $this->multfactor;
            $gradevalue += $this->plusfactor;
        }        
        return $gradevalue;
    } 
    
    /**
     * Sets this grade_item's needsupdate to true. Also looks at parent category, if any, and calls
     * its flag_for_update() method.
     * This is triggered whenever any change in any grade_raw may cause grade_finals
     * for this grade_item to require an update. The flag needs to be propagated up all
     * levels until it reaches the top category. This is then used to determine whether or not
     * to regenerate the raw and final grades for each category grade_item.
     * @return boolean Success or failure
     */
    function flag_for_update() {
        $this->needsupdate = true;

        $result = $this->update();
        $category = $this->get_category();

        if (!empty($category)) {
            $result = $result && $category->flag_for_update();
        }

        return $result;
    }

    /**
     * Disassociates this item from its category parent(s). The object is then updated in DB.
     * @return boolean Success or Failure
     */
    function divorce_parent() {
        $this->old_parent = $this->get_category();
        $this->category = null;
        $this->categoryid = null;
        return $this->update();        
    }

    /**
     * Instantiates a grade_scale object whose data is retrieved from the DB, 
     * if this item's scaleid variable is set.
     * @return object grade_scale
     */
    function load_scale() {
        if (!empty($this->scaleid)) {
            $this->scale = grade_scale::fetch('id', $this->scaleid);
            if (method_exists($this->scale, 'load_items')) {
                $this->scale->load_items();
            } else { 
                $this->scale = null;
            } 
        } 
        return $this->scale;
    }

    /**
     * Instantiates a grade_outcome object whose data is retrieved from the DB, 
     * if this item's outcomeid variable is set.
     * @return object grade_outcome
     */
    function load_outcome() {
        if (!empty($this->outcomeid)) {
            $this->outcome = grade_outcome::fetch('id', $this->outcomeid);
        } 
        return $this->outcome;
    }
    
    /**
     * Loads all the grade_grades_raw objects for this grade_item from the DB into grade_item::$grade_grades_raw array.
     * @return array grade_grades_raw objects
     */      
    function load_raw() {
        $grade_raw_array = get_records('grade_grades_raw', 'itemid', $this->id);

        if (empty($grade_raw_array)) {
            return null;
        }

        foreach ($grade_raw_array as $r) {
            $this->grade_grades_raw[$r->userid] = new grade_grades_raw($r);
        }
        return $this->grade_grades_raw;
    }

    /**
     * Loads all the grade_grades_final objects for this grade_item from the DB into grade_item::$grade_grades_final array.
     * @param boolean $generatefakenullgrades If set to true, AND $CFG->usenullgrades is true, will replace missing grades with grades, gradevalue=grademin
     * @return array grade_grades_final objects
     */      
    function load_final($generatefakenullgrades=false) {
        global $CFG;

        $grade_final_array = get_records('grade_grades_final', 'itemid', $this->id);
        
        if (empty($grade_final_array)) {
            $this->generate_final();
            $grade_final_array = get_records('grade_grades_final', 'itemid', $this->id);
        }
        
        if (empty($grade_final_array)) {
            debugging("No final grades recorded for this grade_item");
            return false;
        }

        foreach ($grade_final_array as $f) {
            $this->grade_grades_final[$f->userid] = new grade_grades_final($f);
        }

        $returnarray = fullclone($this->grade_grades_final);

        // If we are generating fake null grades, we have to get a list of users
        if ($generatefakenullgrades && $CFG->usenullgrades) {
            $users = get_records_sql_menu('SELECT userid AS "user", userid FROM ' . $CFG->prefix . 'grade_grades_final GROUP BY userid ORDER BY userid');
            if (!empty($users) && is_array($users)) {
                foreach ($users as $userid) {
                    if (!isset($returnarray[$userid])) {
                        $fakefinal = new grade_grades_final();
                        $fakefinal->itemid = $this->id;
                        $fakefinal->userid = $userid;
                        $fakefinal->gradevalue = $this->grademin;
                        $returnarray[$userid] = $fakefinal;
                    }
                }
            }
        }

        return $returnarray;
    }

    /**
     * Returns an array of values (NOT objects) standardised from the final grades of this grade_item. They are indexed by userid.
     * @return array integers
     */
    function get_standardised_final() {
        $standardised_finals = array();

        $final_grades = $this->load_final(true);
        
        if (!empty($final_grades)) {
            foreach ($final_grades as $userid => $final) {
                $standardised_finals[$userid] = standardise_score($final->gradevalue, $this->grademin, $this->grademax, 0, 1);
            }
        }

        return $standardised_finals;
    }

    /**
    * Returns the grade_category object this grade_item belongs to (if any).
    * This category object may be the parent (referenced by categoryid) or the associated category 
    * (referenced by iteminstance).
    * 
    * @return mixed grade_category object if applicable, NULL otherwise
    */
    function get_category() {
        $category = null;
        
        if (!empty($this->categoryid)) {
            $category = grade_category::fetch('id', $this->categoryid);
        } elseif (!empty($this->iteminstance) && $this->itemtype == 'category') {
            $category = grade_category::fetch('id', $this->iteminstance);
        }
        
        return $category;
    }
    
    /**
     * Calls upon the get_category method to retrieve the grade_category object
     * from the DB and assigns it to $this->category. It also returns the object.
     * @return object Grade_category
     */
    function load_category() {
        $this->category = $this->get_category();
        return $this->category;
    }

    /**
     * Returns this object's calculation.
     * @param boolean $fetch Whether to fetch the value from the DB or not (false == just use the object's value)
     * @return mixed $calculation A string if found, false otherwise.
     */
    function get_calculation($fetch = false) {
        if (!$fetch && get_class($this->calculation) == 'grade_calculation') {
            return $this->calculation;
        } 
        $grade_calculation = grade_calculation::fetch('itemid', $this->id);
            
        if (empty($grade_calculation)) { // There is no calculation in DB
            return false;
        } elseif (empty($this->calculation) || !is_object($this->calculation)) { // The calculation isn't yet loaded
            $this->calculation = $grade_calculation;
            return $grade_calculation;
        } elseif ($grade_calculation->calculation != $this->calculation->calculation) { // The object's calculation is not in sync with the DB (new value??)
            $this->calculation = $grade_calculation;
            return $grade_calculation;
        } else { // The object's calculation is already in sync with the database
            return $this->calculation;
        }
    }

    /**
     * Sets this item's calculation (creates it) if not yet set, or
     * updates it if already set (in the DB). If no calculation is given,
     * the method will attempt to retrieve one from the Database, based on
     * the variables set in the current object.
     * @param string $calculation
     * @return boolean
     */
    function set_calculation($calculation = null) {
        if (empty($calculation)) { // We are setting this item object's calculation variable from the DB
            $grade_calculation = $this->get_calculation(true);
            if (empty($grade_calculation)) {
                debugging("No calculation to set for this grade_item.");
                return false;
            } else {
                $this->calculation = $grade_calculation;
            }
        } else { // We are updating or creating the calculation entry in the DB
            $grade_calculation = $this->get_calculation();
            
            if (empty($grade_calculation)) { // Creating
                $grade_calculation = new grade_calculation();
                $grade_calculation->calculation = $calculation;
                $grade_calculation->itemid = $this->id;

                if ($grade_calculation->insert()) {
                    $this->calculation = $grade_calculation;
                    return true;
                } else {
                    debugging("Could not save the calculation in the database, for this grade_item.");
                    return false;
                }                
            } else { // Updating
                $grade_calculation->calculation = $calculation;
                $grade_calculation = new grade_calculation($grade_calculation);
                $this->calculation = $grade_calculation;
                return $grade_calculation->update();
            }
        }
    }
    
    /**
     * Returns the raw values for this grade item (as imported by module or other source).
     * @param int $userid Optional: to retrieve a single raw grade
     * @return mixed An array of all raw_grades (stdClass objects) for this grade_item, or a single raw_grade.
     */
    function get_raw($userid=NULL) {
        if (empty($this->grade_grades_raw)) {
            $this->load_raw();
        }

        $grade_raw_array = null;
        if (!empty($userid)) {
            $r = get_record('grade_grades_raw', 'itemid', $this->id, 'userid', $userid);
            $grade_raw_array[$r->userid] = new grade_grades_raw($r);
        } else {
            $grade_raw_array = $this->grade_grades_raw;
        }
        return $grade_raw_array;
    }
    
    /**
     * Returns the final values for this grade item (as imported by module or other source).
     * @param int $userid Optional: to retrieve a single final grade
     * @return mixed An array of all final_grades (stdClass objects) for this grade_item, or a single final_grade.
     */
    function get_final($userid=NULL) {
        if (empty($this->grade_grades_final)) {
            $this->load_final();
        }

        $grade_final_array = null;
        if (!empty($userid)) {
            $f = get_record('grade_grades_final', 'itemid', $this->id, 'userid', $userid);
            $grade_final_array[$f->userid] = new grade_grades_final($f);
        } else {
            $grade_final_array = $this->grade_grades_final;
        }
        return $grade_final_array;
    }

    /**
     * Returns the sortorder of this grade_item. This method is also available in 
     * grade_category, for cases where the object type is not know. It will act as a virtual 
     * variable for a grade_category.
     * @return int Sort order
     */
    function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * Sets the sortorder of this grade_item. This method is also available in 
     * grade_category, for cases where the object type is not know. It will act as a virtual 
     * variable for a grade_category.
     * @param int $sortorder
     * @return void
     */
    function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used 
     * when we do not know the exact type of an object.
     * @return string name
     */
    function get_name() {
        return $this->itemname;
    } 
    
    /**
     * Returns this grade_item's id. This is specified for cases where we do not
     * know an object's type, and want to get either an item's id or a category's item's id.
     *
     * @return int
     */
    function get_item_id() {
        return $this->id;
    }

    /**
     * Returns this item's category id. A generic method shared by objects that have a parent id of some kind.
     * @return int $parentid
     */
    function get_parent_id() {
        return $this->categoryid;
    }

    /**
     * Sets this item's categoryid. A generic method shared by objects that have a parent id of some kind.
     * @param int $parentid
     */
    function set_parent_id($parentid) {
        $this->categoryid = $parentid;
    }
    
    /**
     * Returns the locked state/date of this grade_item. This method is also available in 
     * grade_category, for cases where the object type is not known. 
     * @return int 0, 1 or timestamp int(10)
     */
    function get_locked() {
        return $this->locked;
    }

    /**
     * Sets the grade_item's locked variable and updates the grade_item.
     * @param int $locked 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @return success or failure of update() method
     */
    function set_locked($locked) {
        $this->locked = $locked;
        return $this->update();
    }
    
    /**
     * Returns the hidden state/date of this grade_item. This method is also available in 
     * grade_category, for cases where the object type is not known. 
     * @return int 0, 1 or timestamp int(10)
     */
    function get_hidden() {
        return $this->hidden;
    }

    /**
     * Sets the grade_item's hidden variable and updates the grade_item. 
     * @param int $hidden 0, 1 or a timestamp int(10) after which date the item will be hidden.
     * @return void
     */
    function set_hidden($hidden) {
        $this->hidden = $hidden;
        return $this->update(); 
    }

    /** 
     * If the old parent is set (after an update), this checks and returns whether it has any children. Important for
     * deleting childless categories.
     * @return boolean
     */
    function is_old_parent_childless() {
        if (!empty($this->old_parent)) {
            return !$this->old_parent->has_children();
        } else {
            return false;
        }
    } 
}
?>

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
    var $nonfields = array('table', 'nonfields', 'calculation', 'scale', 'category', 'outcome');

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

        // all new grade_items must be recalculated
        $this->needsupdate = true;

        if (!isset($this->gradetype)) {
            $this->gradetype = GRADE_TYPE_VALUE;
        }

        if (empty($this->scaleid) and !empty($this->scale->id)) {
            $this->scaleid = $this->scale->id;
        }

        // Retrieve scale and infer grademax from it
        if ($this->gradetype == GRADE_TYPE_SCALE and !empty($this->scaleid)) {
            $this->load_scale();

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
     * Returns the locked state of this grade_item (if the grade_item is locked OR no specific
     * $userid is given) or the locked state of a specific grade within this item if a specific
     * $userid is given and the grade_item is unlocked.
     *
     * @param int $userid
     * @return boolean Locked state
     */
    function is_locked($userid=NULL) {
        // TODO: rewrite the item check

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
        // TODO: implement new locking

        return 0;
    }

    /**
     * Locks or unlocks this grade_item and (optionally) all its associated final grades.
     * @param boolean $update_final Whether to update final grades too
     * @param boolean $new_state Optional new state. Will use inverse of current state otherwise.
     * @return int Number of final grades changed, or false if error occurred during update.
     */
    function toggle_hiding($update_final=false, $new_state=NULL) {
        //TODO: implement new hiding

        return 0;
    }


    /**
     * Performs the necessary calculations on the grades_final referenced by this grade_item.
     * Also resets the needsupdate flag once successfully performed.
     *
     * This function must be use ONLY from lib/gradeslib.php/grade_update_final_grades(),
     * because the calculation must be done in correct order!!
     *
     * @return boolean true if ok, array of errors otherwise
     */
    function update_final_grades() {
        global $CFG;

        $errors = array();

        if ($calculation = $this->get_calculation()) {
            if ($calculation->compute()) {
                $this->needsupdate = false;
                $this->update();
                return true;
            } else {
                $errors[] = "Could not calculate grades for grade item id:".$this->id; // TODO: improve and localize
            }

        } else if ($this->itemtype == 'category') {
            // aggregate category grade item
            $category = $this->get_category();
            if (!$category->generate_grades()) {
                $errors[] = "Could not calculate category grade item id:".$this->id; // TODO: improve and localize
            }
        }

        // TODO: add locking support
        if ($rs = get_recordset('grade_grades', 'itemid', $this->id)) {
            if ($rs->RecordCount() > 0) {
                while ($grade = rs_fetch_next_record($rs)) {
                    if (!empty($errors) or is_null($grade->rawgrade)) {
                        // unset existing final grade when no raw present or error
                        if (!is_null($grade->finalgrade)) {
                            $g = new object();
                            $g->id         = $grade->id;
                            $g->finalgrade = null;
                            if (!update_record('grade_grades', $g)) {
                                $errors[] = "Could not remove final grade for grade item:".$this->id;
                            }
                        }

                    } else {
                        $finalgrade = $this->adjust_grade($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);

                        if ($finalgrade != $grade->finalgrade) {
                            $g = new object();
                            $g->id         = $grade->id;
                            $g->finalgrade = $finalgrade;
                            if (!update_record('grade_grades', $g)) {
                                $errors[] = "Could not update final grade for grade item:".$this->id;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($errors)) {
            $this->flag_for_update();
            return $errors;

        } else {
            $this->needsupdate = false;
            $this->update();
            return true;
        }
    }

    /**
     * Given a float grade value or integer grade scale, applies a number of adjustment based on
     * grade_item variables and returns the result.
     * @param object $rawgrade The raw grade value.
     * @return mixed
     */
    function adjust_grade($rawgrade, $rawmin, $rawmax) {
        if (is_null($rawgrade)) {
            return null;
        }

        if ($this->gradetype == GRADE_TYPE_VALUE) { // Dealing with numerical grade

            if ($this->grademax < $this->grademin) {
                return null;
            }

            if ($this->grademax == $this->grademin) {
                return $this->grademax; // no range
            }

            // Standardise score to the new grade range
            // NOTE: this is not compatible with current assignment grading
            if ($rawmin != $this->grademin or $rawmax != $this->grademax) {
                $rawgrade = grade_grades::standardise_score($rawgrade, $rawmin, $rawmax, $this->grademin, $this->grademax);
            }

            // Apply other grade_item factors
            $rawgrade *= $this->multfactor;
            $rawgrade += $this->plusfactor;

            return bounded_number($this->grademin, $rawgrade, $this->grademax);

        } else if($this->gradetype == GRADE_TYPE_SCALE) { // Dealing with a scale value
            if (empty($this->scale)) {
                $this->load_scale();
            }

            if ($this->grademax < 0) {
                return null; // scale not present - no grade
            }

            if ($this->grademax == 0) {
                return $this->grademax; // only one option
            }

            // Convert scale if needed
            // NOTE: this is not compatible with current assignment grading
            if ($rawmin != $this->grademin or $rawmax != $this->grademax) {
                $rawgrade = grade_grades::standardise_score($rawgrade, $rawmin, $rawmax, $this->grademin, $this->grademax);
            }

            return (int)bounded_number(0, round($rawgrade+0.00001), $this->grademax);


        } else if ($this->gradetype == GRADE_TYPE_TEXT or $this->gradetype == GRADE_TYPE_NONE) { // no value
            // somebody changed the grading type when grades already existed
            return null;

        } else {
            dubugging("Unkown grade type");
            return null;;
        }


    }

    /**
     * Sets this grade_item's needsupdate to true. Also looks at parent category, if any, and calls
     * its flag_for_update() method.
     * This is triggered whenever any change in any raw grade may cause grade_finals
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
        if ($this->gradetype != GRADE_TYPE_SCALE) {
            $this->scaleid = null;
        }

        if (!empty($this->scaleid)) {
            $this->scale = grade_scale::fetch('id', $this->scaleid);
            $this->scale->load_items();
            $this->grademax = count($this->scale->scale_items) - 1;
            $this->grademin = 0;
        } else {
            $this->scale = null;
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
     * @return mixed $calculation Object if found, false otherwise.
     */
    function get_calculation($nocache = false) {
        if (is_null($this->calculation)) {
            $nocache = true;
        }

        if ($nocache) {
            $this->calculation = grade_calculation::fetch('itemid', $this->id);
        }

        return $this->calculation;
    }

    /**
     * Sets this item's calculation (creates it) if not yet set, or
     * updates it if already set (in the DB). If no calculation is given,
     * the calculation is removed.
     * @param string $formula
     * @return boolean
     */
    function set_calculation($formula) {
        // remove cached calculation object
        if (empty($formula)) { // We are removing this calculation
            if (!empty($this->id)) {
                if ($grade_calculation = $this->get_calculation(true)) {
                    $grade_calculation->delete();
                }
            }
            $this->calculation = false; // cache no calculation present
            $this->flag_for_update();
            return true;

        } else { // We are updating or creating the calculation entry in the DB
            if ($grade_calculation = $this->get_calculation(true)) {
                $grade_calculation->calculation = $formula;
                if ($grade_calculation->update()) {
                    $this->flag_for_update();
                    return true;
                } else {
                    $this->calculation = null; // remove cache
                    debugging("Could not save the calculation in the database for this grade_item.");
                    return false;
                }

            } else {
                $grade_calculation = new grade_calculation();
                $grade_calculation->calculation = $formula;
                $grade_calculation->itemid = $this->id;

                if ($grade_calculation->insert()) {
                    return true;
                } else {
                    $this->calculation = null; // remove cache
                    debugging("Could not save the calculation in the database for this grade_item.");
                    return false;
                }
            }
        }
    }

    /**
     * Returns the final values for this grade item (as imported by module or other source).
     * @param int $userid Optional: to retrieve a single final grade
     * @return mixed An array of all final_grades (stdClass objects) for this grade_item, or a single final_grade.
     */
    function get_final($userid=NULL) {
        if ($userid) {
            if ($user = get_record('grade_grades', 'itemid', $this->id, 'userid', $userid)) {
                return $user;
            }

        } else {
            if ($grades = get_records('grade_grades', 'itemid', $this->id)) {
                //TODO: speed up with better SQL
                $result = array();
                foreach ($grades as $grade) {
                    $result[$grade->userid] = $grade;
                }
                return $result;
            } else {
                return array();
            }
        }
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

    /**
     * Finds out on which other items does this depend directly when doing calculation or category agregation
     * @return array of grade_item ids this one depends on
     */
    function dependson() {

        if ($calculation = $this->get_calculation()) {
            return $calculation->dependson();

        } else if ($this->itemtype == 'category') {
            $grade_category = grade_category::fetch('id', $this->iteminstance);
            $children = $grade_category->get_children(1, 'flat');

            if (empty($children)) {
                return array();
            }

            $result = array();

            $childrentype = get_class(reset($children));
            if ($childrentype == 'grade_category') {
                foreach ($children as $id => $category) {
                    $grade_item = $category->get_grade_item();
                    $result[] = $grade_item->id;
                }
            } elseif ($childrentype == 'grade_item') {
                foreach ($children as $id => $grade_item) {
                    $result[] = $grade_item->id;
                }
            }

            return $result;

        } else {
            return array();
        }
    }

    /**
     * Updates raw grade value for given user, this is a only way to update raw
     * grades from external source (module, gradebook, import, etc.),
     * because it logs the change in history table and deals with final grade recalculation.
     *
     * The only exception is category grade item which stores the raw grades directly.
     * Calculated grades do not use raw grades at all, the rawgrade changes there are not logged too.
     *
     * @param int $userid the graded user
     * @param float $rawgrade value of raw grade
     * @param string $howmodified modification source
     * @param string $note optional note
     * @param string $feedback teachjers feedback
     * @param int $feedbackformat
     * @return boolean true if ok, false if error
     */
    function update_raw($userid, $rawgrade=false, $howmodified='manual', $note=NULL, $feedback=false, $feedbackformat=FORMAT_MOODLE) {
        $grade = new grade_grades(array('itemid'=>$this->id, 'userid'=>$userid));

        //TODO: add locking checks here - prevent update if item or individaul grade locked
        //TODO: if grade tree does not need to be recalculated, try to update all users grades in course and flag_for_update only if failed

        // fist copy current grademin/max and scale
        $grade->rawgrademin = $this->grademin;
        $grade->rawgrademax = $this->grademax;
        $grade->rawscaleid  = $this->scaleid;

        if ($rawgrade !== false) {
            // change of grade value requested
            if (empty($grade->id)) {
                $oldgrade = null;
                $grade->rawgrade = $rawgrade;
                $result = $grade->insert();

            } else {
                $oldgrade = $grade->rawgrade;
                $grade->rawgrade = $rawgrade;
                $result = $grade->update();
            }
        }

        // do we have comment from teacher?
        if ($result and $feedback !== false) {
            if (empty($grade->id)) {
                // create new grade
                $oldgrade = null;
                $result = $grade->insert();
            }
            $result = $result && $grade->update_feedback($feedback, $feedbackformat);
        }

        // TODO Handle history recording error, such as displaying a notice, but still return true
        grade_history::insert_change($userid, $this->id, $grade->rawgrade, $oldgrade, $howmodified, $note);

        // This grade item needs update
        $this->flag_for_update();

        if ($result) {
            return $grade;
        } else {
            return false;
        }
    }
}
?>

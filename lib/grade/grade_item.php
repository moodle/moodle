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
    var $nonfields = array('table', 'nonfields', 'formula', 'calculation_normalized', 'scale', 'item_category', 'parent_category', 'outcome');

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
     * The grade_category object referenced $this->iteminstance (itemtype must be == 'category' or == 'course' in that case).
     * @var object $item_category
     */
    var $item_category;

    /**
     * The grade_category object referenced by $this->categoryid.
     * @var object $parent_category
     */
    var $parent_category;


    /**
     * The name of this grade_item (pushed by the module).
     * @var string $itemname
     */
    var $itemname;

    /**
     * e.g. 'category', 'course' and 'mod', 'blocks', 'import', etc...
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
     * Calculation string used for this item.
     * @var string $calculation
     */
    var $calculation;

    /**
     * Indicates if we already tried to normalize the grade calculation formula.
     * This flag helps to minimize db access when broken formulas used in calculation.
     * @var boolean
     */
    var $calculation_normalized;
    /**
     * Math evaluation object
     */
    var $formula;

    /**
     * The type of grade (0 = none, 1 = value, 2 = scale, 3 = text)
     * @var int $gradetype
     */
    var $gradetype = GRADE_TYPE_VALUE;

    /**
     * Maximum allowable grade.
     * @var float $grademax
     */
    var $grademax = 100;

    /**
     * Minimum allowable grade.
     * @var float $grademin
     */
    var $grademin = 0;

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
     * grade required to pass. (grademin <= gradepass <= grademax)
     * @var float $gradepass
     */
    var $gradepass = 0;

    /**
     * Multiply all grades by this number.
     * @var float $multfactor
     */
    var $multfactor = 1.0;

    /**
     * Add this to all grades.
     * @var float $plusfactor
     */
    var $plusfactor = 0;

    /**
     * Sorting order of the columns.
     * @var int $sortorder
     */
    var $sortorder = 0;

    /**
     * 0 if visible, 1 always hidden or date not visible until
     * @var int $hidden
     */
    var $hidden = 0;

    /**
     * Grade item lock flag. Enmpty if not locked, lcoked if any value presetn ,usually date when was locked. Locking prevents updating.
     * @var int $locked
     */
    var $locked = 0;

    /**
     * Date when to lock the grade. Empty means no automatic locking.
     * @var int $locktime
     */
    var $locktime = 0;

    /**
     * Whether or not the module instance referred to by this grade_item has been deleted.
     * @var int $deleted
     */
    var $deleted = 0;

    /**
     * If set, the whole column will be recalculated, then this flag will be switched off.
     * @var boolean $needsupdate
     */
    var $needsupdate = 0;

    /**
     * In addition to update() as defined in grade_object, handle the grade_outcome and grade_scale objects.
     * Force regrading if necessary
     *
     * @return boolean success
     */
    function update() {

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

        if ($this->qualifies_for_regrading()) {
            return $this->force_regrading();

        } else {
            return parent::update();
        }
    }

    /**
     * Compares the values held by this object with those of the matching record in DB, and returns
     * whether or not these differences are sufficient to justify an update of all parent objects.
     * This assumes that this object has an id number and a matching record in DB. If not, it will return false.
     * @return boolean
     */
    function qualifies_for_regrading() {
        if (empty($this->id)) {
            return false;
        }

        $db_item = new grade_item(array('id' => $this->id));

        $calculationdiff = $db_item->calculation != $this->calculation;
        $categorydiff    = $db_item->categoryid  != $this->categoryid;
        $gradetypediff   = $db_item->gradetype   != $this->gradetype;
        $grademaxdiff    = $db_item->grademax    != $this->grademax;
        $grademindiff    = $db_item->grademin    != $this->grademin;
        $scaleiddiff     = $db_item->scaleid     != $this->scaleid;
        $outcomeiddiff   = $db_item->outcomeid   != $this->outcomeid;
        $multfactordiff  = $db_item->multfactor  != $this->multfactor;
        $plusfactordiff  = $db_item->plusfactor  != $this->plusfactor;
        $deleteddiff     = $db_item->deleted     != $this->deleted;

        $needsupdatediff = !$db_item->needsupdate &&  $this->needsupdate;    // force regrading only if setting the flag first time
        $lockeddiff      = !empty($db_item->locked) && empty($this->locked); // force regrading only when unlocking

        return ($calculationdiff || $categorydiff || $gradetypediff || $grademaxdiff || $grademindiff || $scaleiddiff
             || $outcomeiddiff || $multfactordiff || $plusfactordiff || $deleteddiff || $needsupdatediff
             || $lockeddiff);
    }

    /**
     * Finds and returns a grade_item instance based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_item instance or false if none found.
     */
    function fetch($params) {
        return grade_object::fetch_helper('grade_items', 'grade_item', $params);
    }

    /**
     * Finds and returns all grade_item instances based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_item insatnces or false if none found.
     */
    function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_items', 'grade_item', $params);
    }

    /**
     * If parent::delete() is successful, send force_regrading message to parent category.
     * @return boolean Success or failure.
     */
    function delete() {
        if ($this->is_course_item()) {
            debuggin('Can not delete course or category item!');
            return false;
        }

        if (!$this->is_category_item() and $category = $this->get_parent_category()) {
            $category->force_regrading();
        }

        return parent::delete();;
    }

    /**
     * In addition to perform parent::insert(), this calls the grade_item's category's (if applicable) force_regrading() method.
     * @return int ID of the new grade_item record.
     */
    function insert() {
        global $CFG;

        if (empty($this->courseid)) {
            error('Can not insert grade item without course id!');
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

        if (empty($this->categoryid) and !$this->is_course_item() and !$this->is_category_item()) {
            $course_category = grade_category::fetch_course_category($this->courseid);
            $this->categoryid = $course_category->id;

        }

        // always place the new items at the end, move them after insert if needed
        $last_sortorder = get_field_select('grade_items', 'MAX(sortorder)', "courseid = {$this->courseid}");
        if (!empty($last_sortorder)) {
            $this->sortorder = $last_sortorder + 1;
        } else {
            $this->sortorder = 1;
        }

        // If not set, generate an idnumber from itemmodule and iteminstance
        if (empty($this->idnumber)) {
            if (!empty($this->itemmodule) && !empty($this->iteminstance)) {
                $this->idnumber = "$this->itemmodule.$this->iteminstance";
            } else { // No itemmodule or iteminstance, generate a random idnumber
                $this->idnumber = rand(0,9999999999); // TODO replace rand() with proper random generator
            }
        }
/*
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
*/

        $result = parent::insert();

        if ($result) {
            // force regrading of items if needed
            $this->force_regrading();
            return true;

        } else {
            debugging("Could not insert this grade_item in the database!");
            return false;
        }
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
        if (!empty($this->locked)) {
            return true;
        }

        if (!empty($userid)) {
            if ($grade = grade_grades::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
                $grade->grade_item =& $this; // prevent db fetching of cached grade_item
                return $grade->is_locked();
            }
        }

        return false;
    }

    /**
     * Locks or unlocks this grade_item and (optionally) all its associated final grades.
     * @param boolean $update_final Whether to update final grades too
     * @param boolean $new_state Optional new state. Will use inverse of current state otherwise.
     * @return boolean true if grade_item all grades updated, false if at least one update fails
     */
    function set_locked($lockedstate) {
        if ($lockedstate) {
        /// setting lock
            if (!empty($this->locked)) {
                return true; // already locked
            }

            if ($this->needsupdate) {
                return false; // can not lock grade without first calculating final grade
            }

            $this->locked = time();
            $this->update();

            // this could be improved with direct SQL update
            $result = true;
            $grades = $this->get_final();
            foreach($grades as $g) {
                $grade = new grade_grades($g, false);
                $grade->grade_item =& $this;
                if (!$grade->set_locked(true)) {
                    $result = false;
                }
            }

            return $result;

        } else {
        /// removing lock
            if (empty($this->locked)) {
                return true; // not locked
            }

            if (!empty($this->locktime) and $this->locktime < time()) {
                return false; // can not unlock grade item that should be already locked
            }

            $this->locked = 0;
            $this->update();

            // this could be improved with direct SQL update
            $result = true;
            if ($grades = grade_grades::fetch_all(array('itemid'=>$this->id))) {
                foreach($grades as $grade) {
                    $grade->grade_item =& $this;

                    if (!empty($grade->locktime) and $grade->locktime < time()) {
                        $result = false; // can not unlock grade that should be already locked
                    }

                    if (!$grade->set_locked(false)) {
                        $result = false;
                    }
                }
            }

            return $result;

        }
    }

    /**
     * Returns the hidden state of this grade_item (if the grade_item is hidden OR no specific
     * $userid is given) or the hidden state of a specific grade within this item if a specific
     * $userid is given and the grade_item is unhidden.
     *
     * @param int $userid
     * @return boolean hidden state
     */
    function is_hidden($userid=NULL) {
        if ($this->hidden == 1 or $this->hidden > time()) {
            return true;
        }

        if (!empty($userid)) {
            if ($grade = grade_grades::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
                $grade->grade_item =& $this; // prevent db fetching of cached grade_item
                return $grade->is_hidden();
            }
        }

        return false;
    }

    /**
     * Set the hidden status of grade_item and all grades, 0 mean visible, 1 always hidden, number means date to hide until.
     * @param int $hidden new hidden status
     * @return void
     */
    function set_hidden($hidden) {
        $this->hidden = $hidden;
        $this->update();

        if ($grades = grade_grades::fetch_all(array('itemid'=>$this->id))) {
            foreach($grades as $grade) {
                $grade->grade_item =& $this;
                $grade->set_hidden($hidden);
            }
        }
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

        if ($this->is_locked()) {
            // locked grade items already have correct final grades
            $this->needsupdate = false;
            $this->update();
            return true;
        }

        if ($this->is_calculated()) {
            if ($this->compute()) {
                $this->needsupdate = false;
                $this->update();
                return true;
            } else {
                return array("Could not calculate grades for grade item id:".$this->id); // TODO: improve and localize
            }

        } else if ($this->is_category_item() or $this->is_course_item()) {
            // aggregate category grade item
            $category = $this->get_item_category();
            if (!$category->generate_grades()) {
                return ("Could not calculate raw category grades id:".$this->id); // TODO: improve and localize
            }
        }

        $errors = array();

         // we need it to be really fast here ==> sql only
        if ($rs = get_recordset('grade_grades', 'itemid', $this->id)) {
            if ($rs->RecordCount() > 0) {
                while ($grade = rs_fetch_next_record($rs)) {
                    if (!empty($grade->locked)) {
                        // this grade is locked - final grade must be ok
                        continue;
                    }

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

                        // do not use $grade->is_locked() bacause item may be still locked!
                        if (!empty($grade->locktime) and empty($grade->locked) and $grade->locktime < time()) {
                            // time to lock this grade
                            $g = new object();
                            $g->id     = $grade->id;
                            $g->locked = time();
                            update_record('grade_grades', $g);
                        }
                    }
                }
            }
        }

        if (!empty($errors)) {
            $this->force_regrading();
            return $errors;

        } else {
            // reset the regrading flag
            $this->needsupdate = false;
            $this->update();

            // recheck the needsupdate just to make sure ;-)
            if (empty($this->needsupdate) and !empty($this->locktime)
                and empty($this->locked) and $this->locktime < time()) {
                // time to lock this grade_item
                $this->set_locked(true);
            }

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
     * its force_regrading() method.
     * This is triggered whenever any change in any raw grade may cause grade_finals
     * for this grade_item to require an update. The flag needs to be propagated up all
     * levels until it reaches the top category. This is then used to determine whether or not
     * to regenerate the raw and final grades for each category grade_item.
     * @return boolean Success or failure
     */
    function force_regrading() {
        $this->needsupdate = true;

        if (!parent::update()) {
            return false;
        }

        if ($this->is_course_item()) {
            // no parent

        } else {
            $parent = $this->load_parent_category();
            $parent->force_regrading();

        }

        return true;
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
            $this->scale = grade_scale::fetch(array('id'=>$this->scaleid));
            $this->scale->load_items();

            // Until scales are uniformly set to min=0 max=count(scaleitems)-1 throughout Moodle, we
            // stay with the current min=1 max=count(scaleitems)
            $this->grademax = count($this->scale->scale_items);
            $this->grademin = 1;
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
            $this->outcome = grade_outcome::fetch(array('id'=>$this->outcomeid));
        }
        return $this->outcome;
    }

    /**
    * Returns the grade_category object this grade_item belongs to (referenced by categoryid)
    * or category attached to category item.
    *
    * @return mixed grade_category object if applicable, false if course item
    */
    function get_parent_category() {
        if ($this->is_category_item() or $this->is_course_item()) {
            return $this->get_item_category();

        } else {
            return grade_category::fetch(array('id'=>$this->categoryid));
        }
    }

    /**
     * Calls upon the get_parent_category method to retrieve the grade_category object
     * from the DB and assigns it to $this->parent_category. It also returns the object.
     * @return object Grade_category
     */
    function load_parent_category() {
        if (empty($this->parent_category->id)) {
            $this->parent_category = $this->get_parent_category();
        }
        return $this->parent_category;
    }

    /**
    * Returns the grade_category for category item
    *
    * @return mixed grade_category object if applicable, false otherwise
    */
    function get_item_category() {
        if (!$this->is_course_item() and !$this->is_category_item()) {
            return false;
        }
        return grade_category::fetch(array('id'=>$this->iteminstance));
    }

    /**
     * Calls upon the get_item_category method to retrieve the grade_category object
     * from the DB and assigns it to $this->item_category. It also returns the object.
     * @return object Grade_category
     */
    function load_item_category() {
        if (empty($this->category->id)) {
            $this->item_category = $this->get_item_category();
        }
        return $this->item_category;
    }

    function is_category_item() {
        return ($this->itemtype == 'category');
    }

    function is_course_item() {
        return ($this->itemtype == 'course');
    }

    function fetch_course_item($courseid) {
        if ($course_item = grade_item::fetch(array('courseid'=>$courseid, 'itemtype'=>'course'))) {
            return $course_item;
        }

        // first call - let category insert one
        $course_category = grade_category::fetch_course_category($courseid);

        return grade_item::fetch(array('courseid'=>$courseid, 'itemtype'=>'course'));
    }

    /**
     * Checks if grade calculated. Returns this object's calculation.
     * @return boolean true if grade item calculated.
     */
    function is_calculated() {
        if (empty($this->calculation)) {
            return false;
        }

        /*
         * The main reason why we use the [#gixxx#] instead of [idnumber] is speed of depends_on(),
         * we would have to fetch all course grade items to find out the ids.
         * Also if user changes the idnumber the formula does not need to be updated.
         */

        // first detect if we need to update calculation formula from [idnumber] to [#giXXX#] (after backup, etc.)
        if (!$this->calculation_normalized and preg_match_all('/\[(?!#gi)(.*?)\]/', $this->calculation, $matches)) {
            foreach ($matches[1] as $idnumber) {
                if ($grade_item = grade_item::fetch(array('courseid'=>$this->courseid, 'idnumber'=>$idnumber))) {
                    $this->calculation = str_replace('['.$grade_item->idnumber.']', '[#gi'.$grade_item->id.'#]', $this->calculation);
                }
            }
            $this->update(); // update in db if needed
            $this->calculation_normalized = true;
            return !empty($this->calculation);
        }

        return true;
    }

    /**
     * Returns calculation string if grade calculated.
     * @return mixed string if calculation used, null if not
     */
    function get_calculation() {
        if ($this->is_calculated()) {
            $formula = $this->calculation;
            // denormalize formula - convert [#giXX#] to [idnumber]
            if (preg_match_all('/\[#gi([0-9]+)#\]/', $formula, $matches)) {
                foreach ($matches[1] as $id) {
                    if ($grade_item = grade_item::fetch(array('id'=>$id))) {
                        if (!empty($grade_item->idnumber)) {
                            $formula = str_replace('[#gi'.$grade_item->id.'#]', '['.$grade_item->idnumber.']', $formula);
                        }
                    }
                }
            }

            return $formula;

        } else {
            return NULL;
        }
    }

    /**
     * Sets this item's calculation (creates it) if not yet set, or
     * updates it if already set (in the DB). If no calculation is given,
     * the calculation is removed.
     * @param string $formula string representation of formula used for calculation
     * @return boolean success
     */
    function set_calculation($formula) {
        $formula = trim($formula);

        if (empty($formula)) {
            $this->calculation = NULL;

        } else {
            if (strpos($formula, '=') !== 0) {
                $formula = '='.$formula;
            }

            // normalize formula - we want grade item ids [#giXXX#] instead of [idnumber]
            if ($grade_items = grade_item::fetch_all(array('courseid'=>$this->courseid))) {
                foreach ($grade_items as $grade_item) {
                    $formula = str_replace('['.$grade_item->idnumber.']', '[#gi'.$grade_item->id.'#]', $formula);
                }
            }

            $this->calculation = $formula;
        }

        $this->calculation_normalized = true;
        return $this->update();
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
     * grade_category, for cases where the object type is not know.
     * @return int Sort order
     */
    function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * Sets the sortorder of this grade_item. This method is also available in
     * grade_category, for cases where the object type is not know.
     * @param int $sortorder
     * @return void
     */
    function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
        $this->update();
    }

    function move_after_sortorder($sortorder) {
        global $CFG;

        //make some room first
        $sql = "UPDATE {$CFG->prefix}grade_items
                   SET sortorder = sortorder + 1
                 WHERE sortorder > $sortorder AND courseid = {$this->courseid}";
        execute_sql($sql);

        $this->set_sortorder($sortorder + 1);
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     * @return string name
     */
    function get_name() {
        if (!empty($this->itemname)) {
            return $this->itemname;

        } else if ($this->is_course_item()) {
            return get_string('total');

        } else {
            return get_string('grade');

        }
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
        if ($this->is_course_item()) {
            return false;

        } else if ($this->is_category_item()) {

            return $category->id;

        } else {
            return $this->categoryid;
;
        }
    }

    /**
     * Sets this item's categoryid. A generic method shared by objects that have a parent id of some kind.
     * @param int $parentid
     * @return boolean success;
     */
    function set_parent($parentid) {
        if ($this->is_course_item() or $this->is_category_item()) {
            error('Can not set parent for category or course item!');
        }

        if ($this->categoryid == $parentid) {
            return true;
        }

        // find parent and check course id
        if (!$parent_category = grade_category::fetch(array('id'=>$parentid, 'courseid'=>$this->courseid))) {
            return false;
        }

        $this->force_regrading(); // mark old parent as needing regrading

        // set new parent
        $this->categoryid = $parentid;
        $this->parent_category = null;

        return $this->update(); // mark new parent as needing regrading too
    }

    /**
     * Finds out on which other items does this depend directly when doing calculation or category agregation
     * @return array of grade_item ids this one depends on
     */
    function depends_on() {
        global $CFG;

        if ($this->is_locked()) {
            // locked items do not need to be regraded
            return array();
        }

        if ($this->is_calculated()) {
            if (preg_match_all('/\[#gi([0-9]+)#\]/', $this->calculation, $matches)) {
                return array_unique($matches[1]); // remove duplicates
            } else {
                return array();
            }

        } else if ($grade_category = $this->load_item_category()) {
            $sql = "SELECT gi.id
                      FROM {$CFG->prefix}grade_items gi
                     WHERE gi.categoryid ={$grade_category->id}

                    UNION

                    SELECT gi.id
                      FROM {$CFG->prefix}grade_items gi, {$CFG->prefix}grade_categories gc
                     WHERE (gi.itemtype = 'category' OR gi.itemtype = 'course') AND gi.iteminstance=gc.id
                           AND gc.parent = {$grade_category->id}";

            if ($children = get_records_sql($sql)) {
                return array_keys($children);
            } else {
                return array();
            }

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
     * @param mixed $rawgrade float value of raw grade - false means do not change
     * @param string $howmodified modification source
     * @param string $note optional note
     * @param mixed $feedback teachers feedback as string - false means do not change
     * @param int $feedbackformat
     * @return mixed grade_grades object if ok, false if error
     */
    function update_raw_grade($userid, $rawgrade=false, $source='manual', $note=NULL, $feedback=false, $feedbackformat=FORMAT_MOODLE) {
        global $CFG;
        require_once($CFG->libdir.'/eventslib.php');

        // calculated grades can not be updated
        if ($this->is_calculated()) {
            return false;
        }

        // do not allow grade updates when item locked - this prevents fetching of grade from db
        if ($this->is_locked()) {
            return false;
        }

        $grade = new grade_grades(array('itemid'=>$this->id, 'userid'=>$userid));
        $grade->grade_item =& $this; // prevent db fetching of cached grade_item

        if (!empty($grade->id)) {
            if ($grade->is_locked()) {
                // do not update locked grades at all
                return false;
            }

            if (!empty($grade->locktime) and $grade->locktime < time()) {
                // do not update grades that should be already locked
                // this does not solve all problems, cron is still needed to recalculate the final grades periodically
                return false;
            }

        }

        //TODO: if grade tree does not need to be recalculated, try to update grades of all users in course and force_regrading only if failed

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
        grade_history::insert_change($userid, $this->id, $grade->rawgrade, $oldgrade, $source, $note);

        // This grade item needs update
        $this->force_regrading();

        if ($result) {

            // trigger grade_updated event notification
            $eventdata = new object();

            $eventdata->source       = $source;
            $eventdata->itemid       = $this->id;
            $eventdata->courseid     = $this->courseid;
            $eventdata->itemtype     = $this->itemtype;
            $eventdata->itemmodule   = $this->itemmodule;
            $eventdata->iteminstance = $this->iteminstance;
            $eventdata->itemnumber   = $this->itemnumber;
            $eventdata->idnumber     = $this->idnumber;
            $eventdata->userid       = $grade->userid;
            $eventdata->rawgrade     = $grade->rawgrade;

            // load existing text annotation
            if ($grade_text = $grade->load_text()) {
                $eventdata->feedback          = $grade_text->feedback;
                $eventdata->feedbackformat    = $grade_text->feedbackformat;
                $eventdata->information       = $grade_text->information;
                $eventdata->informationformat = $grade_text->informationformat;
            }

            events_trigger('grade_updated', $eventdata);

            return $grade;

        } else {
            return false;
        }
    }

    /**
     * Calculates final grade values useing the formula in calculation property.
     * The parameteres are taken from final grades of grade items in current course only.
     * @return boolean false if error
     */
    function compute() {
        global $CFG;

        if (!$this->is_calculated()) {
            return false;
        }

        require_once($CFG->libdir.'/mathslib.php');

        if ($this->is_locked()) {
            return true; // no need to recalculate locked items
        }

        // get used items
        $useditems = $this->depends_on();

        // prepare formula and init maths library
        $formula = preg_replace('/\[#(gi[0-9]+)#\]/', '\1', $this->calculation);
        $this->formula = new calc_formula($formula);

        // where to look for final grades?
        // this itemid is added so that we use only one query for source and final grades
        $gis = implode(',', array_merge($useditems, array($this->id)));

        $sql = "SELECT g.*
                  FROM {$CFG->prefix}grade_grades g, {$CFG->prefix}grade_items gi
                 WHERE gi.id = g.itemid AND gi.courseid={$this->courseid} AND gi.id IN ($gis)
              ORDER BY g.userid";

        $return = true;

        // group the grades by userid and use formula on the group
        if ($rs = get_recordset_sql($sql)) {
            if ($rs->RecordCount() > 0) {
                $prevuser = 0;
                $grades   = array();
                $final    = null;
                while ($used = rs_fetch_next_record($rs)) {
                    if ($used->userid != $prevuser) {
                        if (!$this->use_formula($prevuser, $grades, $useditems, $final)) {
                            $return = false;
                        }
                        $prevuser = $used->userid;
                        $grades   = array();
                        $final    = null;
                    }
                    if ($used->itemid == $this->id) {
                        $final = new grade_grades($used, false); // fetching from db is not needed
                        $final->grade_item =& $this;
                    }
                    $grades['gi'.$used->itemid] = $used->finalgrade;
                }
                if (!$this->use_formula($prevuser, $grades, $useditems, $final)) {
                    $return = false;
                }
            }
        }

        //TODO: we could return array of errors here
        return $return;
    }

    /**
     * internal function - does the final grade calculation
     */
    function use_formula($userid, $params, $useditems, $final) {
        if (empty($userid)) {
            return true;
        }

        // add missing final grade values
        // not graded (null) is counted as 0 - the spreadsheet way
        foreach($useditems as $gi) {
            if (!array_key_exists('gi'.$gi, $params)) {
                $params['gi'.$gi] = 0;
            } else {
                $params['gi'.$gi] = (float)$params['gi'.$gi];
            }
        }

        // can not use own final grade during calculation
        unset($params['gi'.$this->id]);

        // insert final grade - will be needed later anyway
        if (empty($final)) {
            $final = new grade_grades(array('itemid'=>$this->id, 'userid'=>$userid), false);
            $final->insert();
            $final->grade_item =& $this;

        } else if ($final->is_locked()) {
            // no need to recalculate locked grades
            return;
        }


        // do the calculation
        $this->formula->set_params($params);
        $result = $this->formula->evaluate();

        // store the result
        if ($result === false) {
            // error during calculation
            if (!is_null($final->finalgrade) or !is_null($final->rawgrade)) {
                $final->finalgrade = null;
                $final->rawgrade   = null;
                $final->update();
            }
            return false;

        } else {
            // normalize
            $result = bounded_number($this->grademin, $result, $this->grademax);
            if ($this->gradetype == GRADE_TYPE_SCALE) {
                $result = round($result+0.00001); // round scales upwards
            }

            // store only if final grade changed, remove raw grade because we do not need it
            if ($final->finalgrade != $result or !is_null($final->rawgrade)) {
                $final->finalgrade = $result;
                $final->rawgrade   = null;
                $final->update();
            }
            return true;
        }
    }

}
?>

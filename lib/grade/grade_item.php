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
    var $nonfields = array('table', 'nonfields', 'required_fields', 'formula', 'calculation_normalized', 'scale', 'item_category', 'parent_category', 'outcome');

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
     * Aggregation coeficient used for weighted averages
     * @var float $aggregationcoef
     */
    var $aggregationcoef = 0;

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
     * Grade item lock flag. Empty if not locked, locked if any value present, usually date when item was locked. Locking prevents updating.
     * @var int $locked
     */
    var $locked = 0;

    /**
     * Date after which the grade will be locked. Empty means no automatic locking.
     * @var int $locktime
     */
    var $locktime = 0;

    /**
     * If set, the whole column will be recalculated, then this flag will be switched off.
     * @var boolean $needsupdate
     */
    var $needsupdate = 1;

    /**
     * In addition to update() as defined in grade_object, handle the grade_outcome and grade_scale objects.
     * Force regrading if necessary
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function update($source=null) {
        // Retrieve scale and infer grademax/min from it if needed
        $this->load_scale();

        if ($this->qualifies_for_regrading()) {
            $this->force_regrading();
        }

        return parent::update($source);
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
        $acoefdiff       = $db_item->aggregationcoef != $this->aggregationcoef;

        $needsupdatediff = !$db_item->needsupdate &&  $this->needsupdate;    // force regrading only if setting the flag first time
        $lockeddiff      = !empty($db_item->locked) && empty($this->locked); // force regrading only when unlocking

        return ($calculationdiff || $categorydiff || $gradetypediff || $grademaxdiff || $grademindiff || $scaleiddiff
             || $outcomeiddiff || $multfactordiff || $plusfactordiff || $needsupdatediff
             || $lockeddiff || $acoefdiff);
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
     * Delete all grades and force_regrading of parent category.
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function delete($source=null) {
        if ($this->is_course_item()) {
            debuggin('Can not delete course or category item!');
            return false;
        }

        $this->force_regrading();

        if ($grades = grade_grade::fetch_all(array('itemid'=>$this->id))) {
            foreach ($grades as $grade) {
                $grade->delete($source);
            }
        }

        return parent::delete($source);
    }

    /**
     * In addition to perform parent::insert(), calls force_regrading() method too.
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    function insert($source=null) {
        global $CFG;

        if (empty($this->courseid)) {
            error('Can not insert grade item without course id!');
        }

        // load scale if needed
        $this->load_scale();

        // add parent category if needed
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

        // add proper item numbers to manual items
        if ($this->itemtype == 'manual') {
            if (empty($this->itemnumber)) {
                $this->itemnumber = 0;
            }
        }

        if (parent::insert($source)) {
            // force regrading of items if needed
            $this->force_regrading();
            return $this->id;

        } else {
            debugging("Could not insert this grade_item in the database!");
            return false;
        }
    }

    /**
     * Set idnumber of grade item, updates also course_modules table
     * @param string $idnumber (without magic quotes)
     * @return boolean success
     */
    function add_idnumber($idnumber) {
        if (!empty($this->idnumber)) {
            return false;
        }

        if ($this->itemtype == 'mod' and !$this->is_outcome_item()) {
            if (!$cm = get_coursemodule_from_instance($this->itemmodule, $this->iteminstance, $this->courseid)) {
                return false;
            }
            if (!empty($cm->idnumber)) {
                return false;
            }
            if (set_field('course_modules', 'idnumber', addslashes($idnumber), 'id', $cm->id)) {
                $this->idnumber = $idnumber;
                return $this->update();
            }
            return false;

        } else {
            $this->idnumber = $idnumber;
            return $this->update();
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
            if ($grade = grade_grade::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
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
                $grade = new grade_grade($g, false);
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
            if ($grades = grade_grade::fetch_all(array('itemid'=>$this->id))) {
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
     * Set the locktime for this grade.
     *
     * @param int $locktime timestamp for lock to activate
     * @return boolean true if sucessful, false if can not set new lock state for grade
     */
    function set_locktime($locktime) {

        if ($locktime) {
            // if current locktime is before, no need to reset

            if ($this->locktime && $this->locktime <= $locktime) {
                return true;
            }

            /*
            if ($this->grade_item->needsupdate) {
                //can not lock grade if final not calculated!
                return false;
            }
            */

            $this->locktime = $locktime;
            $this->update();

            return true;

        } else {

            // remove the locktime timestamp
            $this->locktime = 0;

            $this->update();

            return true;
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
            if ($grade = grade_grade::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
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

        if ($grades = grade_grade::fetch_all(array('itemid'=>$this->id))) {
            foreach($grades as $grade) {
                $grade->grade_item =& $this;
                $grade->set_hidden($hidden);
            }
        }
    }

    /**
     * Mark regrading as finished successfully.
     */
    function regrading_finished() {
        $this->needsupdate = 0;
        //do not use $this->update() because we do not want this logged in grade_item_history
        set_field('grade_items', 'needsupdate', 0, 'id', $this->id);

        if (!empty($this->locktime) and empty($this->locked) and $this->locktime < time()) {
            // time to lock this grade_item
            $this->set_locked(true);
        }
    }

    /**
     * Performs the necessary calculations on the grades_final referenced by this grade_item.
     * Also resets the needsupdate flag once successfully performed.
     *
     * This function must be used ONLY from lib/gradeslib.php/grade_regrade_final_grades(),
     * because the regrading must be done in correct order!!
     *
     * @return boolean true if ok, error string otherwise
     */
    function regrade_final_grades($userid=null) {
        global $CFG;

        // locked grade items already have correct final grades
        if ($this->is_locked()) {
            return true;
        }

        // calculation produces final value using formula from other final values
        if ($this->is_calculated()) {
            if ($this->compute($userid)) {
                return true;
            } else {
                return "Could not calculate grades for grade item"; // TODO: improve and localize
            }

        // aggregate the category grade
        } else if ($this->is_category_item() or $this->is_course_item()) {
            // aggregate category grade item
            $category = $this->get_item_category();
            $category->grade_item =& $this;
            if ($category->generate_grades($userid)) {
                return true;
            } else {
                return "Could not aggregate final grades for category:".$this->id; // TODO: improve and localize
            }
        } else if ($this->is_manual_item()) {
            // manual items track only final grades, no raw grades
            return true;
        }

        // normal grade item - just new final grades
        $result = true;
        if ($userid) {
            $rs = get_recordset_select('grade_grades', "itemid={$this->id} AND userid=$userid");
        } else {
            $rs = get_recordset('grade_grades', 'itemid', $this->id);
        }
        if ($rs) {
            if ($rs->RecordCount() > 0) {
                while ($grade_record = rs_fetch_next_record($rs)) {
                    if (!empty($grade_record->locked) or !empty($grade_record->overridden)) {
                        // this grade is locked - final grade must be ok
                        continue;
                    }

                    $grade = new grade_grade($grade_record, false);
                    $grade->finalgrade = $this->adjust_grade($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);

                    if ($grade_record->finalgrade !== $grade->finalgrade) {
                        if (!$grade->update('system')) {
                            $result = "Internal error updating final grade";
                        }
                    }

                    // time to lock this grade?
                    if (!empty($grade->locktime) and empty($grade->locked) and $grade->locktime < time()) {
                        $grade->locked = time();
                        $grade->grade_item =& $this;
                        $grade->set_locked(true);
                    }
                }
            }
            rs_close($rs);
        }

        return $result;
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
                $rawgrade = grade_grade::standardise_score($rawgrade, $rawmin, $rawmax, $this->grademin, $this->grademax);
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
                $rawgrade = grade_grade::standardise_score($rawgrade, $rawmin, $rawmax, $this->grademin, $this->grademax);
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
     * Sets this grade_item's needsupdate to true. Also marks the course item as needing update.
     * @return void
     */
    function force_regrading() {
        $this->needsupdate = 1;
        //mark this item and course item only - categories and calculated items are always regraded
        $wheresql = "(itemtype='course' OR id={$this->id}) AND courseid={$this->courseid}";
        set_field_select('grade_items', 'needsupdate', 1, $wheresql);
    }

    /**
     * Instantiates a grade_scale object whose data is retrieved from the DB,
     * if this item's scaleid variable is set.
     * @return object grade_scale or null if no scale used
     */
    function load_scale() {
        if ($this->gradetype != GRADE_TYPE_SCALE) {
            $this->scaleid = null;
        }

        if (!empty($this->scaleid)) {
            //do not load scale if already present
            if (empty($this->scale->id) or $this->scale->id != $this->scaleid) {
                $this->scale = grade_scale::fetch(array('id'=>$this->scaleid));
                $this->scale->load_items();
            }

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

    /**
     * Is the grade item associated with category?
     * @return boolean
     */
    function is_category_item() {
        return ($this->itemtype == 'category');
    }

    /**
     * Is the grade item associated with course?
     * @return boolean
     */
    function is_course_item() {
        return ($this->itemtype == 'course');
    }

    /**
     * Is this a manualy graded item?
     * @return boolean
     */
    function is_manual_item() {
        return ($this->itemtype == 'manual');
    }

    /**
     * Is this an outcome item?
     * @return boolean
     */
    function is_outcome_item() {
        return !empty($this->outcomeid);
    }

    /**
     * Is the grade item normal - associated with module, plugin or something else?
     * @return boolean
     */
    function is_normal_item() {
        return ($this->itemtype != 'course' and $this->itemtype != 'category' and $this->itemtype != 'manual');
    }

    /**
     * Returns grade item associated with the course
     * @param int $courseid
     * @return course item object
     */
    function fetch_course_item($courseid) {
        if ($course_item = grade_item::fetch(array('courseid'=>$courseid, 'itemtype'=>'course'))) {
            return $course_item;
        }

        // first get category - it creates the associated grade item
        $course_category = grade_category::fetch_course_category($courseid);

        return grade_item::fetch(array('courseid'=>$courseid, 'itemtype'=>'course'));
    }

    /**
     * Is grading object editable?
     * @return boolean
     */
    function is_editable() {
        return true;
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
         * The main reason why we use the ##gixxx## instead of [[idnumber]] is speed of depends_on(),
         * we would have to fetch all course grade items to find out the ids.
         * Also if user changes the idnumber the formula does not need to be updated.
         */

        // first detect if we need to change calculation formula from [[idnumber]] to ##giXXX## (after backup, etc.)
        if (!$this->calculation_normalized and preg_match('/##gi\d+##/', $this->calculation)) {
            $this->set_calculation($this->calculation);
        }

        return !empty($this->calculation);
    }

    /**
     * Returns calculation string if grade calculated.
     * @return mixed string if calculation used, null if not
     */
    function get_calculation() {
        if ($this->is_calculated()) {
            return grade_item::denormalize_formula($this->calculation, $this->courseid);

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
        $this->calculation = grade_item::normalize_formula($formula, $this->courseid);
        $this->calculation_normalized = true;
        return $this->update();
    }

    /**
     * Denormalizes the calculation formula to [idnumber] form
     * @static
     * @param string $formula
     * @return string denormalized string
     */
    function denormalize_formula($formula, $courseid) {
        if (empty($formula)) {
            return '';
        }

        // denormalize formula - convert ##giXX## to [[idnumber]]
        if (preg_match_all('/##gi(\d+)##/', $formula, $matches)) {
            foreach ($matches[1] as $id) {
                if ($grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$courseid))) {
                    if (!empty($grade_item->idnumber)) {
                        $formula = str_replace('##gi'.$grade_item->id.'##', '[['.$grade_item->idnumber.']]', $formula);
                    }
                }
            }
        }

        return $formula;

    }

    /**
     * Normalizes the calculation formula to [#giXX#] form
     * @static
     * @param string $formula
     * @return string normalized string
     */
    function normalize_formula($formula, $courseid) {
        $formula = trim($formula);

        if (empty($formula)) {
            return NULL;

        }

        // normalize formula - we want grade item ids ##giXXX## instead of [[idnumber]]
        if ($grade_items = grade_item::fetch_all(array('courseid'=>$courseid))) {
            foreach ($grade_items as $grade_item) {
                $formula = str_replace('[['.$grade_item->idnumber.']]', '##gi'.$grade_item->id.'##', $formula);
            }
        }

        return $formula;
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
     * Get (or create if not exist yet) grade for this user
     * @param int $userid
     * @return object grade_grade object instance
     */
    function get_grade($userid, $create=true) {
        if (empty($this->id)) {
            debugging('Can not use before insert');
            return false;
        }

        $grade = new grade_grade(array('userid'=>$userid, 'itemid'=>$this->id));
        if (empty($grade->id) and $create) {
            $grade->insert();
        }

        return $grade;
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
        execute_sql($sql, false);

        $this->set_sortorder($sortorder + 1);
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     * @return string name
     */
    function get_name() {
        if (!empty($this->itemname)) {
            // MDL-10557
            return format_string($this->itemname);

        } else if ($this->is_course_item()) {
            return get_string('total');

        } else {
            return get_string('grade');
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

        $this->force_regrading();

        // set new parent
        $this->categoryid = $parent_category->id;
        $this->parent_category =& $parent_category;

        return $this->update();
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
            if (preg_match_all('/##gi(\d+)##/', $this->calculation, $matches)) {
                return array_unique($matches[1]); // remove duplicates
            } else {
                return array();
            }

        } else if ($grade_category = $this->load_item_category()) {
            //only items with numeric or scale values can be aggregated
            if ($this->gradetype != GRADE_TYPE_VALUE and $this->gradetype != GRADE_TYPE_SCALE) {
                return array();
            }

            $sql = "SELECT gi.id
                      FROM {$CFG->prefix}grade_items gi
                     WHERE gi.categoryid = {$grade_category->id}
                           AND (gi.gradetype = ".GRADE_TYPE_VALUE." OR gi.gradetype = ".GRADE_TYPE_SCALE.")  

                    UNION

                    SELECT gi.id
                      FROM {$CFG->prefix}grade_items gi, {$CFG->prefix}grade_categories gc
                     WHERE (gi.itemtype = 'category' OR gi.itemtype = 'course') AND gi.iteminstance=gc.id
                           AND gc.parent = {$grade_category->id}
                           AND (gi.gradetype = ".GRADE_TYPE_VALUE." OR gi.gradetype = ".GRADE_TYPE_SCALE.")";  

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
     * Updates final grade value for given user, this is a only way to update final
     * grades from gradebook and import because it logs the change in history table
     * and deals with overridden flag. This flag is set to prevent later overriding
     * from raw grades submitted from modules.
     *
     * @param int $userid the graded user
     * @param mixed $finalgrade float value of final grade - false means do not change
     * @param string $howmodified modification source
     * @param string $note optional note
     * @param mixed $feedback teachers feedback as string - false means do not change
     * @param int $feedbackformat
     * @return boolean success
     * TODO Allow for a change of feedback without a change of finalgrade. Currently I get notice about uninitialised $result
     */
    function update_final_grade($userid, $finalgrade=false, $source=NULL, $note=NULL, $feedback=false, $feedbackformat=FORMAT_MOODLE, $usermodified=null) {
        global $USER;
        if (empty($usermodified)) {
            $usermodified = $USER->id;
        }

        // no grading used or locked
        if ($this->gradetype == GRADE_TYPE_NONE or $this->is_locked()) {
            return false;
        }

        if (!$grade = grade_grade::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
            $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$userid), false);
        }

        $grade->grade_item =& $this; // prevent db fetching of this grade_item
        $oldgrade = new object();
        $oldgrade->finalgrade  = $grade->finalgrade;
        $oldgrade->rawgrade    = $grade->rawgrade;
        $oldgrade->rawgrademin = $grade->rawgrademin;
        $oldgrade->rawgrademax = $grade->rawgrademax;
        $oldgrade->rawscaleid  = $grade->rawscaleid;
        $oldgrade->overridden  = $grade->overridden;

        if ($grade->is_locked()) {
            // do not update locked grades at all
            return false;
        }

        if (!empty($grade->locktime) and $grade->locktime < time()) {
            // do not update grades that should be already locked
            // this does not solve all problems, cron is still needed to recalculate the final grades periodically
            return false;
        }

        if ($finalgrade !== false) {
            if (!is_null($finalgrade)) {
                $grade->finalgrade = bounded_number($this->grademin, $finalgrade, $this->grademax);
            } else {
                $grade->finalgrade = $finalgrade;
            }

            // if we can update the raw grade, do update it
            if ($this->is_outcome_item() or !$this->is_normal_item()
             or $this->plusfactor != 0 or $this->multfactor != 1
             or !events_is_registered('grade_updated', $this->itemtype.'/'.$this->itemmodule)) {
                if (!$grade->overridden) {
                    $grade->overridden = time();
                }
            } else {
                $grade->rawgrade = $finalgrade;
                // copy current grademin/max and scale
                $grade->rawgrademin = $this->grademin;
                $grade->rawgrademax = $this->grademax;
                $grade->rawscaleid  = $this->scaleid;
            }
        }

        if (empty($grade->id)) {
            $result = (boolean)$grade->insert($source);

        } else if ($grade->finalgrade  !== $oldgrade->finalgrade
                or $grade->rawgrade    !== $oldgrade->rawgrade
                or $grade->rawgrademin !== $oldgrade->rawgrademin
                or $grade->rawgrademax !== $oldgrade->rawgrademax
                or $grade->rawscaleid  !== $oldgrade->rawscaleid
                or $grade->overridden  !== $oldgrade->overridden) {

            $result = $grade->update($source);

        } else {
            $result = true;
        }

        // do we have comment from teacher?
        if ($result and $feedback !== false) {
            $result = $grade->update_feedback($feedback, $feedbackformat, $usermodified);
        }

        if ($this->is_course_item() and !$this->needsupdate) {
            if (!grade_regrade_final_grades($this->courseid, $userid, $this)) {
                $this->force_regrading();
            }

        } else if (!$this->needsupdate) {
            $course_item = grade_item::fetch_course_item($this->courseid);
            if (!$course_item->needsupdate) {
                if (!grade_regrade_final_grades($this->courseid, $userid, $this)) {
                    $this->force_regrading();
                }
            } else {
                $this->force_regrading();
            }
        }

        // no events for overridden items and outcomes
        if ($result and !$grade->overridden and $this->itemnumber < 1000) {
            $this->trigger_raw_updated($grade, $source);
        }

        return $result;
    }


    /**
     * Updates raw grade value for given user, this is a only way to update raw
     * grades from external source (modules, etc.),
     * because it logs the change in history table and deals with final grade recalculation.
     *
     * @param int $userid the graded user
     * @param mixed $rawgrade float value of raw grade - false means do not change
     * @param string $howmodified modification source
     * @param string $note optional note
     * @param mixed $feedback teachers feedback as string - false means do not change
     * @param int $feedbackformat
     * @return boolean success
     */
    function update_raw_grade($userid, $rawgrade=false, $source=NULL, $note=NULL, $feedback=false, $feedbackformat=FORMAT_MOODLE, $usermodified=null) {
        global $USER;

        if (empty($usermodified)) {
            $usermodified = $USER->id;
        }

        // calculated grades can not be updated; course and category can not be updated  because they are aggregated
        if ($this->is_calculated() or $this->is_outcome_item() or !$this->is_normal_item()
         or $this->gradetype == GRADE_TYPE_NONE or $this->is_locked()) {
            return false;
        }

        if (!$grade = grade_grade::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
            $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$userid), false);
        }

        $grade->grade_item =& $this; // prevent db fetching of this grade_item
        $oldgrade = new object();
        $oldgrade->finalgrade  = $grade->finalgrade;
        $oldgrade->rawgrade    = $grade->rawgrade;
        $oldgrade->rawgrademin = $grade->rawgrademin;
        $oldgrade->rawgrademax = $grade->rawgrademax;
        $oldgrade->rawscaleid  = $grade->rawscaleid;

        if ($grade->is_locked()) {
            // do not update locked grades at all
            return false;
        }

        if (!empty($grade->locktime) and $grade->locktime < time()) {
            // do not update grades that should be already locked
            // this does not solve all problems, cron is still needed to recalculate the final grades periodically
            return false;
        }

        // fist copy current grademin/max and scale
        $grade->rawgrademin = $this->grademin;
        $grade->rawgrademax = $this->grademax;
        $grade->rawscaleid  = $this->scaleid;

        if ($rawgrade !== false) {
            $grade->rawgrade = $rawgrade;
        }

        if (empty($grade->id)) {
            $result = (boolean)$grade->insert($source);

        } else if ($grade->finalgrade  !== $oldgrade->finalgrade
                or $grade->rawgrade    !== $oldgrade->rawgrade
                or $grade->rawgrademin !== $oldgrade->rawgrademin
                or $grade->rawgrademax !== $oldgrade->rawgrademax
                or $grade->rawscaleid  !== $oldgrade->rawscaleid) {

            $result = $grade->update($source);

        } else {
            $result = true;
        }

        // do we have comment from teacher?
        if ($result and $feedback !== false) {
            $result = $grade->update_feedback($feedback, $feedbackformat, $usermodified);
        }

        if (!$this->needsupdate) {
            $course_item = grade_item::fetch_course_item($this->courseid);
            if (!$course_item->needsupdate) {
                if (!grade_regrade_final_grades($this->courseid, $userid, $this)) {
                    $this->force_regrading();
                }
            } else {
                $this->force_regrading();
            }
        }

        // no events for outcomes
        if ($result and $this->itemnumber < 1000) {
            $this->trigger_raw_updated($grade, $source);
        }

        return $result;
    }

    /**
     * Internal function used by update_final/raw_grade() only.
     */
    function trigger_raw_updated($grade, $source) {
        global $CFG;
        require_once($CFG->libdir.'/eventslib.php');

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
    }

    /**
     * Calculates final grade values using the formula in calculation property.
     * The parameters are taken from final grades of grade items in current course only.
     * @return boolean false if error
     */
    function compute($userid=null) {
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
        $formula = preg_replace('/##(gi\d+)##/', '\1', $this->calculation);
        $this->formula = new calc_formula($formula);

        // where to look for final grades?
        // this itemid is added so that we use only one query for source and final grades
        $gis = implode(',', array_merge($useditems, array($this->id)));

        if ($userid) {
            $usersql = "AND g.userid=$userid";
        } else {
            $usersql = "";
        }

        $sql = "SELECT g.*
                  FROM {$CFG->prefix}grade_grades g, {$CFG->prefix}grade_items gi
                 WHERE gi.id = g.itemid AND gi.courseid={$this->courseid} AND gi.id IN ($gis) $usersql
              ORDER BY g.userid";

        $return = true;

        // group the grades by userid and use formula on the group
        if ($rs = get_recordset_sql($sql)) {
            if ($rs->RecordCount() > 0) {
                $prevuser = 0;
                $grade_records   = array();
                $oldgrade    = null;
                while ($used = rs_fetch_next_record($rs)) {
                    if ($used->userid != $prevuser) {
                        if (!$this->use_formula($prevuser, $grade_records, $useditems, $oldgrade)) {
                            $return = false;
                        }
                        $prevuser = $used->userid;
                        $grade_records   = array();
                        $oldgrade    = null;
                    }
                    if ($used->itemid == $this->id) {
                        $oldgrade = $used;
                    }
                    $grade_records['gi'.$used->itemid] = $used->finalgrade;
                }
                if (!$this->use_formula($prevuser, $grade_records, $useditems, $oldgrade)) {
                    $return = false;
                }
            }
            rs_close($rs);
        }

        return $return;
    }

    /**
     * internal function - does the final grade calculation
     */
    function use_formula($userid, $params, $useditems, $oldgrade) {
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
        if ($oldgrade) {
            $grade = new grade_grade($oldgrade, false); // fetching from db is not needed
            $grade->grade_item =& $this;

        } else {
            $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$userid), false);
            $grade->insert('system');
            $grade->grade_item =& $this;

            $oldgrade = new object();
            $oldgrade->finalgrade  = $grade->finalgrade;
            $oldgrade->rawgrade    = $grade->rawgrade;
        }

        // no need to recalculate locked or overridden grades
        if ($grade->is_locked() or $grade->is_overridden()) {
            return true;
        }

        // do the calculation
        $this->formula->set_params($params);
        $result = $this->formula->evaluate();

        // no raw grade for calculated grades - only final
        $grade->rawgrade = null;


        if ($result === false) {
            $grade->finalgrade = null;

        } else {
            // normalize
            $result = bounded_number($this->grademin, $result, $this->grademax);
            if ($this->gradetype == GRADE_TYPE_SCALE) {
                $result = round($result+0.00001); // round scales upwards
            }
            $grade->finalgrade = $result;
        }

        // update in db if changed
        if (   $grade->finalgrade  !== $oldgrade->finalgrade
            or $grade->rawgrade    !== $oldgrade->rawgrade) {

            $grade->update('system');
        }

        if ($result === false) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Validate the formula.
     * @param string $formula
     * @return boolean true if calculation possible, false otherwise
     */
    function validate_formula($formula) {
        global $CFG;
        require_once($CFG->libdir.'/mathslib.php');

        $formula = grade_item::normalize_formula($formula, $this->courseid);

        if (empty($formula)) {
            return true;
        }

        if (strpos($formula, '=') !== 0) {
            return get_string('errorcalculationnoequal', 'grades');
        }

        // prepare formula and init maths library
        $formula = preg_replace('/##(gi\d+)##/', '\1', $formula);
        $formula = new calc_formula($formula);

        // get used items
        $useditems = $this->depends_on();

        if (empty($useditems)) {
            $grade_items = array();

        } else {
            $gis = implode(',', $useditems);

            $sql = "SELECT gi.*
                      FROM {$CFG->prefix}grade_items gi
                     WHERE gi.id IN ($gis) and gi.courseid={$this->courseid}"; // from the same course only!

            if (!$grade_items = get_records_sql($sql)) {
                $grade_items = array();
            }
        }

        $params = array();
        foreach ($useditems as $itemid) {
            // make sure all grade items exist in this course
            if (!array_key_exists($itemid, $grade_items)) {
                return false;
            }
            // use max grade when testing formula, this should be ok in 99.9%
            // division by 0 is one of possible problems
            $params['gi'.$grade_items[$itemid]->id] = $grade_items[$itemid]->grademax;
        }

        // do the calculation
        $formula->set_params($params);
        $result = $formula->evaluate();

        // false as result indicates some problem
        if ($result === false) {
            // TODO: add more error hints
            return get_string('errorcalculationunknown', 'grades');
        } else {
            return true;
        }
    }
}
?>

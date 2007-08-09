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

class grade_grade extends grade_object {

    /**
     * The DB table.
     * @var string $table
     */
    var $table = 'grade_grades';

    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'required_fields', 'grade_grade_text', 'grade_item');

    /**
     * The id of the grade_item this grade belongs to.
     * @var int $itemid
     */
    var $itemid;

    /**
     * The grade_item object referenced by $this->itemid.
     * @var object $grade_item
     */
    var $grade_item;

    /**
     * The id of the user this grade belongs to.
     * @var int $userid
     */
    var $userid;

    /**
     * The grade value of this raw grade, if such was provided by the module.
     * @var float $rawgrade
     */
    var $rawgrade;

    /**
     * The maximum allowable grade when this grade was created.
     * @var float $rawgrademax
     */
    var $rawgrademax = 100;

    /**
     * The minimum allowable grade when this grade was created.
     * @var float $rawgrademin
     */
    var $rawgrademin = 0;

    /**
     * id of the scale, if this grade is based on a scale.
     * @var int $rawscaleid
     */
    var $rawscaleid;

    /**
     * The userid of the person who last modified this grade.
     * @var int $usermodified
     */
    var $usermodified;

    /**
     * Additional textual information about this grade. It can be automatically generated
     * from the module or entered manually by the teacher. This is kept in its own table
     * for efficiency reasons, so it is encapsulated in its own object, and included in this grade object.
     * @var object $grade_grade_text
     */
    var $grade_grade_text;

    /**
     * The final value of this grade.
     * @var float $finalgrade
     */
    var $finalgrade;

    /**
     * 0 if visible, 1 always hidden or date not visible until
     * @var float $hidden
     */
    var $hidden = 0;

    /**
     * 0 not locked, date when the item was locked
     * @var float locked
     */
    var $locked = 0;

    /**
     * 0 no automatic locking, date when to lock the grade automatically
     * @var float $locktime
     */
    var $locktime = 0;

    /**
     * Exported flag
     * @var boolean $exported
     */
    var $exported = 0;

    /**
     * Overridden flag
     * @var boolean $overridden
     */
    var $overridden = 0;

    /**
     * Grade excluded from aggregation functions
     * @var boolean $excluded
     */
    var $excluded = 0;

    /**
     * Loads the grade_grade_text object linked to this grade (through the intersection of itemid and userid), and
     * saves it as a class variable for this final object.
     * @return object
     */
    function load_text() {
        if (empty($this->id)) {
            return false; // text can not be attached to non existing grade
        }

        if (empty($this->grade_grade_text->id)) {
            $this->grade_grade_text = grade_grade_text::fetch(array('gradeid'=>$this->id));
        }

        return $this->grade_grade_text;
    }

    /**
     * Loads the grade_item object referenced by $this->itemid and saves it as $this->grade_item for easy access.
     * @return object grade_item.
     */
    function load_grade_item() {
        if (empty($this->itemid)) {
            debugging('Missing itemid');
            $this->grade_item = null;
            return null;
        }

        if (empty($this->grade_item)) {
            $this->grade_item = grade_item::fetch(array('id'=>$this->itemid));

        } else if ($this->grade_item->id != $this->itemid) {
            debugging('Itemid mismatch');
            $this->grade_item = grade_item::fetch(array('id'=>$this->itemid));
        }

        return $this->grade_item;
    }

    /**
     * Is grading object editable?
     * @return boolean
     */
    function is_editable() {
        if ($this->is_locked()) {
            return false;
        }

        $grade_item = $this->load_grade_item();

        if ($grade_item->gradetype == GRADE_TYPE_NONE) {
            return false;
        }

        return true;
    }

    /**
     * Check grade lock status. Uses both grade item lock and grade lock.
     * Internally any date in locked field (including future ones) means locked,
     * the date is stored for logging purposes only.
     *
     * @return boolean true if locked, false if not
     */
    function is_locked() {
        $this->load_grade_item();

        return !empty($this->locked) or $this->grade_item->is_locked();
    }

    /**
     * Checks if grade overridden
     * @return boolean
     */
    function is_overridden() {
        return !empty($this->overridden);
    }

    /**
     * Set the overridden status of grade
     * @param boolean $state requested overridden state
     * @return boolean true is db state changed
     */
    function set_overridden($state) {
        if (empty($this->overridden) and $state) {
            $this->overridden = time();
            $this->update();
            return true;

        } else if (!empty($this->overridden) and !$state) {
            $this->overridden = 0;
            $this->update();
            return true;
        }
        return false;
    }

    /**
     * Checks if grade excluded from aggregation functions
     * @return boolean
     */
    function is_excluded() {
        return !empty($this->excluded);
    }

    /**
     * Set the excluded status of grade
     * @param boolean $state requested excluded state
     * @return boolean true is db state changed
     */
    function set_excluded($state) {
        if (empty($this->excluded) and $state) {
            $this->excluded = time();
            $this->update();
            return true;

        } else if (!empty($this->excluded) and !$state) {
            $this->excluded = 0;
            $this->update();
            return true;
        }
        return false;
    }

    /**
     * Lock/unlock this grade.
     *
     * @param int $locked 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @param boolean $cascade ignored param
     * @param boolean $refresh refresh grades when unlocking
     * @return boolean true if sucessful, false if can not set new lock state for grade
     */
    function set_locked($lockedstate, $cascade=false, $refresh=true) {
        $this->load_grade_item();

        if ($lockedstate) {
            if ($this->grade_item->needsupdate) {
                //can not lock grade if final not calculated!
                return false;
            }

            $this->locked = time();
            $this->update();

            return true;

        } else {
            if (!empty($this->locked) and $this->locktime < time()) {
                //we have to reset locktime or else it would lock up again
                $this->locktime = 0;
            }

            // remove the locked flag
            $this->locked = 0;
            $this->update();

            if ($refresh) {
                //refresh when unlocking
                $this->grade_item->refresh_grades($this->userid);
            }

            return true;
        }
    }

    /**
     * Lock the grade if needed - make sure this is called only when final grade is valid
     */
    function check_locktime() {
        if (!empty($this->locked)) {
            return; // already locked - do not use is_locked() because we do not want the locking status of grade_item here
        }

        if ($this->locktime and $this->locktime < time()) {
            $this->locked = time();
            $this->update('locktime');
        }
    }


    /**
     * Lock the grade if needed - make sure this is called only when final grades are valid
     * @param int $courseid
     * @param array $items array of all grade item ids (speedup only)
     * @return void
     */
    function check_locktime_all($courseid, $items=null) {
        global $CFG;

        if (!$items) {
            if (!$items = get_records('grade_items', 'courseid', $courseid, '', 'id')) {
                return; // no items?
            }
            $items = array_keys($items);
        }

        $items_sql = implode(',', $items);

        $now = time(); // no rounding needed, this is not supposed to be called every 10 seconds

        if ($rs = get_recordset_select('grade_grades', "itemid IN ($items_sql) AND locked = 0 AND locktime > 0 AND locktime < $now")) {
            if ($rs->RecordCount() > 0) {
                while ($grade = rs_fetch_next_record($rs)) {
                    $grade_grade = new grade_grade($grade, false);
                    $grade_grade->locked = time();
                    $grade_grade->update('locktime');
                }
            }
            rs_close($rs);
        }
    }

    /**
     * Set the locktime for this grade.
     *
     * @param int $locktime timestamp for lock to activate
     * @return void
     */
    function set_locktime($locktime) {
        $this->locktime = $locktime;
        $this->update();
    }

    /**
     * Set the locktime for this grade.
     *
     * @return int $locktime timestamp for lock to activate
     */
    function get_locktime() {
        $this->load_grade_item();

        $item_locktime = $this->grade_item->get_locktime();

        if (empty($this->locktime) or ($item_locktime and $item_locktime < $this->locktime)) {
            return $item_locktime;

        } else {
            return $this->locktime;
        }
    }

    /**
     * Check grade lock status. Uses both grade item lock and grade lock.
     * Internally any date in hidden field (including future ones) means hidden,
     * the date is stored for logging purposes only.
     *
     * @param object $grade_item An optional grade_item given to avoid having to reload one from the DB
     * @return boolean true if hidden, false if not
     */
    function is_hidden($grade_item=null) {
        $this->load_grade_item($grade_item);

        return $this->hidden == 1 or $this->hidden > time() or $this->grade_item->is_hidden();
    }

    /**
     * Set the hidden status of grade, 0 mean visible, 1 always hidden, number means date to hide until.
     * @param int $hidden new hidden status
     */
    function set_hidden($hidden) {
       $this->hidden = $hidden;
       $this->update();
    }

    /**
     * Finds and returns a grade_grade instance based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_grade instance or false if none found.
     */
    function fetch($params) {
        return grade_object::fetch_helper('grade_grades', 'grade_grade', $params);
    }

    /**
     * Finds and returns all grade_grade instances based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_grade insatnces or false if none found.
     */
    function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_grades', 'grade_grade', $params);
    }


    /**
     * Delete grade together with feedback.
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function delete($source=null) {
        if ($text = $this->load_text()) {
            $text->delete($source);
        }
        return parent::delete($source);
    }

    /**
     * Updates this grade with the given textual information. This will create a new grade_grade_text entry
     * if none was previously in DB for this raw grade, or will update the existing one.
     * @param string $information Manual information from the teacher. Could be a code like 'mi'
     * @param int $informationformat Text format for the information
     * @return boolean Success or Failure
     */
    function update_information($information, $informationformat) {
        $this->load_text();

        if (empty($this->grade_grade_text->id)) {
            $this->grade_grade_text = new grade_grade_text();

            $this->grade_grade_text->gradeid           = $this->id;
            $this->grade_grade_text->userid            = $this->userid;
            $this->grade_grade_text->information       = $information;
            $this->grade_grade_text->informationformat = $informationformat;

            return $this->grade_grade_text->insert();

        } else {
            if ($this->grade_grade_text->information != $information
              or $this->grade_grade_text->informationformat != $informationformat) {

                $this->grade_grade_text->information       = $information;
                $this->grade_grade_text->informationformat = $informationformat;
                return  $this->grade_grade_text->update();
            } else {
                return true;
            }
        }
    }

    /**
     * Updates this grade with the given textual information. This will create a new grade_grade_text entry
     * if none was previously in DB for this raw grade, or will update the existing one.
     * @param string $feedback Manual feedback from the teacher. Could be a code like 'mi'
     * @param int $feedbackformat Text format for the feedback
     * @return boolean Success or Failure
     */
    function update_feedback($feedback, $feedbackformat, $usermodified=null) {
        global $USER;

        $this->load_text();

        if (empty($usermodified)) {
            $usermodified = $USER->id;
        }

        if (empty($this->grade_grade_text->id)) {
            $this->grade_grade_text = new grade_grade_text();

            $this->grade_grade_text->gradeid        = $this->id;
            $this->grade_grade_text->feedback       = $feedback;
            $this->grade_grade_text->feedbackformat = $feedbackformat;
            $this->grade_grade_text->usermodified   = $usermodified;

            return $this->grade_grade_text->insert();

        } else {
            if ($this->grade_grade_text->feedback != $feedback
              or $this->grade_grade_text->feedbackformat != $feedbackformat) {

                $this->grade_grade_text->feedback       = $feedback;
                $this->grade_grade_text->feedbackformat = $feedbackformat;
                $this->grade_grade_text->usermodified   = $usermodified;

                return  $this->grade_grade_text->update();
            } else {
                return true;
            }
        }
    }

    /**
     * Given a float value situated between a source minimum and a source maximum, converts it to the
     * corresponding value situated between a target minimum and a target maximum. Thanks to Darlene
     * for the formula :-)
     *
     * @static
     * @param float $rawgrade
     * @param float $source_min
     * @param float $source_max
     * @param float $target_min
     * @param float $target_max
     * @return float Converted value
     */
    function standardise_score($rawgrade, $source_min, $source_max, $target_min, $target_max) {
        if (is_null($rawgrade)) {
          return null;
        }

        $factor = ($rawgrade - $source_min) / ($source_max - $source_min);
        $diff = $target_max - $target_min;
        $standardised_value = $factor * $diff + $target_min;
        return $standardised_value;
    }

    /**
     * Returns the grade letter this grade falls under, as they are set up in the given array.
     * @param array $letters An array of grade boundaries with associated letters
     * @param float $gradevalue The value to convert. If not given, will use instantiated object
     * @param float $grademin If not given, will look up the grade_item's grademin
     * @param float $grademax If not given, will look up the grade_item's grademax
     * @return string Grade letter
     */
    function get_letter($letters, $gradevalue=null, $grademin=null, $grademax=null) {
        if (is_null($grademin) || is_null($grademax)) {
            if (!isset($this)) {
                debugging("Tried to call grade_grade::get_letter statically without giving an explicit grademin or grademax!");
                return false;
            }
            $this->load_grade_item();
            $grademin = $this->grade_item->grademin;
            $grademax = $this->grade_item->grademax;
        }

        if (is_null($gradevalue)) {
            if (!isset($this)) {
                debugging("Tried to call grade_grade::get_letter statically without giving an explicit gradevalue!");
                return false;
            }
            $gradevalue = $this->finalgrade;
        }
        // Standardise grade first
        $grade = grade_grade::standardise_score($gradevalue, $grademin, $grademax, 0, 100);

        // Sort the letters by descending boundaries (100-0)
        krsort($letters);
        foreach ($letters as $boundary => $letter) {
            if ($grade >= $boundary) {
                return $letter;
            }
        }
        return '-';
    }

}

?>

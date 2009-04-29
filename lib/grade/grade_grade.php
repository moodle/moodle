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

class grade_grade extends grade_object {

    /**
     * The DB table.
     * @var string $table
     */
    var $table = 'grade_grades';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    var $required_fields = array('id', 'itemid', 'userid', 'rawgrade', 'rawgrademax', 'rawgrademin',
                                 'rawscaleid', 'usermodified', 'finalgrade', 'hidden', 'locked',
                                 'locktime', 'exported', 'overridden', 'excluded', 'timecreated', 'timemodified');

    /**
     * Array of optional fields with default values (these should match db defaults)
     * @var array $optional_fields
     */
    var $optional_fields = array('feedback'=>null, 'feedbackformat'=>0, 'information'=>null, 'informationformat'=>0);

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
     * TODO: HACK: create a new field datesubmitted - the date of submission if any
     * @var boolean $timecreated
     */
    var $timecreated = null;

    /**
     * TODO: HACK: create a new field dategraded - the date of grading
     * @var boolean $timemodified
     */
    var $timemodified = null;


    /**
     * Returns array of grades for given grade_item+users.
     * @param object $grade_item
     * @param array $userids
     * @param bool $include_missing include grades that do not exist yet
     * @return array userid=>grade_grade array
     */
    function fetch_users_grades($grade_item, $userids, $include_missing=true) {

        // hmm, there might be a problem with length of sql query
        // if there are too many users requested - we might run out of memory anyway
        $limit = 2000;
        $count = count($userids);
        if ($count > $limit) {
            $half = (int)($count/2);
            $first  = array_slice($userids, 0, $half);
            $second = array_slice($userids, $half);
            return grade_grade::fetch_users_grades($grade_item, $first, $include_missing) + grade_grade::fetch_users_grades($grade_item, $second, $include_missing);
        }

        $user_ids_cvs = implode(',', $userids);
        $result = array();
        if ($grade_records = get_records_select('grade_grades', "itemid={$grade_item->id} AND userid IN ($user_ids_cvs)")) {
            foreach ($grade_records as $record) {
                $result[$record->userid] = new grade_grade($record, false);
            }
        }
        if ($include_missing) {
            foreach ($userids as $userid) {
                if (!array_key_exists($userid, $result)) {
                    $grade_grade = new grade_grade();
                    $grade_grade->userid = $userid;
                    $grade_grade->itemid = $grade_item->id;
                    $result[$userid] = $grade_grade;
                }
            }
        }

        return $result;
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
        if (empty($this->grade_item)) {
            return !empty($this->locked);
        } else {
            return !empty($this->locked) or $this->grade_item->is_locked();
        }
    }

    /**
     * Checks if grade overridden
     * @return boolean
     */
    function is_overridden() {
        return !empty($this->overridden);
    }

    /**
     * Returns timestamp of submission related to this grade,
     * might be null if not submitted.
     * @return int
     */
    function get_datesubmitted() {
        //TODO: HACK - create new fields in 2.0
        return $this->timecreated;
    }

    /**
     * Returns timestamp when last graded,
     * might be null if no grade present.
     * @return int
     */
    function get_dategraded() {
        //TODO: HACK - create new fields in 2.0
        if (is_null($this->finalgrade) and is_null($this->feedback)) {
            return null; // no grade == no date
        } else if ($this->overridden) {
            return $this->overridden;
        } else {
            return $this->timemodified;
        }
    }

    /**
     * Set the overridden status of grade
     * @param boolean $state requested overridden state
     * @param boolean $refresh refresh grades from external activities if needed
     * @return boolean true is db state changed
     */
    function set_overridden($state, $refresh = true) {
        if (empty($this->overridden) and $state) {
            $this->overridden = time();
            $this->update();
            return true;

        } else if (!empty($this->overridden) and !$state) {
            $this->overridden = 0;
            $this->update();

            if ($refresh) {
                //refresh when unlocking
                $this->grade_item->refresh_grades($this->userid);
            }

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

            if ($refresh and !$this->is_overridden()) {
                //refresh when unlocking and not overridden
                $this->grade_item->refresh_grades($this->userid);
            }

            return true;
        }
    }

    /**
     * Lock the grade if needed - make sure this is called only when final grades are valid
     * @param array $items array of all grade item ids
     * @return void
     */
    function check_locktime_all($items) {
        global $CFG;

        $items_sql = implode(',', $items);

        $now = time(); // no rounding needed, this is not supposed to be called every 10 seconds

        if ($rs = get_recordset_select('grade_grades', "itemid IN ($items_sql) AND locked = 0 AND locktime > 0 AND locktime < $now")) {
            while ($grade = rs_fetch_next_record($rs)) {
                $grade_grade = new grade_grade($grade, false);
                $grade_grade->locked = time();
                $grade_grade->update('locktime');
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
     * Check grade hidden status. Uses data from both grade item and grade.
     * @return boolean true if hidden, false if not
     */
    function is_hidden() {
        $this->load_grade_item();
        if (empty($this->grade_item)) {
            return $this->hidden == 1 or ($this->hidden != 0 and $this->hidden > time());
        } else {
            return $this->hidden == 1 or ($this->hidden != 0 and $this->hidden > time()) or $this->grade_item->is_hidden();
        }
    }

    /**
     * Check grade hidden status. Uses data from both grade item and grade.
     * @return boolean true if hiddenuntil, false if not
     */
    function is_hiddenuntil() {
        $this->load_grade_item();

        if ($this->hidden == 1 or $this->grade_item->hidden == 1) {
            return false; //always hidden
        }

        if ($this->hidden > 1 or $this->grade_item->hidden > 1) {
            return true;
        }

        return false;
    }

    /**
     * Check grade hidden status. Uses data from both grade item and grade.
     * @return int 0 means visible, 1 hidden always, timestamp hidden until
     */
    function get_hidden() {
        $this->load_grade_item();

        $item_hidden = $this->grade_item->get_hidden();

        if ($item_hidden == 1) {
            return 1;

        } else if ($item_hidden == 0) {
            return $this->hidden;

        } else {
            if ($this->hidden == 0) {
                return $item_hidden;
            } else if ($this->hidden == 1) {
                return 1;
            } else if ($this->hidden > $item_hidden) {
                return $this->hidden;
            } else {
                return $item_hidden;
            }
        }
    }

    /**
     * Set the hidden status of grade, 0 mean visible, 1 always hidden, number means date to hide until.
     * @param boolean $cascade ignored
     * @param int $hidden new hidden status
     */
    function set_hidden($hidden, $cascade=false) {
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

        if ($source_max == $source_min or $target_min == $target_max) {
            // prevent division by 0
            return $target_max;
        }

        $factor = ($rawgrade - $source_min) / ($source_max - $source_min);
        $diff = $target_max - $target_min;
        $standardised_value = $factor * $diff + $target_min;
        return $standardised_value;
    }

    /**
     * Return array of grade item ids that are either hidden or indirectly depend
     * on hidden grades, excluded grades are not returned.
     * THIS IS A REALLY BIG HACK! to be replaced by conditional aggregation of hidden grades in 2.0
     *
     * @static
     * @param array $grades all course grades of one user, & used for better internal caching
     * @param array $items $grade_items array of grade items, & used for better internal caching
     * @return array
     */
    function get_hiding_affected(&$grade_grades, &$grade_items) {
        global $CFG;

        if (count($grade_grades) !== count($grade_items)) {
            error('Incorrect size of arrays in params of grade_grade::get_hiding_affected()!');
        }

        $dependson = array();
        $todo = array();
        $unknown = array();  // can not find altered
        $altered = array();  // altered grades

        $hiddenfound = false;
        foreach($grade_grades as $itemid=>$unused) {
            $grade_grade =& $grade_grades[$itemid];
            if ($grade_grade->is_excluded()) {
                //nothing to do, aggregation is ok
            } else if ($grade_grade->is_hidden()) {
                $hiddenfound = true;
                $altered[$grade_grade->itemid] = null;
            } else if ($grade_grade->is_locked() or $grade_grade->is_overridden()) {
                // no need to recalculate locked or overridden grades
            } else {
                $dependson[$grade_grade->itemid] = $grade_items[$grade_grade->itemid]->depends_on();
                if (!empty($dependson[$grade_grade->itemid])) {
                    $todo[] = $grade_grade->itemid;
                }
            }
        }
        if (!$hiddenfound) {
            return array('unknown'=>array(), 'altered'=>array());
        }

        $max = count($todo);
        for($i=0; $i<$max; $i++) {
            $found = false;
            foreach($todo as $key=>$do) {
                if (array_intersect($dependson[$do], $unknown)) {
                    // this item depends on hidden grade indirectly
                    $unknown[$do] = $do;
                    unset($todo[$key]);
                    $found = true;
                    continue;

                } else if (!array_intersect($dependson[$do], $todo)) {
                    if (!array_intersect($dependson[$do], array_keys($altered))) {
                        // hiding does not affect this grade
                        unset($todo[$key]);
                        $found = true;
                        continue;

                    } else {
                        // depends on altered grades - we should try to recalculate if possible
                        if ($grade_items[$do]->is_calculated() or (!$grade_items[$do]->is_category_item() and !$grade_items[$do]->is_course_item())) {
                            $unknown[$do] = $do;
                            unset($todo[$key]);
                            $found = true;
                            continue;

                        } else {
                            $grade_category = $grade_items[$do]->load_item_category();

                            $values = array();
                            foreach ($dependson[$do] as $itemid) {
                                if (array_key_exists($itemid, $altered)) {
                                    $values[$itemid] = $altered[$itemid];
                                } elseif (!empty($values[$itemid])) {
                                    $values[$itemid] = $grade_grades[$itemid]->finalgrade;
                                }
                            }

                            foreach ($values as $itemid=>$value) {
                                if ($grade_grades[$itemid]->is_excluded()) {
                                    unset($values[$itemid]);
                                    continue;
                                }
                                $values[$itemid] = grade_grade::standardise_score($value, $grade_items[$itemid]->grademin, $grade_items[$itemid]->grademax, 0, 1);
                            }

                            if ($grade_category->aggregateonlygraded) {
                                foreach ($values as $itemid=>$value) {
                                    if (is_null($value)) {
                                        unset($values[$itemid]);
                                    }
                                }
                            } else {
                                foreach ($values as $itemid=>$value) {
                                    if (is_null($value)) {
                                        $values[$itemid] = 0;
                                    }
                                }
                            }

                            // limit and sort
                            $grade_category->apply_limit_rules($values, $grade_items);
                            asort($values, SORT_NUMERIC);

                            // let's see we have still enough grades to do any statistics
                            if (count($values) == 0) {
                                // not enough attempts yet
                                $altered[$do] = null;
                                unset($todo[$key]);
                                $found = true;
                                continue;
                            }

                            $agg_grade = $grade_category->aggregate_values($values, $grade_items);

                            // recalculate the rawgrade back to requested range
                            $finalgrade = grade_grade::standardise_score($agg_grade, 0, 1, $grade_items[$do]->grademin, $grade_items[$do]->grademax);

                            $finalgrade = $grade_items[$do]->bounded_grade($finalgrade);

                            $altered[$do] = $finalgrade;
                            unset($todo[$key]);
                            $found = true;
                            continue;
                        }
                    }
                }
            }
            if (!$found) {
                break;
            }
        }

        return array('unknown'=>$unknown, 'altered'=>$altered);
    }

    /**
     * Returns true if the grade's value is superior or equal to the grade item's gradepass value, false otherwise.
     * @param object $grade_item An optional grade_item of which gradepass value we can use, saves having to load the grade_grade's grade_item
     * @return boolean
     */
    function is_passed($grade_item = null) {
        if (empty($grade_item)) {
            if (!isset($this->grade_item)) {
                $this->load_grade_item();
            }
        } else {
            $this->grade_item = $grade_item;
            $this->itemid = $grade_item->id;
        }

        // Return null if finalgrade is null
        if (is_null($this->finalgrade)) {
            return null;
        }

        // Return null if gradepass == grademin or gradepass is null
        if (is_null($this->grade_item->gradepass) || $this->grade_item->gradepass == $this->grade_item->grademin) {
            return null;
        }

        return $this->finalgrade >= $this->grade_item->gradepass;
    }

    function insert($source=null) {
        // TODO: dategraded hack - do not update times, they are used for submission and grading
        //$this->timecreated = $this->timemodified = time();
        return parent::insert($source);
    }

    /**
     * In addition to update() as defined in grade_object rounds the float numbers using php function,
     * the reason is we need to compare the db value with computed number to skip updates if possible.
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function update($source=null) {
        $this->rawgrade    = grade_floatval($this->rawgrade);
        $this->finalgrade  = grade_floatval($this->finalgrade);
        $this->rawgrademin = grade_floatval($this->rawgrademin);
        $this->rawgrademax = grade_floatval($this->rawgrademax);
        return parent::update($source);
    }
}
?>

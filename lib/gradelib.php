<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of functions for gradebook - both public and internal
 *
 * @package   core_grades
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/** Include essential files */
require_once($CFG->libdir . '/grade/constants.php');

require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/grade/grade_scale.php');
require_once($CFG->libdir . '/grade/grade_outcome.php');

/////////////////////////////////////////////////////////////////////
///// Start of public API for communication with modules/blocks /////
/////////////////////////////////////////////////////////////////////

/**
 * Submit new or update grade; update/create grade_item definition. Grade must have userid specified,
 * rawgrade and feedback with format are optional. rawgrade NULL means 'Not graded'.
 * Missing property or key means does not change the existing value.
 *
 * Only following grade item properties can be changed 'itemname', 'idnumber', 'gradetype', 'grademax',
 * 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted' and 'hidden'. 'reset' means delete all current grades including locked ones.
 *
 * Manual, course or category items can not be updated by this function.
 *
 * @category grade
 * @param string $source Source of the grade such as 'mod/assignment'
 * @param int    $courseid ID of course
 * @param string $itemtype Type of grade item. For example, mod or block
 * @param string $itemmodule More specific then $itemtype. For example, assignment or forum. May be NULL for some item types
 * @param int    $iteminstance Instance ID of graded item
 * @param int    $itemnumber Most probably 0. Modules can use other numbers when having more than one grade for each user
 * @param mixed  $grades Grade (object, array) or several grades (arrays of arrays or objects), NULL if updating grade_item definition only
 * @param mixed  $itemdetails Object or array describing the grading item, NULL if no change
 * @param bool   $isbulkupdate If bulk grade update is happening.
 * @return int Returns GRADE_UPDATE_OK, GRADE_UPDATE_FAILED, GRADE_UPDATE_MULTIPLE or GRADE_UPDATE_ITEM_LOCKED
 */
function grade_update($source, $courseid, $itemtype, $itemmodule, $iteminstance, $itemnumber, $grades = null,
        $itemdetails = null, $isbulkupdate = false) {
    global $USER, $CFG, $DB;

    // only following grade_item properties can be changed in this function
    $allowed = array('itemname', 'idnumber', 'gradetype', 'grademax', 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted', 'hidden');
    // list of 10,5 numeric fields
    $floats  = array('grademin', 'grademax', 'multfactor', 'plusfactor');

    // grade item identification
    $params = compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber');

    if (is_null($courseid) or is_null($itemtype)) {
        debugging('Missing courseid or itemtype');
        return GRADE_UPDATE_FAILED;
    }

    if (!$gradeitems = grade_item::fetch_all($params)) {
        // create a new one
        $gradeitem = false;
    } else if (count($gradeitems) == 1) {
        $gradeitem = reset($gradeitems);
        unset($gradeitems); // Release memory.
    } else {
        debugging('Found more than one grade item');
        return GRADE_UPDATE_MULTIPLE;
    }

    if (!empty($itemdetails['deleted'])) {
        if ($gradeitem) {
            if ($gradeitem->delete($source)) {
                return GRADE_UPDATE_OK;
            } else {
                return GRADE_UPDATE_FAILED;
            }
        }
        return GRADE_UPDATE_OK;
    }

/// Create or update the grade_item if needed

    if (!$gradeitem) {
        if ($itemdetails) {
            $itemdetails = (array)$itemdetails;

            // grademin and grademax ignored when scale specified
            if (array_key_exists('scaleid', $itemdetails)) {
                if ($itemdetails['scaleid']) {
                    unset($itemdetails['grademin']);
                    unset($itemdetails['grademax']);
                }
            }

            foreach ($itemdetails as $k=>$v) {
                if (!in_array($k, $allowed)) {
                    // ignore it
                    continue;
                }
                if ($k == 'gradetype' and $v == GRADE_TYPE_NONE) {
                    // no grade item needed!
                    return GRADE_UPDATE_OK;
                }
                $params[$k] = $v;
            }
        }
        $gradeitem = new grade_item($params);
        $gradeitem->insert(null, $isbulkupdate);

    } else {
        if ($gradeitem->is_locked()) {
            // no notice() here, test returned value instead!
            return GRADE_UPDATE_ITEM_LOCKED;
        }

        if ($itemdetails) {
            $itemdetails = (array)$itemdetails;
            $update = false;
            foreach ($itemdetails as $k=>$v) {
                if (!in_array($k, $allowed)) {
                    // ignore it
                    continue;
                }
                if (in_array($k, $floats)) {
                    if (grade_floats_different($gradeitem->{$k}, $v)) {
                        $gradeitem->{$k} = $v;
                        $update = true;
                    }

                } else {
                    if ($gradeitem->{$k} != $v) {
                        $gradeitem->{$k} = $v;
                        $update = true;
                    }
                }
            }
            if ($update) {
                $gradeitem->update(null, $isbulkupdate);
            }
        }
    }

/// reset grades if requested
    if (!empty($itemdetails['reset'])) {
        $gradeitem->delete_all_grades('reset');
        return GRADE_UPDATE_OK;
    }

/// Some extra checks
    // do we use grading?
    if ($gradeitem->gradetype == GRADE_TYPE_NONE) {
        return GRADE_UPDATE_OK;
    }

    // no grade submitted
    if (empty($grades)) {
        return GRADE_UPDATE_OK;
    }

/// Finally start processing of grades
    if (is_object($grades)) {
        $grades = array($grades->userid=>$grades);
    } else {
        if (array_key_exists('userid', $grades)) {
            $grades = array($grades['userid']=>$grades);
        }
    }

/// normalize and verify grade array
    foreach($grades as $k=>$g) {
        if (!is_array($g)) {
            $g = (array)$g;
            $grades[$k] = $g;
        }

        if (empty($g['userid']) or $k != $g['userid']) {
            debugging('Incorrect grade array index, must be user id! Grade ignored.');
            unset($grades[$k]);
        }
    }

    if (empty($grades)) {
        return GRADE_UPDATE_FAILED;
    }

    $count = count($grades);
    if ($count > 0 and $count < 200) {
        list($uids, $params) = $DB->get_in_or_equal(array_keys($grades), SQL_PARAMS_NAMED, $start='uid');
        $params['gid'] = $gradeitem->id;
        $sql = "SELECT * FROM {grade_grades} WHERE itemid = :gid AND userid $uids";

    } else {
        $sql = "SELECT * FROM {grade_grades} WHERE itemid = :gid";
        $params = array('gid' => $gradeitem->id);
    }

    $rs = $DB->get_recordset_sql($sql, $params);

    $failed = false;

    while (count($grades) > 0) {
        $gradegrade = null;
        $grade       = null;

        foreach ($rs as $gd) {

            $userid = $gd->userid;
            if (!isset($grades[$userid])) {
                // this grade not requested, continue
                continue;
            }
            // existing grade requested
            $grade       = $grades[$userid];
            $gradegrade = new grade_grade($gd, false);
            unset($grades[$userid]);
            break;
        }

        if (is_null($gradegrade)) {
            if (count($grades) == 0) {
                // No more grades to process.
                break;
            }

            $grade       = reset($grades);
            $userid      = $grade['userid'];
            $gradegrade = new grade_grade(array('itemid' => $gradeitem->id, 'userid' => $userid), false);
            $gradegrade->load_optional_fields(); // add feedback and info too
            unset($grades[$userid]);
        }

        $rawgrade       = false;
        $feedback       = false;
        $feedbackformat = FORMAT_MOODLE;
        $feedbackfiles = [];
        $usermodified   = $USER->id;
        $datesubmitted  = null;
        $dategraded     = null;

        if (array_key_exists('rawgrade', $grade)) {
            $rawgrade = $grade['rawgrade'];
        }

        if (array_key_exists('feedback', $grade)) {
            $feedback = $grade['feedback'];
        }

        if (array_key_exists('feedbackformat', $grade)) {
            $feedbackformat = $grade['feedbackformat'];
        }

        if (array_key_exists('feedbackfiles', $grade)) {
            $feedbackfiles = $grade['feedbackfiles'];
        }

        if (array_key_exists('usermodified', $grade)) {
            $usermodified = $grade['usermodified'];
        }

        if (array_key_exists('datesubmitted', $grade)) {
            $datesubmitted = $grade['datesubmitted'];
        }

        if (array_key_exists('dategraded', $grade)) {
            $dategraded = $grade['dategraded'];
        }

        // update or insert the grade
        if (!$gradeitem->update_raw_grade($userid, $rawgrade, $source, $feedback, $feedbackformat, $usermodified,
                $dategraded, $datesubmitted, $gradegrade, $feedbackfiles, $isbulkupdate)) {
            $failed = true;
        }
    }

    if ($rs) {
        $rs->close();
    }

    if (!$failed) {
        return GRADE_UPDATE_OK;
    } else {
        return GRADE_UPDATE_FAILED;
    }
}

/**
 * Updates a user's outcomes. Manual outcomes can not be updated.
 *
 * @category grade
 * @param string $source Source of the grade such as 'mod/assignment'
 * @param int    $courseid ID of course
 * @param string $itemtype Type of grade item. For example, 'mod' or 'block'
 * @param string $itemmodule More specific then $itemtype. For example, 'forum' or 'quiz'. May be NULL for some item types
 * @param int    $iteminstance Instance ID of graded item. For example the forum ID.
 * @param int    $userid ID of the graded user
 * @param array  $data Array consisting of grade item itemnumber ({@link grade_update()}) => outcomegrade
 * @return bool returns true if grade items were found and updated successfully
 */
function grade_update_outcomes($source, $courseid, $itemtype, $itemmodule, $iteminstance, $userid, $data) {
    if ($items = grade_item::fetch_all(array('itemtype'=>$itemtype, 'itemmodule'=>$itemmodule, 'iteminstance'=>$iteminstance, 'courseid'=>$courseid))) {
        $result = true;
        foreach ($items as $item) {
            if (!array_key_exists($item->itemnumber, $data)) {
                continue;
            }
            $grade = $data[$item->itemnumber] < 1 ? null : $data[$item->itemnumber];
            $result = ($item->update_final_grade($userid, $grade, $source) && $result);
        }
        return $result;
    }
    return false; //grade items not found
}

/**
 * Return true if the course needs regrading.
 *
 * @param int $courseid The course ID
 * @return bool true if course grades need updating.
 */
function grade_needs_regrade_final_grades($courseid) {
    $course_item = grade_item::fetch_course_item($courseid);
    return $course_item->needsupdate;
}

/**
 * Return true if the regrade process is likely to be time consuming and
 * will therefore require the progress bar.
 *
 * @param int $courseid The course ID
 * @return bool Whether the regrade process is likely to be time consuming
 */
function grade_needs_regrade_progress_bar($courseid) {
    global $DB;
    $grade_items = grade_item::fetch_all(array('courseid' => $courseid));
    if (!$grade_items) {
        // If there are no grade items then we definitely don't need a progress bar!
        return false;
    }

    list($sql, $params) = $DB->get_in_or_equal(array_keys($grade_items), SQL_PARAMS_NAMED, 'gi');
    $gradecount = $DB->count_records_select('grade_grades', 'itemid ' . $sql, $params);

    // This figure may seem arbitrary, but after analysis it seems that 100 grade_grades can be calculated in ~= 0.5 seconds.
    // Any longer than this and we want to show the progress bar.
    return $gradecount > 100;
}

/**
 * Check whether regarding of final grades is required and, if so, perform the regrade.
 *
 * If the regrade is expected to be time consuming (see grade_needs_regrade_progress_bar), then this
 * function will output the progress bar, and redirect to the current PAGE->url after regrading
 * completes. Otherwise the regrading will happen immediately and the page will be loaded as per
 * normal.
 *
 * A callback may be specified, which is called if regrading has taken place.
 * The callback may optionally return a URL which will be redirected to when the progress bar is present.
 *
 * @param stdClass $course The course to regrade
 * @param callable $callback A function to call if regrading took place
 * @return moodle_url|false The URL to redirect to if redirecting
 */
function grade_regrade_final_grades_if_required($course, callable $callback = null) {
    global $PAGE, $OUTPUT;

    if (!grade_needs_regrade_final_grades($course->id)) {
        return false;
    }

    if (grade_needs_regrade_progress_bar($course->id)) {
        if ($PAGE->state !== moodle_page::STATE_IN_BODY) {
            $PAGE->set_heading($course->fullname);
            echo $OUTPUT->header();
        }
        echo $OUTPUT->heading(get_string('recalculatinggrades', 'grades'));
        $progress = new \core\progress\display(true);
        $status = grade_regrade_final_grades($course->id, null, null, $progress);

        // Show regrade errors and set the course to no longer needing regrade (stop endless loop).
        if (is_array($status)) {
            foreach ($status as $error) {
                $errortext = new \core\output\notification($error, \core\output\notification::NOTIFY_ERROR);
                echo $OUTPUT->render($errortext);
            }
            $courseitem = grade_item::fetch_course_item($course->id);
            $courseitem->regrading_finished();
        }

        if ($callback) {
            //
            $url = call_user_func($callback);
        }

        if (empty($url)) {
            $url = $PAGE->url;
        }

        echo $OUTPUT->continue_button($url);
        echo $OUTPUT->footer();
        die();
    } else {
        $result = grade_regrade_final_grades($course->id);
        if ($callback) {
            call_user_func($callback);
        }
        return $result;
    }
}

/**
 * Returns grading information for given activity, optionally with user grades.
 * Manual, course or category items can not be queried.
 *
 * This function can be VERY costly - it is doing full course grades recalculation if needsupdate = 1
 * for course grade item. So be sure you really need it.
 * If you need just certain grades consider using grade_item::refresh_grades()
 * together with grade_item::get_grade() instead.
 *
 * @param int    $courseid ID of course
 * @param string $itemtype Type of grade item. For example, 'mod' or 'block'
 * @param string $itemmodule More specific then $itemtype. For example, 'forum' or 'quiz'. May be NULL for some item types
 * @param int    $iteminstance ID of the item module
 * @param mixed  $userid_or_ids Either a single user ID, an array of user IDs or null. If user ID or IDs are not supplied returns information about grade_item
 * @return stdClass Object with keys {items, outcomes, errors}, where 'items' is an array of grade
 *               information objects (scaleid, name, grade and locked status, etc.) indexed with itemnumbers
 * @category grade
 */
function grade_get_grades($courseid, $itemtype, $itemmodule, $iteminstance, $userid_or_ids=null) {
    global $CFG;

    $return = new stdClass();
    $return->items    = array();
    $return->outcomes = array();
    $return->errors = [];

    $courseitem = grade_item::fetch_course_item($courseid);
    $needsupdate = array();
    if ($courseitem->needsupdate) {
        $result = grade_regrade_final_grades($courseid);
        if ($result !== true) {
            $needsupdate = array_keys($result);
            // Return regrade errors if the user has capability.
            $context = context_course::instance($courseid);
            if (has_capability('moodle/grade:edit', $context)) {
                $return->errors = $result;
            }
            $courseitem->regrading_finished();
        }
    }

    if ($grade_items = grade_item::fetch_all(array('itemtype'=>$itemtype, 'itemmodule'=>$itemmodule, 'iteminstance'=>$iteminstance, 'courseid'=>$courseid))) {
        foreach ($grade_items as $grade_item) {
            $decimalpoints = null;

            if (empty($grade_item->outcomeid)) {
                // prepare information about grade item
                $item = new stdClass();
                $item->id = $grade_item->id;
                $item->itemnumber = $grade_item->itemnumber;
                $item->itemtype  = $grade_item->itemtype;
                $item->itemmodule = $grade_item->itemmodule;
                $item->iteminstance = $grade_item->iteminstance;
                $item->scaleid    = $grade_item->scaleid;
                $item->name       = $grade_item->get_name();
                $item->grademin   = $grade_item->grademin;
                $item->grademax   = $grade_item->grademax;
                $item->gradepass  = $grade_item->gradepass;
                $item->locked     = $grade_item->is_locked();
                $item->hidden     = $grade_item->is_hidden();
                $item->grades     = array();

                switch ($grade_item->gradetype) {
                    case GRADE_TYPE_NONE:
                        break;

                    case GRADE_TYPE_VALUE:
                        $item->scaleid = 0;
                        break;

                    case GRADE_TYPE_TEXT:
                        $item->scaleid   = 0;
                        $item->grademin   = 0;
                        $item->grademax   = 0;
                        $item->gradepass  = 0;
                        break;
                }

                if (empty($userid_or_ids)) {
                    $userids = array();

                } else if (is_array($userid_or_ids)) {
                    $userids = $userid_or_ids;

                } else {
                    $userids = array($userid_or_ids);
                }

                if ($userids) {
                    $grade_grades = grade_grade::fetch_users_grades($grade_item, $userids, true);
                    foreach ($userids as $userid) {
                        $grade_grades[$userid]->grade_item =& $grade_item;

                        $grade = new stdClass();
                        $grade->grade          = $grade_grades[$userid]->finalgrade;
                        $grade->locked         = $grade_grades[$userid]->is_locked();
                        $grade->hidden         = $grade_grades[$userid]->is_hidden();
                        $grade->overridden     = $grade_grades[$userid]->overridden;
                        $grade->feedback       = $grade_grades[$userid]->feedback;
                        $grade->feedbackformat = $grade_grades[$userid]->feedbackformat;
                        $grade->usermodified   = $grade_grades[$userid]->usermodified;
                        $grade->datesubmitted  = $grade_grades[$userid]->get_datesubmitted();
                        $grade->dategraded     = $grade_grades[$userid]->get_dategraded();

                        // create text representation of grade
                        if ($grade_item->gradetype == GRADE_TYPE_TEXT or $grade_item->gradetype == GRADE_TYPE_NONE) {
                            $grade->grade          = null;
                            $grade->str_grade      = '-';
                            $grade->str_long_grade = $grade->str_grade;

                        } else if (in_array($grade_item->id, $needsupdate)) {
                            $grade->grade          = false;
                            $grade->str_grade      = get_string('error');
                            $grade->str_long_grade = $grade->str_grade;

                        } else if (is_null($grade->grade)) {
                            $grade->str_grade      = '-';
                            $grade->str_long_grade = $grade->str_grade;

                        } else {
                            $grade->str_grade = grade_format_gradevalue($grade->grade, $grade_item);
                            if ($grade_item->gradetype == GRADE_TYPE_SCALE or $grade_item->get_displaytype() != GRADE_DISPLAY_TYPE_REAL) {
                                $grade->str_long_grade = $grade->str_grade;
                            } else {
                                $a = new stdClass();
                                $a->grade = $grade->str_grade;
                                $a->max   = grade_format_gradevalue($grade_item->grademax, $grade_item);
                                $grade->str_long_grade = get_string('gradelong', 'grades', $a);
                            }
                        }

                        // create html representation of feedback
                        if (is_null($grade->feedback)) {
                            $grade->str_feedback = '';
                        } else {
                            $feedback = file_rewrite_pluginfile_urls(
                                $grade->feedback,
                                'pluginfile.php',
                                $grade_grades[$userid]->get_context()->id,
                                GRADE_FILE_COMPONENT,
                                GRADE_FEEDBACK_FILEAREA,
                                $grade_grades[$userid]->id
                            );

                            $grade->str_feedback = format_text($feedback, $grade->feedbackformat,
                                ['context' => $grade_grades[$userid]->get_context()]);
                        }

                        $item->grades[$userid] = $grade;
                    }
                }
                $return->items[$grade_item->itemnumber] = $item;

            } else {
                if (!$grade_outcome = grade_outcome::fetch(array('id'=>$grade_item->outcomeid))) {
                    debugging('Incorect outcomeid found');
                    continue;
                }

                // outcome info
                $outcome = new stdClass();
                $outcome->id = $grade_item->id;
                $outcome->itemnumber = $grade_item->itemnumber;
                $outcome->itemtype   = $grade_item->itemtype;
                $outcome->itemmodule = $grade_item->itemmodule;
                $outcome->iteminstance = $grade_item->iteminstance;
                $outcome->scaleid    = $grade_outcome->scaleid;
                $outcome->name       = $grade_outcome->get_name();
                $outcome->locked     = $grade_item->is_locked();
                $outcome->hidden     = $grade_item->is_hidden();

                if (empty($userid_or_ids)) {
                    $userids = array();
                } else if (is_array($userid_or_ids)) {
                    $userids = $userid_or_ids;
                } else {
                    $userids = array($userid_or_ids);
                }

                if ($userids) {
                    $grade_grades = grade_grade::fetch_users_grades($grade_item, $userids, true);
                    foreach ($userids as $userid) {
                        $grade_grades[$userid]->grade_item =& $grade_item;

                        $grade = new stdClass();
                        $grade->grade          = $grade_grades[$userid]->finalgrade;
                        $grade->locked         = $grade_grades[$userid]->is_locked();
                        $grade->hidden         = $grade_grades[$userid]->is_hidden();
                        $grade->feedback       = $grade_grades[$userid]->feedback;
                        $grade->feedbackformat = $grade_grades[$userid]->feedbackformat;
                        $grade->usermodified   = $grade_grades[$userid]->usermodified;
                        $grade->datesubmitted  = $grade_grades[$userid]->get_datesubmitted();
                        $grade->dategraded     = $grade_grades[$userid]->get_dategraded();

                        // create text representation of grade
                        if (in_array($grade_item->id, $needsupdate)) {
                            $grade->grade     = false;
                            $grade->str_grade = get_string('error');

                        } else if (is_null($grade->grade)) {
                            $grade->grade = 0;
                            $grade->str_grade = get_string('nooutcome', 'grades');

                        } else {
                            $grade->grade = (int)$grade->grade;
                            $scale = $grade_item->load_scale();
                            $grade->str_grade = format_string($scale->scale_items[(int)$grade->grade-1]);
                        }

                        // create html representation of feedback
                        if (is_null($grade->feedback)) {
                            $grade->str_feedback = '';
                        } else {
                            $grade->str_feedback = format_text($grade->feedback, $grade->feedbackformat);
                        }

                        $outcome->grades[$userid] = $grade;
                    }
                }

                if (isset($return->outcomes[$grade_item->itemnumber])) {
                    // itemnumber duplicates - lets fix them!
                    $newnumber = $grade_item->itemnumber + 1;
                    while(grade_item::fetch(array('itemtype'=>$itemtype, 'itemmodule'=>$itemmodule, 'iteminstance'=>$iteminstance, 'courseid'=>$courseid, 'itemnumber'=>$newnumber))) {
                        $newnumber++;
                    }
                    $outcome->itemnumber    = $newnumber;
                    $grade_item->itemnumber = $newnumber;
                    $grade_item->update('system');
                }

                $return->outcomes[$grade_item->itemnumber] = $outcome;

            }
        }
    }

    // sort results using itemnumbers
    ksort($return->items, SORT_NUMERIC);
    ksort($return->outcomes, SORT_NUMERIC);

    return $return;
}

///////////////////////////////////////////////////////////////////
///// End of public API for communication with modules/blocks /////
///////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////
///// Internal API: used by gradebook plugins and Moodle core /////
///////////////////////////////////////////////////////////////////

/**
 * Returns a  course gradebook setting
 *
 * @param int $courseid
 * @param string $name of setting, maybe null if reset only
 * @param string $default value to return if setting is not found
 * @param bool $resetcache force reset of internal static cache
 * @return string value of the setting, $default if setting not found, NULL if supplied $name is null
 */
function grade_get_setting($courseid, $name, $default=null, $resetcache=false) {
    global $DB;

    $cache = cache::make('core', 'gradesetting');
    $gradesetting = $cache->get($courseid) ?: array();

    if ($resetcache or empty($gradesetting)) {
        $gradesetting = array();
        $cache->set($courseid, $gradesetting);

    } else if (is_null($name)) {
        return null;

    } else if (array_key_exists($name, $gradesetting)) {
        return $gradesetting[$name];
    }

    if (!$data = $DB->get_record('grade_settings', array('courseid'=>$courseid, 'name'=>$name))) {
        $result = null;
    } else {
        $result = $data->value;
    }

    if (is_null($result)) {
        $result = $default;
    }

    $gradesetting[$name] = $result;
    $cache->set($courseid, $gradesetting);
    return $result;
}

/**
 * Returns all course gradebook settings as object properties
 *
 * @param int $courseid
 * @return object
 */
function grade_get_settings($courseid) {
    global $DB;

     $settings = new stdClass();
     $settings->id = $courseid;

    if ($records = $DB->get_records('grade_settings', array('courseid'=>$courseid))) {
        foreach ($records as $record) {
            $settings->{$record->name} = $record->value;
        }
    }

    return $settings;
}

/**
 * Add, update or delete a course gradebook setting
 *
 * @param int $courseid The course ID
 * @param string $name Name of the setting
 * @param string $value Value of the setting. NULL means delete the setting.
 */
function grade_set_setting($courseid, $name, $value) {
    global $DB;

    if (is_null($value)) {
        $DB->delete_records('grade_settings', array('courseid'=>$courseid, 'name'=>$name));

    } else if (!$existing = $DB->get_record('grade_settings', array('courseid'=>$courseid, 'name'=>$name))) {
        $data = new stdClass();
        $data->courseid = $courseid;
        $data->name     = $name;
        $data->value    = $value;
        $DB->insert_record('grade_settings', $data);

    } else {
        $data = new stdClass();
        $data->id       = $existing->id;
        $data->value    = $value;
        $DB->update_record('grade_settings', $data);
    }

    grade_get_setting($courseid, null, null, true); // reset the cache
}

/**
 * Returns string representation of grade value
 *
 * @param float|null $value The grade value
 * @param object $grade_item Grade item object passed by reference to prevent scale reloading
 * @param bool $localized use localised decimal separator
 * @param int $displaytype type of display. For example GRADE_DISPLAY_TYPE_REAL, GRADE_DISPLAY_TYPE_PERCENTAGE, GRADE_DISPLAY_TYPE_LETTER
 * @param int $decimals The number of decimal places when displaying float values
 * @return string
 */
function grade_format_gradevalue(?float $value, &$grade_item, $localized=true, $displaytype=null, $decimals=null) {
    if ($grade_item->gradetype == GRADE_TYPE_NONE or $grade_item->gradetype == GRADE_TYPE_TEXT) {
        return '';
    }

    // no grade yet?
    if (is_null($value)) {
        return '-';
    }

    if ($grade_item->gradetype != GRADE_TYPE_VALUE and $grade_item->gradetype != GRADE_TYPE_SCALE) {
        //unknown type??
        return '';
    }

    if (is_null($displaytype)) {
        $displaytype = $grade_item->get_displaytype();
    }

    if (is_null($decimals)) {
        $decimals = $grade_item->get_decimals();
    }

    switch ($displaytype) {
        case GRADE_DISPLAY_TYPE_REAL:
            return grade_format_gradevalue_real($value, $grade_item, $decimals, $localized);

        case GRADE_DISPLAY_TYPE_PERCENTAGE:
            return grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized);

        case GRADE_DISPLAY_TYPE_LETTER:
            return grade_format_gradevalue_letter($value, $grade_item);

        case GRADE_DISPLAY_TYPE_REAL_PERCENTAGE:
            return grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_REAL_LETTER:
            return grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_letter($value, $grade_item) . ')';

        case GRADE_DISPLAY_TYPE_PERCENTAGE_REAL:
            return grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_LETTER_REAL:
            return grade_format_gradevalue_letter($value, $grade_item) . ' (' .
                    grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE:
            return grade_format_gradevalue_letter($value, $grade_item) . ' (' .
                    grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER:
            return grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_letter($value, $grade_item) . ')';
        default:
            return '';
    }
}

/**
 * Returns a float representation of a grade value
 *
 * @param float|null $value The grade value
 * @param object $grade_item Grade item object
 * @param int $decimals The number of decimal places
 * @param bool $localized use localised decimal separator
 * @return string
 */
function grade_format_gradevalue_real(?float $value, $grade_item, $decimals, $localized) {
    if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
        if (!$scale = $grade_item->load_scale()) {
            return get_string('error');
        }

        $value = $grade_item->bounded_grade($value);
        return format_string($scale->scale_items[$value-1]);

    } else {
        return format_float($value, $decimals, $localized);
    }
}

/**
 * Returns a percentage representation of a grade value
 *
 * @param float|null $value The grade value
 * @param object $grade_item Grade item object
 * @param int $decimals The number of decimal places
 * @param bool $localized use localised decimal separator
 * @return string
 */
function grade_format_gradevalue_percentage(?float $value, $grade_item, $decimals, $localized) {
    $min = $grade_item->grademin;
    $max = $grade_item->grademax;
    if ($min == $max) {
        return '';
    }
    $value = $grade_item->bounded_grade($value);
    $percentage = (($value-$min)*100)/($max-$min);
    return format_float($percentage, $decimals, $localized).' %';
}

/**
 * Returns a letter grade representation of a grade value
 * The array of grade letters used is produced by {@link grade_get_letters()} using the course context
 *
 * @param float|null $value The grade value
 * @param object $grade_item Grade item object
 * @return string
 */
function grade_format_gradevalue_letter(?float $value, $grade_item) {
    global $CFG;
    $context = context_course::instance($grade_item->courseid, IGNORE_MISSING);
    if (!$letters = grade_get_letters($context)) {
        return ''; // no letters??
    }

    if (is_null($value)) {
        return '-';
    }

    $value = grade_grade::standardise_score($value, $grade_item->grademin, $grade_item->grademax, 0, 100);
    $value = bounded_number(0, $value, 100); // just in case

    $gradebookcalculationsfreeze = 'gradebook_calculations_freeze_' . $grade_item->courseid;

    foreach ($letters as $boundary => $letter) {
        if (property_exists($CFG, $gradebookcalculationsfreeze) && (int)$CFG->{$gradebookcalculationsfreeze} <= 20160518) {
            // Do nothing.
        } else {
            // The boundary is a percentage out of 100 so use 0 as the min and 100 as the max.
            $boundary = grade_grade::standardise_score($boundary, 0, 100, 0, 100);
        }
        if ($value >= $boundary) {
            return format_string($letter);
        }
    }
    return '-'; // no match? maybe '' would be more correct
}


/**
 * Returns grade options for gradebook grade category menu
 *
 * @param int $courseid The course ID
 * @param bool $includenew Include option for new category at array index -1
 * @return array of grade categories in course
 */
function grade_get_categories_menu($courseid, $includenew=false) {
    $result = array();
    if (!$categories = grade_category::fetch_all(array('courseid'=>$courseid))) {
        //make sure course category exists
        if (!grade_category::fetch_course_category($courseid)) {
            debugging('Can not create course grade category!');
            return $result;
        }
        $categories = grade_category::fetch_all(array('courseid'=>$courseid));
    }
    foreach ($categories as $key=>$category) {
        if ($category->is_course_category()) {
            $result[$category->id] = get_string('uncategorised', 'grades');
            unset($categories[$key]);
        }
    }
    if ($includenew) {
        $result[-1] = get_string('newcategory', 'grades');
    }
    $cats = array();
    foreach ($categories as $category) {
        $cats[$category->id] = $category->get_name();
    }
    core_collator::asort($cats);

    return ($result+$cats);
}

/**
 * Returns the array of grade letters to be used in the supplied context
 *
 * @param object $context Context object or null for defaults
 * @return array of grade_boundary (minimum) => letter_string
 */
function grade_get_letters($context=null) {
    global $DB;

    if (empty($context)) {
        //default grading letters
        return array('93'=>'A', '90'=>'A-', '87'=>'B+', '83'=>'B', '80'=>'B-', '77'=>'C+', '73'=>'C', '70'=>'C-', '67'=>'D+', '60'=>'D', '0'=>'F');
    }

    $cache = cache::make('core', 'grade_letters');
    $data = $cache->get($context->id);

    if (!empty($data)) {
        return $data;
    }

    $letters = array();

    $contexts = $context->get_parent_context_ids();
    array_unshift($contexts, $context->id);

    foreach ($contexts as $ctxid) {
        if ($records = $DB->get_records('grade_letters', array('contextid'=>$ctxid), 'lowerboundary DESC')) {
            foreach ($records as $record) {
                $letters[$record->lowerboundary] = $record->letter;
            }
        }

        if (!empty($letters)) {
            // Cache the grade letters for this context.
            $cache->set($context->id, $letters);
            return $letters;
        }
    }

    $letters = grade_get_letters(null);
    // Cache the grade letters for this context.
    $cache->set($context->id, $letters);
    return $letters;
}


/**
 * Verify new value of grade item idnumber. Checks for uniqueness of new ID numbers. Old ID numbers are kept intact.
 *
 * @param string $idnumber string (with magic quotes)
 * @param int $courseid ID numbers are course unique only
 * @param grade_item $grade_item The grade item this idnumber is associated with
 * @param stdClass $cm used for course module idnumbers and items attached to modules
 * @return bool true means idnumber ok
 */
function grade_verify_idnumber($idnumber, $courseid, $grade_item=null, $cm=null) {
    global $DB;

    if ($idnumber == '') {
        //we allow empty idnumbers
        return true;
    }

    // keep existing even when not unique
    if ($cm and $cm->idnumber == $idnumber) {
        if ($grade_item and $grade_item->itemnumber != 0) {
            // grade item with itemnumber > 0 can't have the same idnumber as the main
            // itemnumber 0 which is synced with course_modules
            return false;
        }
        return true;
    } else if ($grade_item and $grade_item->idnumber == $idnumber) {
        return true;
    }

    if ($DB->record_exists('course_modules', array('course'=>$courseid, 'idnumber'=>$idnumber))) {
        return false;
    }

    if ($DB->record_exists('grade_items', array('courseid'=>$courseid, 'idnumber'=>$idnumber))) {
        return false;
    }

    return true;
}

/**
 * Force final grade recalculation in all course items
 *
 * @param int $courseid The course ID to recalculate
 */
function grade_force_full_regrading($courseid) {
    global $DB;
    $DB->set_field('grade_items', 'needsupdate', 1, array('courseid'=>$courseid));
}

/**
 * Forces regrading of all site grades. Used when changing site setings
 */
function grade_force_site_regrading() {
    global $CFG, $DB;
    $DB->set_field('grade_items', 'needsupdate', 1);
}

/**
 * Recover a user's grades from grade_grades_history
 * @param int $userid the user ID whose grades we want to recover
 * @param int $courseid the relevant course
 * @return bool true if successful or false if there was an error or no grades could be recovered
 */
function grade_recover_history_grades($userid, $courseid) {
    global $CFG, $DB;

    if ($CFG->disablegradehistory) {
        debugging('Attempting to recover grades when grade history is disabled.');
        return false;
    }

    //Were grades recovered? Flag to return.
    $recoveredgrades = false;

    //Check the user is enrolled in this course
    //Dont bother checking if they have a gradeable role. They may get one later so recover
    //whatever grades they have now just in case.
    $course_context = context_course::instance($courseid);
    if (!is_enrolled($course_context, $userid)) {
        debugging('Attempting to recover the grades of a user who is deleted or not enrolled. Skipping recover.');
        return false;
    }

    //Check for existing grades for this user in this course
    //Recovering grades when the user already has grades can lead to duplicate indexes and bad data
    //In the future we could move the existing grades to the history table then recover the grades from before then
    $sql = "SELECT gg.id
              FROM {grade_grades} gg
              JOIN {grade_items} gi ON gi.id = gg.itemid
             WHERE gi.courseid = :courseid AND gg.userid = :userid";
    $params = array('userid' => $userid, 'courseid' => $courseid);
    if ($DB->record_exists_sql($sql, $params)) {
        debugging('Attempting to recover the grades of a user who already has grades. Skipping recover.');
        return false;
    } else {
        //Retrieve the user's old grades
        //have history ID as first column to guarantee we a unique first column
        $sql = "SELECT h.id, gi.itemtype, gi.itemmodule, gi.iteminstance as iteminstance, gi.itemnumber, h.source, h.itemid, h.userid, h.rawgrade, h.rawgrademax,
                       h.rawgrademin, h.rawscaleid, h.usermodified, h.finalgrade, h.hidden, h.locked, h.locktime, h.exported, h.overridden, h.excluded, h.feedback,
                       h.feedbackformat, h.information, h.informationformat, h.timemodified, itemcreated.tm AS timecreated
                  FROM {grade_grades_history} h
                  JOIN (SELECT itemid, MAX(id) AS id
                          FROM {grade_grades_history}
                         WHERE userid = :userid1
                      GROUP BY itemid) maxquery ON h.id = maxquery.id AND h.itemid = maxquery.itemid
                  JOIN {grade_items} gi ON gi.id = h.itemid
                  JOIN (SELECT itemid, MAX(timemodified) AS tm
                          FROM {grade_grades_history}
                         WHERE userid = :userid2 AND action = :insertaction
                      GROUP BY itemid) itemcreated ON itemcreated.itemid = h.itemid
                 WHERE gi.courseid = :courseid";
        $params = array('userid1' => $userid, 'userid2' => $userid , 'insertaction' => GRADE_HISTORY_INSERT, 'courseid' => $courseid);
        $oldgrades = $DB->get_records_sql($sql, $params);

        //now move the old grades to the grade_grades table
        foreach ($oldgrades as $oldgrade) {
            unset($oldgrade->id);

            $grade = new grade_grade($oldgrade, false);//2nd arg false as dont want to try and retrieve a record from the DB
            $grade->insert($oldgrade->source);

            //dont include default empty grades created when activities are created
            if (!is_null($oldgrade->finalgrade) || !is_null($oldgrade->feedback)) {
                $recoveredgrades = true;
            }
        }
    }

    //Some activities require manual grade synching (moving grades from the activity into the gradebook)
    //If the student was deleted when synching was done they may have grades in the activity that haven't been moved across
    grade_grab_course_grades($courseid, null, $userid);

    return $recoveredgrades;
}

/**
 * Updates all final grades in course.
 *
 * @param int $courseid The course ID
 * @param int $userid If specified try to do a quick regrading of the grades of this user only
 * @param object $updated_item Optional grade item to be marked for regrading. It is required if $userid is set.
 * @param \core\progress\base $progress If provided, will be used to update progress on this long operation.
 * @return array|true true if ok, array of errors if problems found. Grade item id => error message
 */
function grade_regrade_final_grades($courseid, $userid=null, $updated_item=null, $progress=null) {
    // This may take a very long time and extra memory.
    \core_php_time_limit::raise();
    raise_memory_limit(MEMORY_EXTRA);

    $course_item = grade_item::fetch_course_item($courseid);

    if ($progress == null) {
        $progress = new \core\progress\none();
    }

    if ($userid) {
        // one raw grade updated for one user
        if (empty($updated_item)) {
            throw new \moodle_exception("cannotbenull", 'debug', '', "updated_item");
        }
        if ($course_item->needsupdate) {
            $updated_item->force_regrading();
            return array($course_item->id =>'Can not do fast regrading after updating of raw grades');
        }

    } else {
        if (!$course_item->needsupdate) {
            // nothing to do :-)
            return true;
        }
    }

    // Categories might have to run some processing before we fetch the grade items.
    // This gives them a final opportunity to update and mark their children to be updated.
    // We need to work on the children categories up to the parent ones, so that, for instance,
    // if a category total is updated it will be reflected in the parent category.
    $cats = grade_category::fetch_all(array('courseid' => $courseid));
    $flatcattree = array();
    foreach ($cats as $cat) {
        if (!isset($flatcattree[$cat->depth])) {
            $flatcattree[$cat->depth] = array();
        }
        $flatcattree[$cat->depth][] = $cat;
    }
    krsort($flatcattree);
    foreach ($flatcattree as $depth => $cats) {
        foreach ($cats as $cat) {
            $cat->pre_regrade_final_grades();
        }
    }

    $progresstotal = 0;
    $progresscurrent = 0;

    $grade_items = grade_item::fetch_all(array('courseid'=>$courseid));
    $depends_on = array();

    foreach ($grade_items as $gid=>$gitem) {
        if ((!empty($updated_item) and $updated_item->id == $gid) ||
                $gitem->is_course_item() || $gitem->is_category_item() || $gitem->is_calculated()) {
            $grade_items[$gid]->needsupdate = 1;
        }

        // We load all dependencies of these items later we can discard some grade_items based on this.
        if ($grade_items[$gid]->needsupdate) {
            $depends_on[$gid] = $grade_items[$gid]->depends_on();
            $progresstotal++;
        }
    }

    $progress->start_progress('regrade_course', $progresstotal);

    $errors = array();
    $finalids = array();
    $updatedids = array();
    $gids     = array_keys($grade_items);
    $failed = 0;

    while (count($finalids) < count($gids)) { // work until all grades are final or error found
        $count = 0;
        foreach ($gids as $gid) {
            if (in_array($gid, $finalids)) {
                continue; // already final
            }

            if (!$grade_items[$gid]->needsupdate) {
                $finalids[] = $gid; // we can make it final - does not need update
                continue;
            }
            $thisprogress = $progresstotal;
            foreach ($grade_items as $item) {
                if ($item->needsupdate) {
                    $thisprogress--;
                }
            }
            // Clip between $progresscurrent and $progresstotal.
            $thisprogress = max(min($thisprogress, $progresstotal), $progresscurrent);
            $progress->progress($thisprogress);
            $progresscurrent = $thisprogress;

            foreach ($depends_on[$gid] as $did) {
                if (!in_array($did, $finalids)) {
                    // This item depends on something that is not yet in finals array.
                    continue 2;
                }
            }

            // If this grade item has no dependancy with any updated item at all, then remove it from being recalculated.

            // When we get here, all of this grade item's decendents are marked as final so they would be marked as updated too
            // if they would have been regraded. We don't need to regrade items which dependants (not only the direct ones
            // but any dependant in the cascade) have not been updated.

            // If $updated_item was specified we discard the grade items that do not depend on it or on any grade item that
            // depend on $updated_item.

            // Here we check to see if the direct decendants are marked as updated.
            if (!empty($updated_item) && $gid != $updated_item->id && !in_array($updated_item->id, $depends_on[$gid])) {

                // We need to ensure that none of this item's dependencies have been updated.
                // If we find that one of the direct decendants of this grade item is marked as updated then this
                // grade item needs to be recalculated and marked as updated.
                // Being marked as updated is done further down in the code.

                $updateddependencies = false;
                foreach ($depends_on[$gid] as $dependency) {
                    if (in_array($dependency, $updatedids)) {
                        $updateddependencies = true;
                        break;
                    }
                }
                if ($updateddependencies === false) {
                    // If no direct descendants are marked as updated, then we don't need to update this grade item. We then mark it
                    // as final.
                    $count++;
                    $finalids[] = $gid;
                    continue;
                }
            }

            // Let's update, calculate or aggregate.
            $result = $grade_items[$gid]->regrade_final_grades($userid, $progress);

            if ($result === true) {

                // We should only update the database if we regraded all users.
                if (empty($userid)) {
                    $grade_items[$gid]->regrading_finished();
                    // Do the locktime item locking.
                    $grade_items[$gid]->check_locktime();
                } else {
                    $grade_items[$gid]->needsupdate = 0;
                }
                $count++;
                $finalids[] = $gid;
                $updatedids[] = $gid;

            } else {
                $grade_items[$gid]->force_regrading();
                $errors[$gid] = $result;
            }
        }

        if ($count == 0) {
            $failed++;
        } else {
            $failed = 0;
        }

        if ($failed > 1) {
            foreach($gids as $gid) {
                if (in_array($gid, $finalids)) {
                    continue; // this one is ok
                }
                $grade_items[$gid]->force_regrading();
                if (!empty($grade_items[$gid]->calculation) && empty($errors[$gid])) {
                    $itemname = $grade_items[$gid]->get_name();
                    $errors[$gid] = get_string('errorcalculationbroken', 'grades', $itemname);
                }
            }
            break; // Found error.
        }
    }
    $progress->end_progress();

    if (count($errors) == 0) {
        if (empty($userid)) {
            // do the locktime locking of grades, but only when doing full regrading
            grade_grade::check_locktime_all($gids);
        }
        return true;
    } else {
        return $errors;
    }
}

/**
 * Refetches grade data from course activities
 *
 * @param int $courseid The course ID
 * @param string $modname Limit the grade fetch to a single module type. For example 'forum'
 * @param int $userid limit the grade fetch to a single user
 */
function grade_grab_course_grades($courseid, $modname=null, $userid=0) {
    global $CFG, $DB;

    if ($modname) {
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, m.name as modname
                  FROM {".$modname."} a, {course_modules} cm, {modules} m
                 WHERE m.name=:modname AND m.visible=1 AND m.id=cm.module AND cm.instance=a.id AND cm.course=:courseid";
        $params = array('modname'=>$modname, 'courseid'=>$courseid);

        if ($modinstances = $DB->get_records_sql($sql, $params)) {
            foreach ($modinstances as $modinstance) {
                grade_update_mod_grades($modinstance, $userid);
            }
        }
        return;
    }

    if (!$mods = core_component::get_plugin_list('mod') ) {
        throw new \moodle_exception('nomodules', 'debug');
    }

    foreach ($mods as $mod => $fullmod) {
        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        // include the module lib once
        if (file_exists($fullmod.'/lib.php')) {
            // get all instance of the activity
            $sql = "SELECT a.*, cm.idnumber as cmidnumber, m.name as modname
                      FROM {".$mod."} a, {course_modules} cm, {modules} m
                     WHERE m.name=:mod AND m.visible=1 AND m.id=cm.module AND cm.instance=a.id AND cm.course=:courseid";
            $params = array('mod'=>$mod, 'courseid'=>$courseid);

            if ($modinstances = $DB->get_records_sql($sql, $params)) {
                foreach ($modinstances as $modinstance) {
                    grade_update_mod_grades($modinstance, $userid);
                }
            }
        }
    }
}

/**
 * Force full update of module grades in central gradebook
 *
 * @param object $modinstance Module object with extra cmidnumber and modname property
 * @param int $userid Optional user ID if limiting the update to a single user
 * @return bool True if success
 */
function grade_update_mod_grades($modinstance, $userid=0) {
    global $CFG, $DB;

    $fullmod = $CFG->dirroot.'/mod/'.$modinstance->modname;
    if (!file_exists($fullmod.'/lib.php')) {
        debugging('missing lib.php file in module ' . $modinstance->modname);
        return false;
    }
    include_once($fullmod.'/lib.php');

    $updateitemfunc   = $modinstance->modname.'_grade_item_update';
    $updategradesfunc = $modinstance->modname.'_update_grades';

    if (function_exists($updategradesfunc) and function_exists($updateitemfunc)) {
        //new grading supported, force updating of grades
        $updateitemfunc($modinstance);
        $updategradesfunc($modinstance, $userid);
    } else if (function_exists($updategradesfunc) xor function_exists($updateitemfunc)) {
        // Module does not support grading?
        debugging("You have declared one of $updateitemfunc and $updategradesfunc but not both. " .
                  "This will cause broken behaviour.", DEBUG_DEVELOPER);
    }

    return true;
}

/**
 * Remove grade letters for given context
 *
 * @param context $context The context
 * @param bool $showfeedback If true a success notification will be displayed
 */
function remove_grade_letters($context, $showfeedback) {
    global $DB, $OUTPUT;

    $strdeleted = get_string('deleted');

    $records = $DB->get_records('grade_letters', array('contextid' => $context->id));
    foreach ($records as $record) {
        $DB->delete_records('grade_letters', array('id' => $record->id));
        // Trigger the letter grade deleted event.
        $event = \core\event\grade_letter_deleted::create(array(
            'objectid' => $record->id,
            'context' => $context,
        ));
        $event->trigger();
    }
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.' - '.get_string('letters', 'grades'), 'notifysuccess');
    }

    $cache = cache::make('core', 'grade_letters');
    $cache->delete($context->id);
}

/**
 * Remove all grade related course data
 * Grade history is kept
 *
 * @param int $courseid The course ID
 * @param bool $showfeedback If true success notifications will be displayed
 */
function remove_course_grades($courseid, $showfeedback) {
    global $DB, $OUTPUT;

    $fs = get_file_storage();
    $strdeleted = get_string('deleted');

    $course_category = grade_category::fetch_course_category($courseid);
    $course_category->delete('coursedelete');
    $fs->delete_area_files(context_course::instance($courseid)->id, 'grade', 'feedback');
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.' - '.get_string('grades', 'grades').', '.get_string('items', 'grades').', '.get_string('categories', 'grades'), 'notifysuccess');
    }

    if ($outcomes = grade_outcome::fetch_all(array('courseid'=>$courseid))) {
        foreach ($outcomes as $outcome) {
            $outcome->delete('coursedelete');
        }
    }
    $DB->delete_records('grade_outcomes_courses', array('courseid'=>$courseid));
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.' - '.get_string('outcomes', 'grades'), 'notifysuccess');
    }

    if ($scales = grade_scale::fetch_all(array('courseid'=>$courseid))) {
        foreach ($scales as $scale) {
            $scale->delete('coursedelete');
        }
    }
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.' - '.get_string('scales'), 'notifysuccess');
    }

    $DB->delete_records('grade_settings', array('courseid'=>$courseid));
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.' - '.get_string('settings', 'grades'), 'notifysuccess');
    }
}

/**
 * Called when course category is deleted
 * Cleans the gradebook of associated data
 *
 * @param int $categoryid The course category id
 * @param int $newparentid If empty everything is deleted. Otherwise the ID of the category where content moved
 * @param bool $showfeedback print feedback
 */
function grade_course_category_delete($categoryid, $newparentid, $showfeedback) {
    global $DB;

    $context = context_coursecat::instance($categoryid);
    $records = $DB->get_records('grade_letters', array('contextid' => $context->id));
    foreach ($records as $record) {
        $DB->delete_records('grade_letters', array('id' => $record->id));
        // Trigger the letter grade deleted event.
        $event = \core\event\grade_letter_deleted::create(array(
            'objectid' => $record->id,
            'context' => $context,
        ));
        $event->trigger();
    }
}

/**
 * Does gradebook cleanup when a module is uninstalled
 * Deletes all associated grade items
 *
 * @param string $modname The grade item module name to remove. For example 'forum'
 */
function grade_uninstalled_module($modname) {
    global $CFG, $DB;

    $sql = "SELECT *
              FROM {grade_items}
             WHERE itemtype='mod' AND itemmodule=?";

    // go all items for this module and delete them including the grades
    $rs = $DB->get_recordset_sql($sql, array($modname));
    foreach ($rs as $item) {
        $grade_item = new grade_item($item, false);
        $grade_item->delete('moduninstall');
    }
    $rs->close();
}

/**
 * Deletes all of a user's grade data from gradebook
 *
 * @param int $userid The user whose grade data should be deleted
 */
function grade_user_delete($userid) {
    if ($grades = grade_grade::fetch_all(array('userid'=>$userid))) {
        foreach ($grades as $grade) {
            $grade->delete('userdelete');
        }
    }
}

/**
 * Purge course data when user unenrolls from a course
 *
 * @param int $courseid The ID of the course the user has unenrolled from
 * @param int $userid The ID of the user unenrolling
 */
function grade_user_unenrol($courseid, $userid) {
    if ($items = grade_item::fetch_all(array('courseid'=>$courseid))) {
        foreach ($items as $item) {
            if ($grades = grade_grade::fetch_all(array('userid'=>$userid, 'itemid'=>$item->id))) {
                foreach ($grades as $grade) {
                    $grade->delete('userdelete');
                }
            }
        }
    }
}

/**
 * Reset all course grades, refetch from the activities and recalculate
 *
 * @param int $courseid The course to reset
 * @return bool success
 */
function grade_course_reset($courseid) {

    // no recalculations
    grade_force_full_regrading($courseid);

    $grade_items = grade_item::fetch_all(array('courseid'=>$courseid));
    foreach ($grade_items as $gid=>$grade_item) {
        $grade_item->delete_all_grades('reset');
    }

    //refetch all grades
    grade_grab_course_grades($courseid);

    // recalculate all grades
    grade_regrade_final_grades($courseid);
    return true;
}

/**
 * Convert a number to 5 decimal point float, null db compatible format
 * (we need this to decide if db value changed)
 *
 * @param float|null $number The number to convert
 * @return float|null float or null
 */
function grade_floatval(?float $number) {
    if (is_null($number)) {
        return null;
    }
    // we must round to 5 digits to get the same precision as in 10,5 db fields
    // note: db rounding for 10,5 is different from php round() function
    return round($number, 5);
}

/**
 * Compare two float numbers safely. Uses 5 decimals php precision using {@link grade_floatval()}. Nulls accepted too.
 * Used for determining if a database update is required
 *
 * @param float|null $f1 Float one to compare
 * @param float|null $f2 Float two to compare
 * @return bool True if the supplied values are different
 */
function grade_floats_different(?float $f1, ?float $f2): bool {
    // note: db rounding for 10,5 is different from php round() function
    return (grade_floatval($f1) !== grade_floatval($f2));
}

/**
 * Compare two float numbers safely. Uses 5 decimals php precision using {@link grade_floatval()}
 *
 * Do not use rounding for 10,5 at the database level as the results may be
 * different from php round() function.
 *
 * @since Moodle 2.0
 * @param float|null $f1 Float one to compare
 * @param float|null $f2 Float two to compare
 * @return bool True if the values should be considered as the same grades
 */
function grade_floats_equal(?float $f1, ?float $f2): bool {
    return (grade_floatval($f1) === grade_floatval($f2));
}

/**
 * Get the most appropriate grade date for a grade item given the user that the grade relates to.
 *
 * @param \stdClass $grade
 * @param \stdClass $user
 * @return int|null
 */
function grade_get_date_for_user_grade(\stdClass $grade, \stdClass $user): ?int {
    // The `datesubmitted` is the time that the grade was created.
    // The `dategraded` is the time that it was modified or overwritten.
    // If the grade was last modified by the user themselves use the date graded.
    // Otherwise use date submitted.
    if ($grade->usermodified == $user->id || empty($grade->datesubmitted)) {
        return $grade->dategraded;
    } else {
        return $grade->datesubmitted;
    }
}

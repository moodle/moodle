<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/report/lib.php');
$gradeserror = array();

/**
* Shortcut function for printing the grader report toggles.
* @param string $type The type of toggle
* @param string $baseurl The base of the URL the toggles will link to
* @param bool $return Whether to return the HTML string rather than printing it
* @return void
*/
function grader_report_print_toggle($type, $baseurl, $return=false) {
    global $CFG;

    $icons = array('eyecons' => 'hide',
                   'calculations' => 'calc',
                   'locks' => 'lock',
                   'grandtotals' => 'sigma');

    $pref_name = 'grade_report_show' . $type;
    $show_pref = get_user_preferences($pref_name, $CFG->$pref_name);

    $strshow = get_string('show' . $type, 'grades');
    $strhide = get_string('hide' . $type, 'grades');

    $show_hide = 'show';
    $toggle_action = 1;

    if ($show_pref) {
        $show_hide = 'hide';
        $toggle_action = 0;
    }

    if (array_key_exists($type, $icons)) {
        $image_name = $icons[$type];
    } else {
        $image_name = $type;
    }

    $string = ${'str' . $show_hide};

    $img = '<img src="'.$CFG->pixpath.'/t/'.$image_name.'.gif" class="iconsmall" alt="'
                  .$string.'" title="'.$string.'" />'. "\n";

    $retval = '<div class="gradertoggle">' . $img . '<a href="' . $baseurl . "&amp;toggle=$toggle_action&amp;toggle_type=$type\">"
         . $string . '</a></div>';

    if ($return) {
        return $retval;
    } else {
        echo $retval;
    }
}


/// processing posted grades here

if ($data = data_submitted() and confirm_sesskey()) {

    // always initialize all arrays
    $queue = array();

    foreach ($data as $varname => $postedgrade) {
        // this is a bit tricky - we have to first load all grades into memory,
        // check if changed and only then start updating the final grades because
        // columns might depend one on another - the result would be overriden calculated and category grades

        // skip, not a grade
        if (!strstr($varname, 'grade')) {
            continue;
        }

        $gradeinfo = explode("_", $varname);

        $userid = clean_param($gradeinfo[1], PARAM_INT);
        $itemid = clean_param($gradeinfo[2], PARAM_INT);

        if (!$grade_item = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$course->id))) { // we must verify course id here!
            error('Incorrect grade item id');
        }

        if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
            if ($postedgrade == -1) { // -1 means no grade
                $finalgrade = null;
            } else {
                $finalgrade = (float)$postedgrade;
            }
        } else {
            if ($postedgrade == '') { // empty string means no grade
                $finalgrade = null;
            } else {
                $finalgrade = format_grade($postedgrade);
            }
        }

        if (!is_null($finalgrade) and ($finalgrade < $grade_item->grademin or $finalgrade > $grade_item->grademax)) {
            $gradeserror[$grade_item->id][$userid] = 'outofrange'; //TODO: localize
            // another possiblity is to use bounded number instead
            continue;
        }

        if ($grade = grade_grades::fetch(array('userid'=>$userid, 'itemid'=>$grade_item->id))) {
            if (!is_null($grade->finalgrade)) {
                $grade->finalgrade = (float)$grade->finalgrade;
            }
            if ($grade->finalgrade === $finalgrade) {
                // we must not update all grades, only changed ones - we do not want to mark everything as overriden
                continue;
            }
        }

        $gradedata = new object();
        $gradedata->grade_item = $grade_item;
        $gradedata->finalgrade = $finalgrade;
        $gradedata->userid     = $userid;

        $queue[] = $gradedata;
    }

    // now we update the new final grade for each changed grade
    foreach ($queue as $gradedata) {
        $gradedata->grade_item->update_final_grade($gradedata->userid, $gradedata->finalgrade, 'gradebook');
    }
}

// get the params
$courseid      = required_param('id', PARAM_INT);
$context       = get_context_instance(CONTEXT_COURSE, $courseid);
$page          = optional_param('page', 0, PARAM_INT);
$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUM); // sort by which grade item
$report        = optional_param('report', 0, PARAM_ALPHANUM);
$action        = optional_param('action', 0, PARAM_ALPHA);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);
$toggle        = optional_param('toggle', NULL, PARAM_INT);
$toggle_type   = optional_param('toggle_type', 0, PARAM_ALPHANUM);

// Handle toggle change request
// TODO print visual feedback
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_show' . $toggle_type => $toggle));
}

// Get the user preferences
$perpage              = get_user_preferences('grade_report_studentsperpage', $CFG->grade_report_studentsperpage); // number of users on a page
$decimals             = get_user_preferences('grade_report_decimalpoints', $CFG->grade_report_decimalpoints); // decimals in grades
$showgrandtotals      = get_user_preferences('grade_report_showgrandtotals', $CFG->grade_report_showgrandtotals);
$showgroups           = get_user_preferences('grade_report_showgroups', $CFG->grade_report_showgroups);
$aggregation_position = get_user_preferences('grade_report_aggregationposition', $CFG->grade_report_aggregationposition);
$showscales           = get_user_preferences('grade_report_showscales', $CFG->grade_report_showscales);
$quickgrading         = get_user_preferences('grade_report_quickgrading', $CFG->grade_report_quickgrading);
$quickfeedback        = get_user_preferences('grade_report_quickfeedback', $CFG->grade_report_quickfeedback);

// Override perpage if set in URL
if ($perpageurl = optional_param('perpage', 0, PARAM_INT)) {
    $perpage = $perpageurl;
}

// Prepare language strings
$strsortasc  = get_string('sortasc', 'grades');
$strsortdesc = get_string('sortdesc', 'grades');
$strfeedback = get_string("feedback");

// base url for sorting by first/last name
$baseurl = 'report.php?id='.$courseid.'&amp;perpage='.$perpage.'&amp;report=grader&amp;page='.$page;
// base url for paging
$pbarurl = 'report.php?id='.$courseid.'&amp;perpage='.$perpage.'&amp;report=grader&amp;';

/// setting up groups
$groupsql = '';
$groupwheresql = '';
$group_selector = null;
$currentgroup = null;

if ($showgroups) {
    /// find out current groups mode
    $course = get_record('course', 'id', $courseid);
    $groupmode = $course->groupmode;
    ob_start();
    $currentgroup = setup_and_print_groups($course, $groupmode, $baseurl);
    $group_selector = ob_get_clean();

    // update paging after group
    $baseurl .= 'group='.$currentgroup.'&amp;';
    $pbarurl .= 'group='.$currentgroup.'&amp;';

    if ($currentgroup) {
        $groupsql = " LEFT JOIN {$CFG->prefix}groups_members gm ON gm.userid = u.id ";
        $groupwheresql = " AND gm.groupid = $currentgroup ";
    }
}

// Grab the grade_tree for this course
$gtree = new grade_tree($courseid, true, false, $aggregation_position);

// setting the sort order, this depends on last state
// all this should be in the new table class that we might need to use
// for displaying grades

// already in not requesting sort, i.e. normal paging

if ($sortitemid) {
    if (!isset($SESSION->gradeuserreport->sort)) {
        $sortorder = $SESSION->gradeuserreport->sort = 'ASC';
    } else {
        // this is the first sort, i.e. by last name
        if (!isset($SESSION->gradeuserreport->sortitemid)) {
            $sortorder = $SESSION->gradeuserreport->sort = 'ASC';
        } else if ($SESSION->gradeuserreport->sortitemid == $sortitemid) {
            // same as last sort
            if ($SESSION->gradeuserreport->sort == 'ASC') {
                $sortorder = $SESSION->gradeuserreport->sort = 'DESC';
            } else {
                $sortorder = $SESSION->gradeuserreport->sort = 'ASC';
            }
        } else {
            $sortorder = $SESSION->gradeuserreport->sort = 'ASC';
        }
    }
    $SESSION->gradeuserreport->sortitemid = $sortitemid;
} else {
    // not requesting sort, use last setting (for paging)

    if (isset($SESSION->gradeuserreport->sortitemid)) {
        $sortitemid = $SESSION->gradeuserreport->sortitemid;
    }
    if (isset($SESSION->gradeuserreport->sort)) {
        $sortorder = $SESSION->gradeuserreport->sort;
    } else {
        $sortorder = 'ASC';
    }
}

/// end of setting sort order code

// Perform actions on categories, items and grades
if (!empty($target) && !empty($action) && confirm_sesskey()) {

    $element = $gtree->locate_element($target);

    switch ($action) {
        case 'edit':
            break;
        case 'delete':
            if ($confirm == 1) { // Perform the deletion
                //TODO: add proper delete support for grade items and categories
                //$element['object']->delete();
                // Print result message

            } else { // Print confirmation dialog
                $eid = $element['eid'];
                $strdeletecheckfull = get_string('deletecheck', '', $element['object']->get_name());
                $linkyes = "category.php?target=$eid&amp;action=delete&amp;confirm=1$gtree->commonvars";
                $linkno = "category.php?$gtree->commonvars";
                notice_yesno($strdeletecheckfull, $linkyes, $linkno);
            }
            break;

        case 'hide':
        // TODO Implement calendar for selection of a date to hide element until
            $element['object']->set_hidden(1);
            $gtree = new grade_tree($courseid);
            break;
        case 'show':
            $element['object']->set_hidden(0);
            $gtree = new grade_tree($courseid);
            break;
        case 'lock':
        // TODO Implement calendar for selection of a date to lock element after
            if (!$element['object']->set_locked(1)) {
                debugging("Could not update the element's locked state!");
            }
            $gtree = new grade_tree($courseid);
            break;
        case 'unlock':
            if (!$element['object']->set_locked(0)) {
                debugging("Could not update the element's locked state!");
            }
            $gtree = new grade_tree($courseid);
            break;
        default:
            break;
    }
}

// first make sure we have all final grades
// TODO: check that no grade_item has needsupdate set
grade_regrade_final_grades($courseid);

// roles to be displaye in the gradebook
$gradebookroles = $CFG->gradebookroles;

/*
* pulls out the userids of the users to be display, and sort them
* the right outer join is needed because potentially, it is possible not
* to have the corresponding entry in grade_grades table for some users
* this is check for user roles because there could be some users with grades
* but not supposed to be displayed
*/
if (is_numeric($sortitemid)) {
    $sql = "SELECT u.id, u.firstname, u.lastname
            FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                 {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $sortitemid)
                 LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                 $groupsql
            WHERE ra.roleid in ($gradebookroles)
                 $groupwheresql
            AND ra.contextid ".get_related_contexts_string($context)."
            ORDER BY g.finalgrade $sortorder";
    $users = get_records_sql($sql, $perpage * $page, $perpage);
} else {
    // default sort
    // get users sorted by lastname
    $users = get_role_users(@implode(',', $CFG->gradebookroles), $context, false, 'u.id, u.firstname, u.lastname', 'u.'.$sortitemid .' '. $sortorder, false, $page * $perpage, $perpage, $currentgroup);
    // need to cut users down by groups

}

/// count total records for paging

$countsql = "SELECT COUNT(DISTINCT u.id)
            FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                 {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $sortitemid)
                 LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                 $groupsql
            WHERE ra.roleid in ($gradebookroles)
                 $groupwheresql
            AND ra.contextid ".get_related_contexts_string($context);
$numusers = count_records_sql($countsql);

// print_object($users); // debug

if (empty($users)) {
    $userselect = '';
    $users = array();
} else {
    $userselect = 'AND g.userid in ('.implode(',', array_keys($users)).')';
}


// phase 2 sql, we supply the userids in this query, and get all the grades
// pulls out all the grades, this does not need to worry about paging
$sql = "SELECT g.id, g.itemid, g.userid, g.finalgrade, g.hidden, g.locked, g.locktime, g.overridden, gt.feedback
        FROM  {$CFG->prefix}grade_items gi,
              {$CFG->prefix}grade_grades g
        LEFT JOIN {$CFG->prefix}grade_grades_text gt ON g.id = gt.gradeid
        WHERE g.itemid = gi.id
        AND gi.courseid = $courseid $userselect";

///print_object($grades); //debug

$finalgrades = array();
// needs to be formatted into an array for easy retrival

if ($grades = get_records_sql($sql)) {
    foreach ($grades as $grade) {
        $finalgrades[$grade->userid][$grade->itemid] = $grade;
    }
}

/// With the users in an sorted array and grades fetched, we can not print the main html table

// 1. Fetch all top-level categories for this course, with all children preloaded, sorted by sortorder

    // Fetch array of students enroled in this course
if (!$context = get_context_instance(CONTEXT_COURSE, $gtree->courseid)) {
    return false;
}
//$users = get_role_users(@implode(',', $CFG->gradebookroles), $context);

if ($sortitemid === 'lastname') {
    if ($sortorder == 'ASC') {
        $lastarrow = print_arrow('up', $strsortasc, true);
    } else {
        $lastarrow = print_arrow('down', $strsortdesc, true);
    }
} else {
    $lastarrow = '';
}

if ($sortitemid === 'firstname') {
    if ($sortorder == 'ASC') {
        $firstarrow = print_arrow('up', $strsortasc, true);
    } else {
        $firstarrow = print_arrow('down', $strsortdesc, true);
    }
} else {
    $firstarrow = '';
}

/********* BEGIN OUTPUT *********/

print_heading('Grader Report');

// Add tabs
$currenttab = 'graderreport';
include('tabs.php');

// Group selection drop-down
echo $group_selector;

// Show/hide toggles
echo '<div id="grade-report-toggles">';
if ($USER->gradeediting) {
    grader_report_print_toggle('eyecons', $baseurl);
    grader_report_print_toggle('locks', $baseurl);
    grader_report_print_toggle('calculations', $baseurl);
}

grader_report_print_toggle('grandtotals', $baseurl);
grader_report_print_toggle('groups', $baseurl);
grader_report_print_toggle('scales', $baseurl);
echo '</div>';

// Paging bar
print_paging_bar($numusers, $page, $perpage, $pbarurl);

$items = array();

// Prepare Table Headers
$headerhtml = '';

$numrows = count($gtree->levels);

foreach ($gtree->levels as $key=>$row) {
    if ($key == 0) {
        // do not diplay course grade category
        // continue;
    }

    $headerhtml .= '<tr class="heading">';

    if ($key == $numrows - 1) {
        $headerhtml .= '<th class="user"><a href="'.$baseurl.'&amp;sortitemid=firstname">Firstname</a> ' //TODO: localize
                    . $firstarrow. '/ <a href="'.$baseurl.'&amp;sortitemid=lastname">Lastname </a>'. $lastarrow .'</th>';
    } else {
        $headerhtml .= '<td class="topleft">&nbsp;</td>';
    }

    foreach ($row as $element) {
        $eid    = $element['eid'];
        $object = $element['object'];
        $type   = $element['type'];

        if (!empty($element['colspan'])) {
            $colspan = 'colspan="'.$element['colspan'].'"';
        } else {
            $colspan = '';
        }

        if (!empty($element['depth'])) {
            $catlevel = ' catlevel'.$element['depth'];
        } else {
            $catlevel = '';
        }


        if ($type == 'filler' or $type == 'fillerfirst' or $type == 'fillerlast') {
            $headerhtml .= '<td class="'.$type.$catlevel.'" '.$colspan.'>&nbsp;</td>';
        } else if ($type == 'category') {
            $headerhtml .= '<td class="category'.$catlevel.'" '.$colspan.'>'.$element['object']->get_name();

            // Print icons
            if ($USER->gradeediting) {
                $headerhtml .= grade_get_icons($element, $gtree);
            }

            $headerhtml .= '</td>';
        } else {
            if ($element['object']->id == $sortitemid) {
                if ($sortorder == 'ASC') {
                    $arrow = print_arrow('up', $strsortasc, true);
                } else {
                    $arrow = print_arrow('down', $strsortdesc, true);
                }
            } else {
                $arrow = '';
            }

            $dimmed = '';
            if ($element['object']->is_hidden()) {
                $dimmed = ' dimmed_text ';
            }

            if ($object->itemtype == 'mod') {
                $icon = '<img src="'.$CFG->modpixpath.'/'.$object->itemmodule.'/icon.gif" class="icon" alt="'
                      .get_string('modulename', $object->itemmodule).'"/>';
            } else if ($object->itemtype == 'manual') {
                //TODO: add manual grading icon
                $icon = '<img src="'.$CFG->pixpath.'/t/edit.gif" class="icon" alt="'.get_string('manualgrade', 'grades')
                      .'"/>';
            }


            $headerhtml .= '<th class="'.$type.$catlevel.$dimmed.'"><a href="'.$baseurl.'&amp;sortitemid='
                      . $element['object']->id .'">'. $element['object']->get_name()
                      . '</a>' . $arrow;

            $headerhtml .= grade_get_icons($element, $gtree) . '</th>';

            $items[$element['object']->sortorder] =& $element['object'];
        }

    }

    $headerhtml .= '</tr>';
}

// Prepare Table Rows
$studentshtml = '';

foreach ($users as $userid => $user) {
    // Student name and link
    $studentshtml .= '<tr><th class="user"><a href="' . $CFG->wwwroot . '/user/view.php?id='
                  . $user->id . '">' . fullname($user) . '</a></th>';
    foreach ($items as $item) {

        if (isset($finalgrades[$userid][$item->id])) {
            $gradeval = $finalgrades[$userid][$item->id]->finalgrade;
            $grade = new grade_grades($finalgrades[$userid][$item->id], false);
            $grade->feedback = $finalgrades[$userid][$item->id]->feedback;

        } else {
            $gradeval = null;
            $grade = new grade_grades(array('userid' => $userid, 'itemid' => $item->id), false);
            $grade->feedback = '';
        }

        if ($grade->is_overridden()) {
            $studentshtml .= '<td class="overridden">';
        } else {
            $studentshtml .= '<td>';
        }

        // Do not show any icons if no grade (no record in DB to match)
        if (!empty($grade->id)) {
            // emulate grade element
            $grade->courseid = $course->id;
            $grade->grade_item = $item; // this may speedup is_hidden() and other grade_grades methods
            $element = array ('eid'=>'g'.$grade->id, 'object'=>$grade, 'type'=>'grade');
            $studentshtml .= grade_get_icons($element, $gtree);
        }


        // if in editting mode, we need to print either a text box
        // or a drop down (for scales)

        // grades in item of type grade category or course are not directly editable
        if ($USER->gradeediting) {
            // We need to retrieve each grade_grade object from DB in order to
            // know if they are hidden/locked

            if ($item->scaleid) {
                if ($scale = get_record('scale', 'id', $item->scaleid)) {
                    $scales = explode(",", $scale->scale);
                    // reindex because scale is off 1
                    $i = 0;
                    foreach ($scales as $scaleoption) {
                        $i++;
                        $scaleopt[$i] = $scaleoption;
                    }

                    if ($quickgrading) {
                        $studentshtml .= choose_from_menu($scaleopt, 'grade_'.$userid.'_'.$item->id,
                                                      $gradeval, get_string('nograde'), '', -1, true);
                    } elseif ($scale = get_record('scale', 'id', $item->scaleid)) {
                        $scales = explode(",", $scale->scale);

                        // invalid grade if gradeval < 1
                        if ((int) $gradeval < 1) {
                            $studentshtml .= '-';
                        } else {
                            $studentshtml .= $scales[$gradeval-1];
                        }
                    } else {
                        // no such scale, throw error?
                    }
                }
            } else {
                if ($quickgrading) {
                    $studentshtml .= '<input size="6" type="text" name="grade_'.$userid.'_'.$item->id.'" value="'.get_grade_clean($gradeval).'"/>';
                } else {
                    $studentshtml .= get_grade_clean($gradeval);
                }
            }


            // If quickfeedback is on, print an input element
            if ($quickfeedback) {
                $studentshtml .= '<input size="6" type="text" name="feedback_'.$userid.'_'.$item->id.'" value="'. s($grade->feedback) . '"/>';
            }

            $studentshtml .= '<div class="grade_icons">' . grade_get_icons($element, $gtree, array('edit')) . '</div>';
        } else {
            // If feedback present, surround grade with feedback tooltip
            if (!empty($grade->feedback)) {
                $studentshtml .= '<span onmouseover="return overlib(\''.$grade->feedback.'\', CAPTION, \''
                        . $strfeedback.'\');" onmouseout="return nd();">';
            }

            // finalgrades[$userid][$itemid] could be null because of the outer join
            // in this case it's different than a 0
            if ($item->scaleid) {
                if ($scale = get_record('scale', 'id', $item->scaleid)) {
                    $scales = explode(",", $scale->scale);

                    // invalid grade if gradeval < 1
                    if ((int) $gradeval < 1) {
                        $studentshtml .= '-';
                    } else {
                        $studentshtml .= $scales[$gradeval-1];
                    }
                } else {
                    // no such scale, throw error?
                }
            } else {
                if (is_null($gradeval)) {
                    $studentshtml .= '-';
                } else {
                    $studentshtml .=  get_grade_clean($gradeval);
                }
            }
            if (!empty($grade->feedback)) {
                $studentshtml .= '</span>';
            }
        }

        if (!empty($gradeserror[$item->id][$userid])) {
            $studentshtml .= $gradeserror[$item->id][$userid];
        }

        $studentshtml .=  '</td>' . "\n";
    }
    $studentshtml .= '</tr>';
}

// if user preference to display group sum
$groupsumhtml = '';

if ($currentgroup && $showgroups) {

/** SQL for finding group sum */
    $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum
        FROM {$CFG->prefix}grade_items gi LEFT JOIN
             {$CFG->prefix}grade_grades g ON gi.id = g.itemid RIGHT OUTER JOIN
             {$CFG->prefix}user u ON u.id = g.userid LEFT JOIN
             {$CFG->prefix}role_assignments ra ON u.id = ra.userid
             $groupsql
        WHERE gi.courseid = $courseid
             $groupwheresql
        AND ra.roleid in ($gradebookroles)
        AND ra.contextid ".get_related_contexts_string($context)."
        GROUP BY g.itemid";

    $groupsum = array();
    $sums = get_records_sql($SQL);
    foreach ($sums as $itemid => $csum) {
        $groupsum[$itemid] = $csum;
    }

    $groupsumhtml = '<tr><th>Group total</th>';
    foreach ($items as $item) {
        if (!isset($groupsum[$item->id])) {
            $groupsumhtml .= '<td>-</td>';
        } else {
            $sum = $groupsum[$item->id];
            $groupsumhtml .= '<td>'.get_grade_clean($sum->sum).'</td>';
        }
    }
    $groupsumhtml .= '</tr>';
}

// Grand totals
$gradesumhtml = '';
if ($showgrandtotals) {

/** SQL for finding the SUM grades of all visible users ($CFG->gradebookroles) */

    $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum
        FROM {$CFG->prefix}grade_items gi LEFT JOIN
             {$CFG->prefix}grade_grades g ON gi.id = g.itemid RIGHT OUTER JOIN
             {$CFG->prefix}user u ON u.id = g.userid LEFT JOIN
             {$CFG->prefix}role_assignments ra ON u.id = ra.userid
        WHERE gi.courseid = $courseid
        AND ra.roleid in ($gradebookroles)
        AND ra.contextid ".get_related_contexts_string($context)."
        GROUP BY g.itemid";

    $classsum = array();
    $sums = get_records_sql($SQL);
    foreach ($sums as $itemid => $csum) {
        $classsum[$itemid] = $csum;
    }

    $gradesumhtml = '<tr><th>Total</th>';
    foreach ($items as $item) {
        if (!isset($classsum[$item->id])) {
            $gradesumhtml .= '<td>-</td>';
        } else {
            $sum = $classsum[$item->id];
            $gradesumhtml .= '<td>'.get_grade_clean($sum->sum).'</td>';
        }
    }
    $gradesumhtml .= '</tr>';
}

// finding the ranges of each gradeitem
$scalehtml = '';
if ($showscales) {
    $scalehtml = '<tr><td>'.get_string('range','grades').'</td>';
    foreach ($items as $item) {
        $scalehtml .= '<td>'. get_grade_clean($item->grademin).'-'. get_grade_clean($item->grademax).'</td>';
    }
    $scalehtml .= '</tr>';
}

$reporthtml = "<table class=\"boxaligncenter\">$headerhtml";
$reporthtml .= $scalehtml;
$reporthtml .= $studentshtml;
$reporthtml .= $groupsumhtml;
$reporthtml .= $gradesumhtml;
$reporthtml .= "</table>";

// print submit button
if ($USER->gradeediting) {
    echo '<form action="report.php" method="post">';
    echo '<div>';
    echo '<input type="hidden" value="'.$courseid.'" name="id" />';
    echo '<input type="hidden" value="'.sesskey().'" name="sesskey" />';
    echo '<input type="hidden" value="grader" name="report"/>';
}

echo $reporthtml;

// print submit button
if ($USER->gradeediting && ($quickfeedback || $quickgrading)) {
    echo '<div class="submit"><input type="submit" value="'.get_string('update').'" /></div>';
    echo '</div></form>';
}
?>

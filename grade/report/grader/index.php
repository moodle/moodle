<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');

/// processing posted grades here

if ($data = data_submitted()) {
    foreach ($data as $varname => $postedgrade) {
        
        // clean posted values
        $postedgrade = clean_param($postedgrade, PARAM_NUMBER);      
        $varname = clean_param($varname, PARAM_RAW);
        
        // skip, not a grade
        if (!strstr($varname, 'grade')) {
            continue;
        }

        $gradeinfo = explode("_", $varname);

        $grade = new object();
        $grade->userid = clean_param($gradeinfo[1], PARAM_INT);
        $gradeitemid = clean_param($gradeinfo[2], PARAM_INT);
        $grade->rawgrade = $postedgrade;

        // put into grades array
        $grades[$gradeitemid][] = $grade;
    }
}

// now we update the raw grade for each posted grades
if (!empty($grades)) {
    foreach ($grades as $gradeitemid => $itemgrades) {
        foreach ($itemgrades as $gradedata) {
            $gradeitem = new grade_item(array('id'=>$gradeitemid), true);
            $gradeitem->update_raw_grade($gradedata->userid, $gradedata->rawgrade);
        }
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

// Get the user preferences
$perpage  = get_user_preferences('grade_report_studentsperpage', $CFG->grade_report_studentsperpage); // number of users on a page
$decimals = get_user_preferences('grade_report_decimalpoints', $CFG->grade_report_decimalpoints); // decimals in grades

// Override perpage if set in URL
if ($perpageurl = optional_param('perpage', 0, PARAM_INT)) {
    $perpage = $perpageurl;
}

// Prepare language strings
$strsortasc  = get_string('sortasc', 'grades');
$strsortdesc = get_string('sortdesc', 'grades');

// base url for sorting by first/last name
$baseurl = 'report.php?id='.$courseid.'&amp;perpage='.$perpage.'&amp;report=grader&amp;page='.$page;
// base url for paging
$pbarurl = 'report.php?id='.$courseid.'&amp;perpage='.$perpage.'&amp;report=grader&amp;';

// Grab the grade_tree for this course
$gtree = new grade_tree($courseid);

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
grade_update_final_grades($courseid);

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
            WHERE ra.roleid in ($gradebookroles)
                AND ra.contextid ".get_related_contexts_string($context)."
            ORDER BY g.finalgrade $sortorder";
    $users = get_records_sql($sql, $perpage * $page, $perpage);
} else {
    // default sort
    // get users sorted by lastname
    $users = get_role_users(@implode(',', $CFG->gradebookroles), $context, false, 'u.id, u.firstname, u.lastname', 'u.'.$sortitemid .' '. $sortorder, false, $page * $perpage, $perpage);
}

/// count total records for paging

$countsql = "SELECT COUNT(DISTINCT u.id)
            FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                 {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $sortitemid)
                 LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
            WHERE ra.roleid in ($gradebookroles)
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
$sql = "SELECT g.id, g.itemid, g.userid, g.finalgrade, g.hidden, g.locked, g.locktime, gt.feedback
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
        $headerhtml .= '<th class="user"><a href="'.$baseurl.'&amp;sortitemid=firstname">Firstname</a> '
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
                      .'"/>'; // TODO: localize
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

        $studentshtml .= '<td>';

        if (isset($finalgrades[$userid][$item->id])) {
                    
            $gradeval = $finalgrades[$userid][$item->id]->finalgrade;
            
            // trim trailing "0"s
            if (isset($gradeval)) {
                if ($gradeval != 0) {
                    $gradeval = trim($gradeval, ".0");  
                } else {
                    $gradeval = 0;
                }
            }
            
            $grade = new grade_grades($finalgrades[$userid][$item->id], false);
            $grade->feedback = $finalgrades[$userid][$item->id]->feedback;
        } else {
            // if itemtype is course or category, the grades in this item is not directly editable  
            if ($USER->gradeediting && $item->itemtype != 'course' && $item->itemtype != 'category') {
                $gradeval ='';
            } else { 
                $gradeval = '-';
            }
            $grade = new grade_grades(array('userid' => $userid, 'itemid' => $item->id), false);
        }

        // if in editting mode, we need to print either a text box
        // or a drop down (for scales)
        
        // grades in item of type grade category or course are not directly editable
        if ($USER->gradeediting && $item->itemtype != 'course' && $item->itemtype != 'category') {
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
                    $studentshtml .= choose_from_menu($scaleopt, 'grade_'.$userid.'_'.$item->id,
                                                      $gradeval, get_string('nograde'), '', -1, true);
                }
            } else {
                $studentshtml .= '<input size="6" type="text" name="grade_'.$userid.'_'.$item->id.'" value="'.$gradeval.'"/>';
            }

        } else {
            // finalgrades[$userid][$itemid] could be null because of the outer join
            // in this case it's different than a 0
            if ($item->scaleid) {
                if ($scale = get_record('scale', 'id', $item->scaleid)) {
                    $scales = explode(",", $scale->scale);

                    // invalid grade if gradeval < 1
                    if ((int) $gradeval < 1) {
                        $studentshtml .= '-';
                    } else {
                        $studentshtml .= round($scales[$gradeval-1], $decimals);
                    }
                } else {
                    // no such scale, throw error?
                }
            } else {
                $studentshtml .=  round($gradeval, $decimals);
            }
        }

        // Do not show any icons if no grade (no record in DB to match)
        if (!empty($grade->id)) {
            // emulate grade element
            $grade->courseid = $course->id;
            $grade->grade_item = $item; // this may speedup is_hidden() and other grade_grades methods
            $element = array ('eid'=>'g'.$grade->id, 'object'=>$grade, 'type'=>'grade');
            $studentshtml .= grade_get_icons($element, $gtree);
        }

        $studentshtml .=  '</td>' . "\n";
    }
    $studentshtml .= '</tr>';
}

$reporthtml = "<table class=\"boxaligncenter\">$headerhtml";
$reporthtml .= $studentshtml;
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
if ($USER->gradeediting) {
    echo '<div style="text-align:center"><input type="submit" value="'.get_string('update').'" /></div>';
    echo '</div></form>';
}
?>

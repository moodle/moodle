<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');

// Prepare language strings
$strsortasc  = get_string('sortasc', 'grades');
$strsortdesc = get_string('sortdesc', 'grades');

/// processing posted grades here

if ($data = data_submitted()) {
    foreach ($data as $varname => $postedgrade) {
        // skip, not a grade
        if (!strstr($varname, 'grade')) {
            continue;
        }
        // clean
        $postedgrade = clean_param($postedgrade, PARAM_NUMBER);

        $gradeinfo = explode("_", $varname);

        $grade = new object();
        $grade->userid = $gradeinfo[1];
        $gradeitemid = $gradeinfo[2];
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
$perpage       = optional_param('perpage', 3, PARAM_INT); // number of users on a page
$action        = optional_param('action', 0, PARAM_ALPHA);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);

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
            if (!$element['object']->set_hidden(1)) {
                debugging("Could not update the element's hidden state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
            break;
        case 'show':
            if (!$element['object']->set_hidden(0)) {
                debugging("Could not update the element's hidden state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
            break;
        case 'lock':
        // TODO Implement calendar for selection of a date to lock element after
            if (!$element['object']->set_locked(1)) {
                debugging("Could not update the element's locked state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
            break;
        case 'unlock':
            if (!$element['object']->set_locked(0)) {
                debugging("Could not update the element's locked state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
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

// pulls out the userids of the users to be display, and sort them
// the right outer join is needed because potentially, it is possible not
// to have the corresponding entry in grade_grades table for some users
// this is check for user roles because there could be some users with grades
// but not supposed to be displayed

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

print_heading('Grader Report');

// Add tabs
$currenttab = 'graderreport';
include('tabs.php');

// base url for sorting by first/last name
$baseurl = 'report.php?id='.$courseid.'&amp;report=grader&amp;page='.$page;
// base url for paging
$pbarurl = 'report.php?id='.$courseid.'&amp;report=grader&amp;';

print_paging_bar($numusers, $page, $perpage, $pbarurl);

/// With the users in an sorted array and grades fetched, we can not print the main html table

// 1. Fetch all top-level categories for this course, with all children preloaded, sorted by sortorder

    // Fetch array of students enroled in this course
if (!$context = get_context_instance(CONTEXT_COURSE, $gtree->courseid)) {
    return false;
}
//$users = get_role_users(@implode(',', $CFG->gradebookroles), $context);

if ($sortitemid === 'lastname') {
    if ($sortorder == 'ASC') {
        $lastarrow = ' <img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$strsortasc.'" /> ';
    } else {
        $lastarrow = ' <img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$strsortdesc.'" /> ';
    }
} else {
    $lastarrow = '';
}

if ($sortitemid === 'firstname') {
    if ($sortorder == 'ASC') {
        $firstarrow = ' <img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$strsortasc.'" /> ';
    } else {
        $firstarrow = ' <img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$strsortdesc.'" /> ';
    }
} else {
    $firstarrow = '';
}

// first name/last name column

$items = array();

$headerhtml = '';

$numrows = count($gtree->levels);
foreach ($gtree->levels as $key=>$row) {
    if ($key == 0) {
        // do not diplay course grade category
        // continue;
    }

    $headerhtml .= '<tr class="heading">';

    if ($key == $numrows - 1) {
        $headerhtml .= '<th class="user"><a href="'.$baseurl.'&amp;sortitemid=firstname">Firstname</a> '. $firstarrow. '/ <a href="'.$baseurl.'&amp;sortitemid=lastname">Lastname </a>'. $lastarrow .'</th>';
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


        if ($element['type'] == 'filler') {
            $headerhtml .= '<td class="filler'.$catlevel.'" '.$colspan.'>&nbsp;</td>';

        } else if ($element['type'] == 'category') {
            $headerhtml .= '<td class="category'.$catlevel.'" '.$colspan.'">'.$element['object']->get_name();

            // Print icons
            if ($USER->gradeediting) {
                $headerhtml .= grade_get_icons($element, $gtree) . '</td>';
            }

        } else {
            if ($element['object']->id == $sortitemid) {
                if ($sortorder == 'ASC') {
                    $arrow = ' <img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$strsortasc.'" /> ';
                } else {
                    $arrow = ' <img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$strsortdesc.'" /> ';
                }
            } else {
                $arrow = '';
            }

            $dimmed = '';
            if ($element['object']->is_hidden()) {
                $dimmed = 'class="dimmed_text"';
            }

            $headerhtml .= '<th '.$dimmed.' class="'.$element['type'].$catlevel.'"><a href="'.$baseurl.'&amp;sortitemid='
                      . $element['object']->id .'">'. $element['object']->get_name()
                      . '</a>' . $arrow;

            $headerhtml .= grade_get_icons($element, $gtree) . '</th>';

            $items[$element['object']->sortorder] =& $element['object'];
        }


    }

    $headerhtml .= '</tr>';
}

$studentshtml = '';

foreach ($users as $userid => $user) {
    $studentshtml .= '<tr><th class="user">' . $user->firstname . ' ' . $user->lastname . '</th>';
    foreach ($items as $item) {


        $studentshtml .= '<td>';

        if (isset($finalgrades[$userid][$item->id])) {
            $gradeval = $finalgrades[$userid][$item->id]->finalgrade;
            $grade_grades = new grade_grades($finalgrades[$userid][$item->id], false);
            $grade_grades->feedback = $finalgrades[$userid][$item->id]->feedback;
        } else {
            $gradeval = '-';
            $grade_grades = new grade_grades(array('userid' => $userid, 'itemid' => $item->id), false);
        }

        // if in editting mode, we need to print either a text box
        // or a drop down (for scales)
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
                    $studentshtml .= choose_from_menu ($scaleopt, 'grade_'.$userid.'_'.$item->id, $gradeval, get_string('nograde'), '', -1, true);
                }
            } else {
                $studentshtml .= '<input type="text" name="grade_'.$userid.'_'.$item->id.'" value="'.$gradeval.'"/>';
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
                        $studentshtml .= $scales[$gradeval-1];
                    }
                } else {
                    // no such scale, throw error?
                }
            } else {
                $studentshtml .=  $gradeval;
            }
        }

        $studentshtml .=  '</td>' . "\n";
    }
    $studentshtml .= '</tr>';
}

$reporthtml = "<table style=\"text-align: center\">$headerhtml";
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
    echo '<input type="submit" value="'.get_string('update').'" />';
    echo '</div></form>';
}
?>

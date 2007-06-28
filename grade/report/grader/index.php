<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');

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
$gtree = new grade_tree($courseid, false);

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

    // If targetting a grade, create a pseudo-element
    if (preg_match('/^grade([0-9]*)/', $target, $matches)) {
        $grade_grades_id = $matches[1];
        $element = new stdClass();
        $grade_grades = new grade_grades(array('id' => $grade_grades_id));
        $element->element = array('object' => $grade_grades);
    } else { 
        $element = $gtree->locate_element($target);
    }

    switch ($action) {
        case 'edit':
            break;
        case 'delete':
            if ($confirm == 1) { // Perform the deletion
                $gtree->remove_element($target);
                $gtree->renumber();
                $gtree->update_db();
                // Print result message
                
            } else { // Print confirmation dialog
                $strdeletecheckfull = get_string('deletecheck', '', $element->element['object']->get_name());
                $linkyes = "category.php?target=$target&amp;action=delete&amp;confirm=1$gtree->commonvars";
                $linkno = "category.php?$gtree->commonvars";
                notice_yesno($strdeletecheckfull, $linkyes, $linkno);
            }
            break;
        
        case 'hide':
        // TODO Implement calendar for selection of a date to hide element until
            if (!$element->element['object']->set_hidden(1)) {
                debugging("Could not update the element's hidden state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
            break;
        case 'show':
            if (!$element->element['object']->set_hidden(0)) {
                debugging("Could not update the element's hidden state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
            break;
        case 'lock':
        // TODO Implement calendar for selection of a date to lock element after
            if (!$element->element['object']->set_locked(1)) {
                debugging("Could not update the element's locked state!");
            } else {
                $gtree = new grade_tree($courseid);
            }
            break;
        case 'unlock':
            if (!$element->element['object']->set_locked(0)) {
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
        LEFT JOIN {$CFG->prefix}grade_grades_text gt ON g.itemid = gt.itemid AND g.userid = gt.userid
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
$tree = $gtree->tree_filled;

if (empty($gtree->tree_filled)) {
    debugging("The tree_filled array wasn't initialised, grade_tree could not display the grades correctly.");
}
    
    // Fetch array of students enroled in this course
if (!$context = get_context_instance(CONTEXT_COURSE, $gtree->courseid)) {
    return false;
}        
//$users = get_role_users(@implode(',', $CFG->gradebookroles), $context);

$topcathtml = '<tr><td class="filler">&nbsp;</td>';
$cathtml    = '<tr><td class="filler">&nbsp;</td>';

if ($sortitemid === 'lastname') {
    if ($sortorder == 'ASC') {
        $lastarrow = ' <img src="'.$CFG->pixpath.'/t/up.gif"/> ';
    } else {
        $lastarrow = ' <img src="'.$CFG->pixpath.'/t/down.gif"/> ';
    }
} else {
    $lastarrow = '';  
}

if ($sortitemid === 'firstname') {
    if ($sortorder == 'ASC') {
        $firstarrow = ' <img src="'.$CFG->pixpath.'/t/up.gif"/> ';
    } else {
        $firstarrow = ' <img src="'.$CFG->pixpath.'/t/down.gif"/> ';
    }
} else {
    $firstarrow = '';  
}

// first name/last name column
$itemhtml   = '<tr><th class="filler"><a href="'.$baseurl.'&amp;sortitemid=firstname">Firstname</a> '. $firstarrow. '/ <a href="'.$baseurl.'&amp;sortitemid=lastname">Lastname </a>'. $lastarrow .'</th>';

$items = array();

foreach ($tree as $topcat) {
    $itemcount = 0;
        
    foreach ($topcat['children'] as $catkey => $cat) {
        $catitemcount = 0;

        foreach ($cat['children'] as $item) {
            $itemcount++;
            $catitemcount++;
            
            if ($item['object']->id == $sortitemid) {
                if ($sortorder == 'ASC') {
                    $arrow = ' <img src="'.$CFG->pixpath.'/t/up.gif"/> ';
                } else {
                    $arrow = ' <img src="'.$CFG->pixpath.'/t/down.gif"/> ';
                }
            } else {
                $arrow = '';
            }  
            
            $dimmed = '';
            if ($item['object']->is_hidden()) {
                $dimmed = 'class="dimmed_text"';
            }

            $itemhtml .= '<th '.$dimmed.'><a href="'.$baseurl.'&amp;sortitemid='
                      . $item['object']->id .'">'. $item['object']->itemname 
                      . '</a>' . $arrow; 
            
            // Print icons if grade editing is on 
            if ($USER->gradeediting) {
                $itemhtml .= grade_get_icons($item['object'], $gtree) . '</th>';
            }

            $items[] = $item;
        }
            
        if ($cat['object'] == 'filler') {
            $cathtml .= '<td class="subfiller">&nbsp;</td>';
        } else {
            $dimmed = '';
            if ($cat['object']->is_hidden()) {
                $dimmed = 'class="dimmed_text"';
            }
            
            $cat['object']->load_grade_item();
            $cathtml .= '<td '.$dimmed.' colspan="' . $catitemcount . '">' . $cat['object']->fullname;

            // Print icons if grade editing is on 
            if ($USER->gradeediting) {
                $cathtml .= grade_get_icons($cat['object'], $gtree) . '</td>';
            }
        }
    }

    if ($topcat['object'] == 'filler') {
        $colspan = null;
        if (!empty($topcat['colspan'])) {
            $colspan = 'colspan="' . $topcat['colspan'] . '" ';
        }
        $topcathtml .= '<td ' . $colspan . 'class="topfiller">&nbsp;</td>';
    } else {
        $dimmed = '';
        if ($topcat['object']->is_hidden()) {
            $dimmed = 'class="dimmed_text"';
        }
        
        $topcathtml .= '<th '.$dimmed.' colspan="' . $itemcount . '">' . $topcat['object']->fullname;
        
        // Print icons if grade editing is on 
        if ($USER->gradeediting) {
            $topcathtml .= grade_get_icons($topcat['object'], $gtree) . '</th>';
        }
    }
}
    
$studentshtml = '';

foreach ($users as $userid => $user) {
    $studentshtml .= '<tr><th>' . $user->firstname . ' ' . $user->lastname . '</th>';
    foreach ($items as $item) {
        
        
        $studentshtml .= '<td>';
        
        if (isset($finalgrades[$userid][$item['object']->id])) {
            $gradeval = $finalgrades[$userid][$item['object']->id]->finalgrade;
            $grade_grades = new grade_grades($finalgrades[$userid][$item['object']->id], false);
        } else {
            $gradeval = '-';  
            $grade_grades = new grade_grades(array('userid' => $userid, 'itemid' => $item['object']->id), false);
        }
          
        // if in editting mode, we need to print either a text box
        // or a drop down (for scales)
        if ($USER->gradeediting) {
            // We need to retrieve each grade_grade object from DB in order to 
            // know if they are hidden/locked

            if ($item['object']->scaleid) {
                if ($scale = get_record('scale', 'id', $item['object']->scaleid)) {
                    $scales = explode(",", $scale->scale);
                    // reindex because scale is off 1
                    $i = 0;
                    foreach ($scales as $scaleoption) {
                        $i++;
                        $scaleopt[$i] = $scaleoption;
                    }
                    $studentshtml .= choose_from_menu ($scaleopt, 'grade_'.$userid.'_'.$item['object']->id, $gradeval, get_string('nograde'), '', -1, true);
                }
            } else {
                $studentshtml .= '<input type="text" name="grade_'.$userid.'_'.$item['object']->id.'" value="'.$gradeval.'"/>';
            }
            
            // Do not show any icons if no grade (no record in DB to match)
            if (!empty($grade_grades->id)) {
                $studentshtml .= grade_get_icons($grade_grades, $gtree);
            }
        } else {
            // finalgrades[$userid][$itemid] could be null because of the outer join
            // in this case it's different than a 0  
            if ($item['object']->scaleid) {
                if ($scale = get_record('scale', 'id', $item['object']->scaleid)) {
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
    
$itemhtml   .= '</tr>';
$cathtml    .= '</tr>';
$topcathtml .= '</tr>';
    
$reporthtml = "<table style=\"text-align: center\" border=\"1\">$topcathtml$cathtml$itemhtml";
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

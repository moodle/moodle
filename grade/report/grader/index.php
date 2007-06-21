<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');


// get the params
$courseid = required_param('id', PARAM_INT);
$context = get_context_instance(CONTEXT_COURSE, $courseid);
$page = optional_param('page', 0, PARAM_INT);
$sortitemid = optional_param('sortitemid', 0, PARAM_INT); // sort by which grade item

$perpage = 10;

// roles to be displaye in the gradebook
$gradebookroles = $CFG->gradebookroles;

// pulls out the userids of the users to be display, and sort them
// the right outer join is needed because potentially, it is possible not
// to have the corresponding entry in grade_grades table for some users
// this is check for user roles because there could be some users with grades
// but not supposed to be displayed

if ($sortitemid) {

    $sql = "SELECT u.id, u.firstname, u.lastname, g.itemid, g.finalgrade
            FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                 {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $sortitemid)
                 LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid             
            WHERE ra.roleid in ($gradebookroles)
                AND ra.contextid ".get_related_contexts_string($context)."
            ORDER BY g.finalgrade ASC";
    $users = get_records_sql($sql, $perpage * $page, $perpage);
} else {
    
    // get users sorted by lastname
    $users = get_role_users(@implode(',', $CFG->gradebookroles), $context, false, '', 'u.lastname ASC', false, 0, $perpage); 
}
print_object($users);

// phase 2 sql, we supply the userids in this query, and get all the grades
// pulls out all the grades, this does not need to worry about paging
$sql = "SELECT g.id, g.itemid, g.userid, g.finalgrade 
        FROM  {$CFG->prefix}grade_grades g, 
              {$CFG->prefix}grade_items gi                 
        WHERE g.itemid = gi.id
              AND gi.courseid = $courseid
              AND g.userid in (".implode(",", array_keys($users)).")";

$grades = get_records_sql($sql);

print_object($grades);

$finalgrades = array();
// needs to be formatted into an array for easy retrival
foreach ($grades as $grade) {
    $finalgrades[$grade->userid][$grade->itemid] = $grade->finalgrade;
  
}

print_heading('Grader Report');

if ($gtree = new grade_tree($courseid, false)) {

    // 1. Fetch all top-level categories for this course, with all children preloaded, sorted by sortorder
     $tree = $gtree->tree_filled;

    if (empty($gtree->tree_filled)) {
        debugging("The tree_filled array wasn't initialised, grade_tree could not display the grades correctly.");
        return false;
    }
        
        // Fetch array of students enroled in this course
    if (!$context = get_context_instance(CONTEXT_COURSE, $gtree->courseid)) {
        return false;  
    }        
    //$users = get_role_users(@implode(',', $CFG->gradebookroles), $context);

    $topcathtml = '<tr><td class="filler">&nbsp;</td>';
    $cathtml    = '<tr><td class="filler">&nbsp;</td>';
    $itemhtml   = '<tr><td class="filler">&nbsp;</td>';
    $items = array();

    foreach ($tree as $topcat) {
        $itemcount = 0;
            
        foreach ($topcat['children'] as $catkey => $cat) {
            $catitemcount = 0;

            foreach ($cat['children'] as $item) {
                $itemcount++;
                $catitemcount++;
                $itemhtml .= '<td>' . $item['object']->itemname . '</td>'; 
                $items[] = $item;
            }
                
            if ($cat['object'] == 'filler') {
                $cathtml .= '<td class="subfiller">&nbsp;</td>';
            } else {
                $cat['object']->load_grade_item();
                $cathtml .= '<td colspan="' . $catitemcount . '">' . $cat['object']->fullname . '</td>';
            }
        }

        if ($topcat['object'] == 'filler') {
            $colspan = null;
            if (!empty($topcat['colspan'])) {
                $colspan = 'colspan="' . $topcat['colspan'] . '" ';
            }
            $topcathtml .= '<td ' . $colspan . 'class="topfiller">&nbsp;</td>';
        } else {
            $topcathtml .= '<th colspan="' . $itemcount . '">' . $topcat['object']->fullname . '</th>';
        }
    }
        
    $studentshtml = '';

    foreach ($users as $userid => $user) {
        $studentshtml .= '<tr><th>' . $user->firstname . ' ' . $user->lastname . '</th>';
        foreach ($items as $item) {
            // finalgrades[$userid][$itemid] could be null because of the outer join
            // in this case it's different than a 0
            if (isset($finalgrades[$userid][$item['object']->id])) {
                $studentshtml .= '<td>' . $finalgrades[$userid][$item['object']->id] . '</td>' . "\n"; 
            } else {
                $studentshtml .= '<td>-</td>' . "\n";
            }
        } 
        $studentshtml .= '</tr>';
    }
        
    $itemhtml   .= '</tr>';
    $cathtml    .= '</tr>';
    $topcathtml .= '</tr>';
        
    $reporthtml = "<table style=\"text-align: center\" border=\"1\">$topcathtml$cathtml$itemhtml";
    $reporthtml .= $studentshtml; 
    $reporthtml .= "</table>";

    echo $reporthtml;
}

?>
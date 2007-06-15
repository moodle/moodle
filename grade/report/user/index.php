<?php // $Id$

/// This creates and handles the whole user report interface, sans header and footer

print_heading('User Grades Report');

include_once($CFG->libdir.'/gradelib.php');
$courseid = required_param('id', PARAM_INT);

if (!$userid = optional_param('user', 9, PARAM_INT)) {
    // current user
    $userid = $USER->id;  
}

// get all the grade_items
$gradeitems = grade_get_items($courseid);
$gradetotal = 0;
$gradesum = 0;
/*
 *
 * Table has 6 columns 
 *| pic  | itemname/description | grade (grade_final) | percentage | rank | feedback |
 *
 */
echo '<table><tr>
      <th></th>
      <th>'.get_string('gradeitem', 'grades').'</th>
      <th>'.get_string('grade','grades').'</th>
      <th>'.get_string('percentage', 'grades').'</th>
      <th>'.get_string('rank', 'grades').'</th>
      <th>'.get_string('feedback').'</th>
      </tr>';
    
foreach ($gradeitems as $gradeitem) {
    
    echo '<tr>';

    $params->itemid = $gradeitem->id;
    $params->userid = $userid;
    $grade_grades_final = new grade_grades_final($params);
    $grade_text = $grade_grades_final->load_text();
    
    /// prints mod icon if available
    echo '<td>';
    if ($gradeitem->itemtype == 'mod') {
        $iconpath = $CFG->dirroot.'/mod/'.$gradeitem->itemmodule.'/icon.gif';
        $icon = $CFG->wwwroot.'/mod/'.$gradeitem->itemmodule.'/icon.gif';
        if (file_exists($iconpath)) {
            echo '<img src = "'.$icon.'" alt="'.$gradeitem->itemname.'" class="activityicon"/>';  
        }  
    }
    echo '</td>';
  
    /// prints grade item name
    if ($gradeitem->itemtype == 'category') {
        echo '<td><b>'.$gradeitem->itemname.'</b></td>';
    } else {
        echo '<td>'.$gradeitem->itemname.'</td>';
    }
    
    /// prints the grade 
    echo '<td>'.$grade_grades_final->gradevalue.'</td>';
    
    /// prints percentage
   
    if ($gradeitem->gradetype == 1) {
        // processing numeric grade
        if ($grade_grades_final->gradevalue) {
            $percentage = $grade_grades_final->gradevalue / $gradeitem->grademax * 100 .'%';
        } else {
            $percentage = '-';
        }
        $gradetotal += $gradeitem->grademax;
        $gradesum += $grade_grades_final->gradevalue;
    } else if ($gradeitem->gradetype == 2) {
        // processing scale grade
        $scale = get_record('scale', 'id', $gradeitem->scaleid);
        $scalevals = explode(",", $scale->scale);
        $percentage = ($grade_grades_final->gradevalue -1) / count($scalevals);        
        $gradesum += count($scalevals);
        $gradetotal += $grade_grades_final->gradevalue;
    } else {
        // text grade
        $percentage = '-';  
    }
   
    echo '<td>'.$percentage.'</td>';
    
    /// prints rank
    echo '<td></td>';
    
    /// prints notes
    echo '<td>'.$grade_text->feedback.'</td>';
    
    /// close row <tr> tag   
    echo '</tr>';  
}      
      
/// prints the total
echo '<tr><td colspan="2">'.get_string('total').'</td>';
echo '<td>'.$gradesum.'/'.$gradetotal.'</td>';
echo '<td colspan="3">&nbsp;</td>';
echo '</tr>';
echo '</table>';

?>

<?php // $Id$

/// This creates and handles the whole user report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');

// get the params
$courseid = required_param('id', PARAM_INT);
if (!$userid = optional_param('user', 0, PARAM_INT)) {
    // current user
    $userid = $USER->id;  
}


// construct the tree, this should handle sort order
if ($gradetree = new grade_tree($courseid)) {
    $gradetotal = 0;
    $gradesum = 0;
    
    /*
    * Table has 6 columns 
    *| pic  | itemname/description | grade (grade_final) | percentage | rank | feedback |
    */
    $baseurl = $CFG->wwwroot.'/grade/report?id='.$id.'&amp;userid='.$userid;
 
    // setting up table headers
    $tablecolumns = array('itempic', 'itemname', 'grade', 'percentage', 'rank', 'feedback');
    $tableheaders = array('', get_string('gradeitem', 'grades'), get_string('grade'), get_string('percent', 'grades'), get_string('rank', 'grades'), get_string('feedback'));

    $table = new flexible_table('grade-report-user-'.$course->id);

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl);
 
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'user-grade');
    $table->set_attribute('class', 'generaltable generalbox');
    
    // not sure tables should be sortable or not, because if we allow it then sorted resutls distort grade category structure and sortorder
    $table->set_control_variables(array(
            TABLE_VAR_SORT    => 'ssort',
            TABLE_VAR_HIDE    => 'shide',
            TABLE_VAR_SHOW    => 'sshow',
            TABLE_VAR_IFIRST  => 'sifirst',
            TABLE_VAR_ILAST   => 'silast',
            TABLE_VAR_PAGE    => 'spage'
            ));

    $table->setup();

    // loop through grade items to extra data
    foreach ($gradetree->tree_array as $gradeitemobj) {
        
        // grade item is the 'object' of the grade tree
        $gradeitem = $gradeitemobj['object'];        
        $data = array();

        $params->itemid = $gradeitem->id;
        $params->userid = $userid;
        $grade_grades = new grade_grades($params);
        $grade_text = $grade_grades->load_text();

        /// prints mod icon if available
        if ($gradeitem->itemtype == 'mod') {
            $iconpath = $CFG->dirroot.'/mod/'.$gradeitem->itemmodule.'/icon.gif';
            $icon = $CFG->wwwroot.'/mod/'.$gradeitem->itemmodule.'/icon.gif';
            if (file_exists($iconpath)) {
                $data[] = '<img src = "'.$icon.'" alt="'.$gradeitem->itemname.'" class="activityicon"/>';  
            }  
        } else {
            $data[] = '';
        }
  
        /// prints grade item name
        if ($gradeitem->itemtype == 'category') {
            $data[] = '<b>'.$gradeitem->itemname.'</b>';
        } else {
            $data[] = $gradeitem->itemname;
        }
    
        /// prints the grade 
        $data[] = $grade_grades->finalgrade;
    
        /// prints percentage
   
        if ($gradeitem->gradetype == 1) {
            // processing numeric grade
            if ($grade_grades->finalgrade) {
            } else {
                $percentage = '-';
            }
            $gradetotal += $gradeitem->grademax;
            $gradesum += $grade_grades->finalgrade;
        } else if ($gradeitem->gradetype == 2) {
            // processing scale grade
            $scale = get_record('scale', 'id', $gradeitem->scaleid);
            $scalevals = explode(",", $scale->scale);
            $percentage = ($grade_grades->finalgrade -1) / count($scalevals);        
            $gradesum += count($scalevals);
            $gradetotal += $grade_grades->finalgrade;
        } else {
            // text grade
            $percentage = '-';  
        }
   
        $data[] = $percentage;
    
        /// prints rank
        $data[] = '';
    
        /// prints notes
        if (!empty($grade_text->feedback)) {
            $data[] = $grade_text->feedback;
        } else {
            $data[] = '&nbsp;';  
        }
        $table->add_data($data);  
    }
    
    $table->add_data(array('', get_string('total'), $gradesum.'/'.$gradetotal));

    // print the page
    print_heading(get_string('userreport', 'grades'));
    $table->print_html();
}
?>

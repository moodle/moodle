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

$context = get_context_instance(CONTEXT_COURSE, $courseid);
// find total number of participants
$numusers = count(get_role_users(@implode(',', $CFG->gradebookroles), $context));

// construct the tree, this should handle sort order
if ($gradetree = new grade_tree($courseid)) {
    $gradetotal = 0;
    $gradesum = 0;

    /*
    * Table has 6 columns 
    *| pic  | itemname/description | grade (grade_final) | percentage | rank | feedback |
    */
    $baseurl = $CFG->wwwroot.'/grade/report?id='.$courseid.'&amp;userid='.$userid;
 
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

    if ($gradetree->tree_array) {
        // loop through grade items to extra data
        foreach ($gradetree->tree_array as $gradeitemobj) {
            
            // grade item is the 'object' of the grade tree
            $gradeitem = $gradeitemobj['object'];        
            
            // grade categories are returned as part of the tree
            // skip them
            if (get_class($gradeitem) == 'grade_category') {
                continue;  
            }
            
            // row data to be inserted into table
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

            if ($gradeitem->scaleid) {
                // using scales
                if ($scale = get_record('scale', 'id', $gradeitem->scaleid)) {
                    $scales = explode(",", $scale->scale);
                    // reindex because scale is off 1
                    // invalid grade if gradeval < 1
                    if ((int) $grade_grades->finalgrade < 1) {
                        $data[] = '-';  
                    } else {
                        $data[] = $scales[$grade_grades->finalgrade-1];
                    }
                }
            } else {
                // normal grade, or text, just display
                $data[] = $grade_grades->finalgrade;
            }

            /// prints percentage

            if ($gradeitem->gradetype == 1) {
                // processing numeric grade
                if ($grade_grades->finalgrade) {
                    $percentage = (($grade_grades->finalgrade / $gradeitem->grademax) * 100).'%';
                } else {
                    $percentage = '-';
                }
                $gradetotal += $gradeitem->grademax;
                $gradesum += $grade_grades->finalgrade;
            } else if ($gradeitem->gradetype == 2) {
                // processing scale grade
                $scale = get_record('scale', 'id', $gradeitem->scaleid);
                $scalevals = explode(",", $scale->scale);
                $percentage = (($grade_grades->finalgrade) / count($scalevals) * 100).'%';      
                $gradesum += $grade_grades->finalgrade;
                $gradetotal += count($scalevals);
            } else {
                // text grade
                $percentage = '-';  
            }

            $data[] = $percentage;

            /// prints rank
            if ($grade_grades->finalgrade) {
                /// find the number of users with a higher grade
                $sql = "SELECT COUNT(DISTINCT(userid))
                        FROM {$CFG->prefix}grade_grades
                        WHERE finalgrade > $grade_grades->finalgrade
                        AND itemid = $gradeitem->id";
                $rank = count_records_sql($sql) + 1;
            
                $data[] = "$rank/$numusers";
            } else {
                // no grade, no rank
                $data[] = "-";
            }

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
}
?>

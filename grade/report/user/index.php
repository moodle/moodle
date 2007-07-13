<?php // $Id$

/// This creates and handles the whole user report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/report/lib.php');
// get the params
$courseid = required_param('id', PARAM_INT);
if (!$userid = optional_param('user', 0, PARAM_INT)) {
    // current user
    $userid = $USER->id;
}

// get the user (for full name)
$user = get_record('user', 'id', $userid);

$context = get_context_instance(CONTEXT_COURSE, $courseid);
// find total number of participants
$numusers = count(get_role_users(@implode(',', $CFG->gradebookroles), $context));

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
    $table->set_attribute('class', 'boxaligncenter generaltable');

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

    // print the page
    print_heading(get_string('userreport', 'grades'). " - ".fullname($user));

    if ($all_grade_items = grade_item::fetch_all(array('courseid'=>$courseid))) {
        $grade_items = array();
        foreach ($all_grade_items as $item) {
            $grade_items[$item->sortorder] = $item;
        }
        unset($all_grade_items);
        ksort($grade_items);

        $total = $grade_items[1];
        unset($grade_items[1]);
        $grade_items[] = $total;

        foreach ($grade_items as $grade_item) {

            $data = array();

            $params->itemid = $grade_item->id;
            $params->userid = $userid;
            $grade_grades = new grade_grades($params);
            $grade_text = $grade_grades->load_text();

            /// prints mod icon if available
            if ($grade_item->itemtype == 'mod') {
                $iconpath = $CFG->dirroot.'/mod/'.$grade_item->itemmodule.'/icon.gif';
                $icon = $CFG->wwwroot.'/mod/'.$grade_item->itemmodule.'/icon.gif';
                if (file_exists($iconpath)) {
                    $data[] = '<img src = "'.$icon.'" alt="'.$grade_item->itemname.'" class="activityicon"/>';
                }
            } else {
                $data[] = '';
            }

            // TODO: indicate items that "needsupdate" - missing final calculation

            /// prints grade item name
            if ($grade_item->is_course_item() or $grade_item->is_category_item()) {
                $data[] = '<b>'.$grade_item->get_name().'</b>';
            } else {
                $data[] = $grade_item->get_name();
            }

            /// prints the grade

            if ($grade_item->scaleid) {
                // using scales
                if ($scale = get_record('scale', 'id', $grade_item->scaleid)) {
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
                $data[] = grade_report::get_grade_clean($grade_grades->finalgrade);
            }

            /// prints percentage

            if ($grade_item->gradetype == GRADE_TYPE_VALUE) {
                // processing numeric grade
                if ($grade_grades->finalgrade) {
                    $percentage = grade_report::get_grade_clean(($grade_grades->finalgrade / $grade_item->grademax) * 100).'%';
                } else {
                    $percentage = '-';
                }

            } else if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
                // processing scale grade
                $scale = get_record('scale', 'id', $grade_item->scaleid);
                $scalevals = explode(",", $scale->scale);
                $percentage = grade_report::get_grade_clean(($grade_grades->finalgrade) / count($scalevals) * 100).'%';

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
                        AND itemid = $grade_item->id";
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

        //echo "<div><table class='boxaligncenter'><tr><td>asdfas</td></tr></table></div>";
        $table->print_html();
    } else {
        notify(get_string('nogradeitem', 'grades'));
    }

?>

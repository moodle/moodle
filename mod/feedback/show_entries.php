<?php

/**
 * print the single entries
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/tablelib.php');

////////////////////////////////////////////////////////
//get the params
////////////////////////////////////////////////////////
$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', false, PARAM_INT);
$do_show = required_param('do_show', PARAM_ALPHA);
$perpage = optional_param('perpage', FEEDBACK_DEFAULT_PAGE_COUNT, PARAM_INT);  // how many per page
$showall = optional_param('showall', false, PARAM_INT);  // should we show all users
// $SESSION->feedback->current_tab = $do_show;
$current_tab = $do_show;

////////////////////////////////////////////////////////
//get the objects
////////////////////////////////////////////////////////

if($userid) {
    $formdata->userid = intval($userid);
}

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$url = new moodle_url('/mod/feedback/show_entries.php', array('id'=>$cm->id, 'do_show'=>$do_show));

$PAGE->set_url($url);

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
}

require_login($course->id, true, $cm);

if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

require_capability('mod/feedback:viewreports', $context);

////////////////////////////////////////////////////////
//get the responses of given user
////////////////////////////////////////////////////////
if($do_show == 'showoneentry') {
    //get the feedbackitems
    $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
    $feedbackcompleted = $DB->get_record('feedback_completed', array('feedback'=>$feedback->id, 'userid'=>$userid, 'anonymous_response'=>FEEDBACK_ANONYMOUS_NO)); //arb
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

include('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////
/// Print the links to get responses and analysis
////////////////////////////////////////////////////////
if($do_show == 'showentries'){
    //print the link to analysis
    if(has_capability('mod/feedback:viewreports', $context)) {
        //get the effective groupmode of this course and module
        if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
            $groupmode =  $cm->groupmode;
        } else {
            $groupmode = $course->groupmode;
        }

        // $groupselect = groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/feedback/show_entries.php?id=' . $cm->id.'&do_show=showentries', true);
        $groupselect = groups_print_activity_menu($cm, $url->out(), true);
        $mygroupid = groups_get_activity_group($cm);

        // preparing the table for output
        $baseurl = new moodle_url('/mod/feedback/show_entries.php');
        $baseurl->params(array('id'=>$id, 'do_show'=>$do_show, 'showall'=>$showall));

        $tablecolumns = array('userpic', 'fullname', 'completed_timemodified');
        $tableheaders = array(get_string('userpic'), get_string('fullnameuser'), get_string('date'));

        if(has_capability('mod/feedback:deletesubmissions', $context)) {
            $tablecolumns[] = 'deleteentry';
            $tableheaders[] = '';
        }

        $table = new flexible_table('feedback-showentry-list-'.$course->id);

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($baseurl);

        $table->sortable(true, 'lastname', SORT_DESC);
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'showentrytable');
        $table->set_attribute('class', 'generaltable generalbox');
        $table->set_control_variables(array(
                    TABLE_VAR_SORT    => 'ssort',
                    TABLE_VAR_IFIRST  => 'sifirst',
                    TABLE_VAR_ILAST   => 'silast',
                    TABLE_VAR_PAGE    => 'spage'
                    ));
        $table->setup();

        if($table->get_sql_sort()) {
            $sort = $table->get_sql_sort();
        }else {
            $sort = '';
        }

        list($where, $params) = $table->get_sql_where();
        if ($where) {
            $where .= ' AND';
        }

        //get students in conjunction with groupmode
        if($groupmode > 0) {
            if($mygroupid > 0) {
                $usedgroupid = $mygroupid;
            } else {
                $usedgroupid = false;
            }
        }else {
            $usedgroupid = false;
        }

        $matchcount = feedback_count_complete_users($cm, $usedgroupid);
        $table->initialbars(true);

        if($showall) {
            $startpage = false;
            $pagecount = false;
        }else {
            $table->pagesize($perpage, $matchcount);
            $startpage = $table->get_page_start();
            $pagecount = $table->get_page_size();
        }

        $students = feedback_get_complete_users($cm, $usedgroupid, $where, $params, $sort, $startpage, $pagecount);

        $completedFeedbackCount = feedback_get_completeds_group_count($feedback, $mygroupid);
        if($feedback->course == SITEID){
            $analysisurl = new moodle_url('/mod/feedback/analysis_course.php', array('id'=>$id, 'courseid'=>$courseid));
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">'.get_string('course').' '.get_string('analysis', 'feedback').' ('.get_string('completed_feedbacks', 'feedback').': '.intval($completedFeedbackCount).')</a>';
            echo $OUTPUT->help_icon('viewcompleted', 'feedback');
            echo $OUTPUT->box_end();
        }else {
            $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$id, 'courseid'=>$courseid));
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">'.get_string('analysis', 'feedback').' ('.get_string('completed_feedbacks', 'feedback').': '.intval($completedFeedbackCount).')</a>';
            echo $OUTPUT->box_end();
        }
    }

    //####### viewreports-start
    if(has_capability('mod/feedback:viewreports', $context)) {
        //print the list of students
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo isset($groupselect) ? $groupselect : '';
        echo '<div class="clearer"></div>';
        echo $OUTPUT->box_start('mdl-align');
        // echo '<table><tr><td width="400">';
        if (!$students) {
            $table->print_html();
        } else{
            echo print_string('non_anonymous_entries', 'feedback');
            echo ' ('.count($students).')<hr />';

            foreach ($students as $student){
                $completedCount = $DB->count_records('feedback_completed', array('userid'=>$student->id, 'feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_NO));
                if($completedCount > 0) {

                    //userpicture and link to the profilepage
                    $profilelink = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$student->id.'&amp;course='.$course->id.'">'.fullname($student).'</a></strong>';
                    $data = array ($OUTPUT->user_picture($student, array('courseid'=>$course->id)), $profilelink);

                    //link to the entry of the user
                    $feedbackcompleted = $DB->get_record('feedback_completed', array('feedback'=>$feedback->id, 'userid'=>$student->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_NO));
                    $showentryurl = new moodle_url($url, array('userid'=>$student->id, 'do_show'=>'showoneentry'));
                    $showentrylink = '<a href="'.$showentryurl->out().'">'.UserDate($feedbackcompleted->timemodified).'</a>';
                    $data[] = $showentrylink;

                    //link to delete the entry
                    if(has_capability('mod/feedback:deletesubmissions', $context)) {
                        $deleteentryurl = new moodle_url($CFG->wwwroot.'/mod/feedback/delete_completed.php', array('id'=>$cm->id, 'completedid'=>$feedbackcompleted->id, 'do_show'=>'showoneentry'));
                        $deleteentrylink = '<a href="'.$deleteentryurl->out().'">'.get_string('delete_entry', 'feedback').'</a>';
                        $data[] = $deleteentrylink;
                    }
                    $table->add_data($data);
                }
            }
            $table->print_html();

            $allurl = new moodle_url($baseurl);

            if ($showall) {
                $allurl->param('showall', 0);
                echo $OUTPUT->container(html_writer::link($allurl, get_string('showperpage', '', FEEDBACK_DEFAULT_PAGE_COUNT)), array(), 'showall');
            } else if ($matchcount > 0 && $perpage < $matchcount) {
                $allurl->param('showall', 1);
                echo $OUTPUT->container(html_writer::link($allurl, get_string('showall', '', $matchcount)), array(), 'showall');
            }
        }
?>
        <hr />
        <table width="100%">
            <tr>
                <td align="left" colspan="2">
                    <?php print_string('anonymous_entries', 'feedback');?>&nbsp;(<?php echo $DB->count_records('feedback_completed', array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES));?>)
                </td>
                <td align="right">
                    <?php
                        $aurl = new moodle_url('show_entries_anonym.php', array('sesskey'=>sesskey(), 'userid'=>0, 'do_show'=>'showoneentry', 'id'=>$id));
                        echo $OUTPUT->single_button($aurl, get_string('show_entries', 'feedback'));
                    ?>
                </td>
            </tr>
        </table>
<?php
        echo $OUTPUT->box_end();
        echo $OUTPUT->box_end();
    }

}
////////////////////////////////////////////////////////
/// Print the responses of the given user
////////////////////////////////////////////////////////
if($do_show == 'showoneentry') {
    echo $OUTPUT->heading(format_text($feedback->name));

    //print the items
    if(is_array($feedbackitems)){
        $align = right_to_left() ? 'right' : 'left';
        $usr = $DB->get_record('user', array('id'=>$userid));

        if($feedbackcompleted) {
            echo $OUTPUT->heading(UserDate($feedbackcompleted->timemodified).' ('.fullname($usr).')', 3);
        } else {
            echo $OUTPUT->heading(get_string('not_completed_yet','feedback'), 3);
        }

        echo $OUTPUT->box_start('feedback_items');
        $itemnr = 0;
        foreach($feedbackitems as $feedbackitem){
            //get the values
            $value = $DB->get_record('feedback_value', array('completed'=>$feedbackcompleted->id, 'item'=>$feedbackitem->id));
            echo $OUTPUT->box_start('feedback_item_box_'.$align);
            if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
            }

            if($feedbackitem->typ != 'pagebreak') {
                echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                if(isset($value->value)) {
                    feedback_print_item_show_value($feedbackitem, $value->value);
                }else {
                    feedback_print_item_show_value($feedbackitem, false);
                }
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->continue_button(new moodle_url($url, array('do_show'=>'showentries')));
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();


<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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

if ($userid) {
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

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

if (($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

require_capability('mod/feedback:viewreports', $context);

////////////////////////////////////////////////////////
//get the responses of given user
////////////////////////////////////////////////////////
if ($do_show == 'showoneentry') {
    //get the feedbackitems
    $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');

    $params = array('feedback'=>$feedback->id,
                    'userid'=>$userid,
                    'anonymous_response'=>FEEDBACK_ANONYMOUS_NO);

    $feedbackcompleted = $DB->get_record('feedback_completed', $params); //arb
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

require('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////
/// Print the links to get responses and analysis
////////////////////////////////////////////////////////
if ($do_show == 'showentries') {
    //print the link to analysis
    if (has_capability('mod/feedback:viewreports', $context)) {
        //get the effective groupmode of this course and module
        if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
            $groupmode =  $cm->groupmode;
        } else {
            $groupmode = $course->groupmode;
        }

        $groupselect = groups_print_activity_menu($cm, $url->out(), true);
        $mygroupid = groups_get_activity_group($cm);

        // preparing the table for output
        $baseurl = new moodle_url('/mod/feedback/show_entries.php');
        $baseurl->params(array('id'=>$id, 'do_show'=>$do_show, 'showall'=>$showall));

        $tablecolumns = array('userpic', 'fullname', 'completed_timemodified');
        $tableheaders = array(get_string('userpic'), get_string('fullnameuser'), get_string('date'));

        if (has_capability('mod/feedback:deletesubmissions', $context)) {
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

        if ($table->get_sql_sort()) {
            $sort = $table->get_sql_sort();
        } else {
            $sort = '';
        }

        list($where, $params) = $table->get_sql_where();
        if ($where) {
            $where .= ' AND';
        }

        //get students in conjunction with groupmode
        if ($groupmode > 0) {
            if ($mygroupid > 0) {
                $usedgroupid = $mygroupid;
            } else {
                $usedgroupid = false;
            }
        } else {
            $usedgroupid = false;
        }

        $matchcount = feedback_count_complete_users($cm, $usedgroupid);
        $table->initialbars(true);

        if ($showall) {
            $startpage = false;
            $pagecount = false;
        } else {
            $table->pagesize($perpage, $matchcount);
            $startpage = $table->get_page_start();
            $pagecount = $table->get_page_size();
        }

        $students = feedback_get_complete_users($cm, $usedgroupid, $where, $params, $sort, $startpage, $pagecount);
        $str_analyse = get_string('analysis', 'feedback');
        $str_complete = get_string('completed_feedbacks', 'feedback');
        $str_course = get_string('course');

        $completed_fb_count = feedback_get_completeds_group_count($feedback, $mygroupid);
        if ($feedback->course == SITEID) {
            $analysisurl = new moodle_url('/mod/feedback/analysis_course.php', array('id'=>$id, 'courseid'=>$courseid));
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">';
            echo $str_course.' '.$str_analyse.' ('.$str_complete.': '.intval($completed_fb_count).')';
            echo '</a>';
            echo $OUTPUT->help_icon('viewcompleted', 'feedback');
            echo $OUTPUT->box_end();
        } else {
            $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$id, 'courseid'=>$courseid));
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">';
            echo $str_analyse.' ('.$str_complete.': '.intval($completed_fb_count).')';
            echo '</a>';
            echo $OUTPUT->box_end();
        }
    }

    //####### viewreports-start
    if (has_capability('mod/feedback:viewreports', $context)) {
        //print the list of students
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo isset($groupselect) ? $groupselect : '';
        echo '<div class="clearer"></div>';
        echo $OUTPUT->box_start('mdl-align');
        if (!$students) {
            $table->print_html();
        } else {
            echo print_string('non_anonymous_entries', 'feedback');
            echo ' ('.count($students).')<hr />';

            foreach ($students as $student) {
                $params = array('userid'=>$student->id,
                                'feedback'=>$feedback->id,
                                'anonymous_response'=>FEEDBACK_ANONYMOUS_NO);

                $completed_count = $DB->count_records('feedback_completed', $params);
                if ($completed_count > 0) {

                    //userpicture and link to the profilepage
                    $fullname_url = $CFG->wwwroot.'/user/view.php?id='.$student->id.'&amp;course='.$course->id;
                    $profilelink = '<strong><a href="'.$fullname_url.'">'.fullname($student).'</a></strong>';
                    $data = array ($OUTPUT->user_picture($student, array('courseid'=>$course->id)), $profilelink);

                    //link to the entry of the user
                    $params = array('feedback'=>$feedback->id,
                                    'userid'=>$student->id,
                                    'anonymous_response'=>FEEDBACK_ANONYMOUS_NO);

                    $feedbackcompleted = $DB->get_record('feedback_completed', $params);
                    $showentryurl_params = array('userid'=>$student->id, 'do_show'=>'showoneentry');
                    $showentryurl = new moodle_url($url, $showentryurl_params);
                    $showentrylink = '<a href="'.$showentryurl->out().'">'.userdate($feedbackcompleted->timemodified).'</a>';
                    $data[] = $showentrylink;

                    //link to delete the entry
                    if (has_capability('mod/feedback:deletesubmissions', $context)) {
                        $delete_url_params = array('id' => $cm->id,
                                            'completedid' => $feedbackcompleted->id,
                                            'do_show' => 'showoneentry');

                        $deleteentryurl = new moodle_url($CFG->wwwroot.'/mod/feedback/delete_completed.php', $delete_url_params);
                        $deleteentrylink = '<a href="'.$deleteentryurl->out().'">'.
                                                get_string('delete_entry', 'feedback').
                                            '</a>';
                        $data[] = $deleteentrylink;
                    }
                    $table->add_data($data);
                }
            }
            $table->print_html();

            $allurl = new moodle_url($baseurl);

            if ($showall) {
                $allurl->param('showall', 0);
                echo $OUTPUT->container(html_writer::link($allurl, get_string('showperpage', '', FEEDBACK_DEFAULT_PAGE_COUNT)),
                                        array(),
                                        'showall');

            } else if ($matchcount > 0 && $perpage < $matchcount) {
                $allurl->param('showall', 1);
                echo $OUTPUT->container(html_writer::link($allurl, get_string('showall', '', $matchcount)),
                                        array(),
                                        'showall');
            }
        }
        ?>
        <hr />
        <table width="100%">
            <tr>
                <td align="left" colspan="2">
                    <?php
                    $params = array('feedback' => $feedback->id,
                                    'anonymous_response' => FEEDBACK_ANONYMOUS_YES);

                    $feedback_completeds_count = $DB->count_records('feedback_completed', $params);
                    print_string('anonymous_entries', 'feedback');
                    echo '&nbsp;('.$feedback_completeds_count.')';
                    ?>
                </td>
                <td align="right">
                    <?php
                        $url_params = array('sesskey'=>sesskey(),
                                        'userid'=>0,
                                        'do_show'=>'showoneentry',
                                        'id'=>$id);
                        $aurl = new moodle_url('show_entries_anonym.php', $url_params);
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
if ($do_show == 'showoneentry') {
    echo $OUTPUT->heading(format_text($feedback->name));

    //print the items
    if (is_array($feedbackitems)) {
        $align = right_to_left() ? 'right' : 'left';
        $usr = $DB->get_record('user', array('id'=>$userid));

        if ($feedbackcompleted) {
            echo $OUTPUT->heading(userdate($feedbackcompleted->timemodified).' ('.fullname($usr).')', 3);
        } else {
            echo $OUTPUT->heading(get_string('not_completed_yet', 'feedback'), 3);
        }

        echo $OUTPUT->box_start('feedback_items');
        $itemnr = 0;
        foreach ($feedbackitems as $feedbackitem) {
            //get the values
            $params = array('completed'=>$feedbackcompleted->id, 'item'=>$feedbackitem->id);
            $value = $DB->get_record('feedback_value', $params);
            echo $OUTPUT->box_start('feedback_item_box_'.$align);
            if ($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
            }

            if ($feedbackitem->typ != 'pagebreak') {
                echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                if (isset($value->value)) {
                    feedback_print_item_show_value($feedbackitem, $value->value);
                } else {
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


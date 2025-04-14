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
 * @package mod_feedback
 */

use mod_feedback\manager;

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/tablelib.php');

////////////////////////////////////////////////////////
//get the params
////////////////////////////////////////////////////////
$id = required_param('id', PARAM_INT);
$subject = optional_param('subject', '', PARAM_CLEANHTML);
$message = optional_param_array('message', '', PARAM_CLEANHTML);
$format = optional_param('format', FORMAT_MOODLE, PARAM_INT);
$messageuser = optional_param_array('messageuser', false, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$perpage = optional_param('perpage', FEEDBACK_DEFAULT_PAGE_COUNT, PARAM_INT);  // how many per page
$showall = optional_param('showall', false, PARAM_INT);  // should we show all users

////////////////////////////////////////////////////////
//get the objects
////////////////////////////////////////////////////////

if ($message) {
    $message = $message['text'];
}

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    throw new \moodle_exception('invalidcoursemodule');
}

//this page only can be shown on nonanonymous feedbacks in courses
//we should never reach this page
if ($feedback->anonymous != FEEDBACK_ANONYMOUS_NO OR $feedback->course == SITEID) {
    throw new \moodle_exception('error');
}

$url = new moodle_url('/mod/feedback/show_nonrespondents.php', array('id'=>$cm->id));

$PAGE->set_url($url);

$context = context_module::instance($cm->id);

//we need the coursecontext to allow sending of mass mails
$coursecontext = context_course::instance($course->id);

require_login($course, true, $cm);

$actionbar = new \mod_feedback\output\responses_action_bar($cm->id, $url);

require_capability('mod/feedback:viewreports', $context);

$currentgroup = groups_get_activity_group($cm, true);
$incompleteusers = feedback_get_incomplete_users($cm, $currentgroup);

$canbulkmessaging = has_capability('moodle/course:bulkmessaging', $coursecontext);
if ($action == 'sendmessage' && $canbulkmessaging) {
    require_sesskey();

    $shortname = format_string($course->shortname,
                            true,
                            array('context' => $coursecontext));
    $strfeedbacks = get_string("modulenameplural", "feedback");

    $htmlmessage = "<body id=\"email\">";

    $link1 = $CFG->wwwroot.'/course/view.php?id='.$course->id;
    $link2 = $CFG->wwwroot.'/mod/feedback/index.php?id='.$course->id;
    $link3 = $CFG->wwwroot.'/mod/feedback/view.php?id='.$cm->id;

    $htmlmessage .= '<div class="navbar">'.
    '<a target="_blank" href="'.$link1.'">'.$shortname.'</a> &raquo; '.
    '<a target="_blank" href="'.$link2.'">'.$strfeedbacks.'</a> &raquo; '.
    '<a target="_blank" href="'.$link3.'">'.format_string($feedback->name, true).'</a>'.
    '</div>';

    $htmlmessage .= $message;
    $htmlmessage .= '</body>';

    $good = 1;
    if (is_array($messageuser)) {

        // Ensure selected users are part of the "incomplete users" set.
        $messageuser = array_intersect($messageuser, $incompleteusers);

        foreach ($messageuser as $userid) {
            $senduser = $DB->get_record('user', array('id'=>$userid));
            $eventdata = new \core\message\message();
            $eventdata->courseid         = $course->id;
            $eventdata->name             = 'message';
            $eventdata->component        = 'mod_feedback';
            $eventdata->userfrom         = $USER;
            $eventdata->userto           = $senduser;
            $eventdata->subject          = $subject;
            $eventdata->fullmessage      = html_to_text($htmlmessage);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml  = $htmlmessage;
            $eventdata->smallmessage     = '';
            $eventdata->courseid         = $course->id;
            $eventdata->contexturl       = $link3;
            $eventdata->contexturlname   = $feedback->name;
            $good = $good && message_send($eventdata);
        }
        if (!empty($good)) {
            $msg = $OUTPUT->heading(get_string('messagedselectedusers'));
        } else {
            $msg = $OUTPUT->heading(get_string('messagedselectedusersfailed'));
        }
        redirect($url, $msg, 4);
        exit;
    }
}

////////////////////////////////////////////////////////
//get the responses of given user
////////////////////////////////////////////////////////

/// Print the page header
$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
$PAGE->set_secondary_active_tab('responses');
if ($responsesnode = $PAGE->settingsnav->find('responses', navigation_node::TYPE_CUSTOM)) {
    $responsesnode->make_active();
}
$PAGE->activityheader->set_attrs([
    'hidecompletion' => true,
    'description' => ''
]);
echo $OUTPUT->header();

/** @var \mod_feedback\output\renderer $renderer */
$renderer = $PAGE->get_renderer('mod_feedback');
echo $renderer->main_action_bar($actionbar);
if (!manager::can_see_others_in_groups($cm)) {
    // The user is not in a group so show message and exit.
    echo $OUTPUT->notification(get_string('notingroup'));
    echo $OUTPUT->footer();
    exit();
}
/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////
/// Print the users with no responses
////////////////////////////////////////////////////////
$groupselect = groups_print_activity_menu($cm, $url->out(), true);

// preparing the table for output
$baseurl = new moodle_url('/mod/feedback/show_nonrespondents.php');
$baseurl->params(array('id'=>$id, 'showall'=>$showall));

$tablecolumns = array('userpic', 'fullname', 'status');
$tableheaders = array(get_string('userpic'), get_string('fullnameuser'), get_string('status'));

if ($canbulkmessaging) {
    $tablecolumns[] = 'select';

    // Build the select/deselect all control.
    $selectallid = 'selectall-non-respondents';
    $togglercheckbox = new \core\output\checkbox_toggleall('feedback-non-respondents', true, [
        'id' => $selectallid,
        'name' => $selectallid,
        'value' => 1,
        'label' => get_string('select'),
        // Consistent label to prevent the select column from resizing.
        'selectall' => get_string('select'),
        'deselectall' => get_string('select'),
        'labelclasses' => 'm-0',
    ]);
    $tableheaders[] = $OUTPUT->render($togglercheckbox);
}

$table = new flexible_table('feedback-shownonrespondents-'.$course->id);

$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->define_baseurl($baseurl);

$table->sortable(true, 'lastname', SORT_DESC);
$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'showentrytable');
$table->set_attribute('class', 'table generaltable');
$table->set_control_variables(array(
            TABLE_VAR_SORT    => 'ssort',
            TABLE_VAR_IFIRST  => 'sifirst',
            TABLE_VAR_ILAST   => 'silast',
            TABLE_VAR_PAGE    => 'spage'
            ));

$table->no_sorting('select');
$table->no_sorting('status');

$table->setup();

if ($table->get_sql_sort()) {
    $sort = $table->get_sql_sort();
} else {
    $sort = '';
}

$matchcount = count($incompleteusers);
$table->initialbars(false);

if ($showall) {
    $startpage = false;
    $pagecount = false;
} else {
    $table->pagesize($perpage, $matchcount);
    $startpage = $table->get_page_start();
    $pagecount = $table->get_page_size();
}

// Return students record including if they started or not the feedback.
$students = feedback_get_incomplete_users($cm, $currentgroup, $sort, $startpage, $pagecount, true);
//####### viewreports-start
//print the list of students
echo $OUTPUT->heading(get_string('non_respondents_students', 'feedback', $matchcount), 4);
echo isset($groupselect) ? $groupselect : '';
echo '<div class="clearer"></div>';

if (empty($students)) {
    echo $OUTPUT->notification(get_string('noexistingparticipants', 'enrol'));
} else {

    if ($canbulkmessaging) {
        echo '<form class="mform" action="show_nonrespondents.php" method="post" id="feedback_sendmessageform">';
    }

    foreach ($students as $student) {
        //userpicture and link to the profilepage
        $profileurl = $CFG->wwwroot.'/user/view.php?id='.$student->id.'&amp;course='.$course->id;
        $profilelink = '<strong><a href="'.$profileurl.'">'.fullname($student).'</a></strong>';
        $data = array($OUTPUT->user_picture($student, array('courseid' => $course->id)), $profilelink);

        if ($student->feedbackstarted) {
            $data[] = get_string('started', 'feedback');
        } else {
            $data[] = get_string('not_started', 'feedback');
        }

        //selections to bulk messaging
        if ($canbulkmessaging) {
            $checkbox = new \core\output\checkbox_toggleall('feedback-non-respondents', false, [
                'id' => 'messageuser-' . $student->id,
                'name' => 'messageuser[]',
                'classes' => 'me-1',
                'value' => $student->id,
                'label' => get_string('includeuserinrecipientslist', 'mod_feedback', fullname($student)),
                'labelclasses' => 'accesshide',
            ]);
            $data[] = $OUTPUT->render($checkbox);
        }
        $table->add_data($data);
    }
    $table->print_html();

    $allurl = new moodle_url($baseurl);

    if ($showall) {
        $allurl->param('showall', 0);
        echo $OUTPUT->container(html_writer::link($allurl, get_string('showperpage', '', FEEDBACK_DEFAULT_PAGE_COUNT)),
                                    array(), 'showall');

    } else if ($matchcount > 0 && $perpage < $matchcount) {
        $allurl->param('showall', 1);
        echo $OUTPUT->container(html_writer::link($allurl, get_string('showall', '', $matchcount)), array(), 'showall');
    }
    if ($canbulkmessaging) {
        echo '<fieldset class="clearfix">';
        echo '<legend class="ftoggler">'.get_string('send_message', 'feedback').'</legend>';
        echo '<div>';
        echo '<label for="feedback_subject">'.get_string('subject', 'feedback').'&nbsp;</label>';
        echo '<input type="text" id="feedback_subject" size="50" maxlength="255" name="subject" value="'.s($subject).'" />';
        echo '</div>';
        echo $OUTPUT->print_textarea('message', 'edit-message', $message, 15, 25);
        print_string('formathtml');
        echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
        echo '<br /><div class="buttons">';
        echo '<input type="submit" name="send_message" value="'.get_string('send', 'feedback').'" class="btn btn-secondary" />';
        echo '</div>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="action" value="sendmessage" />';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '</fieldset>';
        echo '</form>';
    }
}

/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();


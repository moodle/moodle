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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes, Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_pdfannotator\output\statistics;

defined('MOODLE_INTERNAL') || die();

$action = optional_param('action', 'view', PARAM_ALPHA); // The default action is 'view'.

$taburl = new moodle_url('/mod/pdfannotator/view.php', array('id' => $id));

$myrenderer = $PAGE->get_renderer('mod_pdfannotator');

require_course_login($pdfannotator->course, true, $cm);

/* * ********************************************** Display overview page *********************************************** */

if ($action === 'overview') {
    // Go to question-overview by default.
    $action = 'overviewquestions';
}

if ($action === 'forwardquestion') {
    require_sesskey();
    require_capability('mod/pdfannotator:forwardquestions', $context);
    require_once($CFG->dirroot . '/mod/pdfannotator/forward_form.php');
    global $USER;

    $commentid = required_param('commentid', PARAM_INT);
    $fromoverview = optional_param('fromoverview', 0, PARAM_INT);
    $cminfo = pdfannotator_instance::get_cm_info($cm->course);
    // Make sure user is allowed to see cm with the question. (Might happen if user changes commentid in url).
    list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cminfo));
    $sql = "SELECT c.*, a.page, cm.id AS cmid "
        . "FROM {pdfannotator_comments} c "
        . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
        . "JOIN {pdfannotator} p ON a.pdfannotatorid = p.id "
        . "JOIN {course_modules} cm ON p.id = cm.instance "
        . "WHERE c.isdeleted = 0 AND c.id = ? AND cm.id $insql";
    $params = array_merge([$commentid], $inparams);
    $comments = $DB->get_records_sql($sql, $params);
    $error = false;
    if (!$comments) {
        $error = true;
    } else {
        $comment = $comments[$commentid];
        if (!$error && $comment->ishidden && !has_capability('mod/pdfannotator:seehiddencomments', $context)) {
            $error = true;
        }
    }

    $possiblerecipients = get_enrolled_users($context, 'mod/pdfannotator:getforwardedquestions');
    $recipientslist = [];
    foreach ($possiblerecipients as $recipient) {
        if ($recipient->id === $USER->id) {
            continue;
        }
        $recipientslist[$recipient->id] = $recipient->firstname . ' ' . $recipient->lastname;
    }

    if (count($recipientslist) === 0) {
        $error = true;
        $errorinfo = get_string('error:forwardquestionnorecipient', 'pdfannotator');
    }

    if ($error) { // An error occured e.g. comment doesn't exist.
        if (!isset($errorinfo)) {
            $errorinfo = get_string('error:forwardquestion', 'pdfannotator'); // Display error notification.
        }
        echo "<span id='subscriptionPanel' class='usernotifications'>" .
            "<div class='alert alert-success alert-block fade in' role='alert'>$errorinfo</div></span>";
        if ($fromoverview) {
            // If user forwarded question from overview go back to overview.
            $action = 'overviewquestions';
        } else {
            // Else go to document.
            $action = 'view';
        }
    } else {

        $data = new stdClass();
        $data->course = $cm->course;
        $data->pdfannotatorid = $cm->instance;
        $data->pdfname = format_string($cm->name, true);
        $data->commentid = $commentid;
        $data->id = $cm->id; // Course module id.
        $data->action = 'forwardquestion';
        $data->fromoverview = $fromoverview;

        // Initialise mform and pass on $data-object to it.
        $mform = new pdfannotator_forward_form(null, ['comment' => $comment, 'recipients' => $recipientslist]);
        $mform->set_data($data);

        if ($mform->is_cancelled()) { // Form was cancelled.
            // Go back to overview or document.
            if ($fromoverview) {
                $action = 'overviewquestions';
            } else {
                $action = 'view';
            }
        } else if ($data = $mform->get_data()) { // Process validated data. $mform->get_data() returns data posted in form.
            $url = (new moodle_url('/mod/pdfannotator/view.php', array('id' => $comment->cmid,
                'page' => $comment->page, 'annoid' => $comment->annotationid, 'commid' => $comment->id)))->out();

            $params = new stdClass();
            $params->sender = $USER->firstname . ' ' . $USER->lastname;
            $params->questioncontent = $comment->content;
            $params->message = $data->message;
            $params->urltoquestion = $url;

            if (isset($data->recipients)) {
                pdfannotator_send_forward_message($data->recipients, $params, $course, $cm, $context);
            }
            if ($fromoverview) {
                // If user forwarded question from overview go back to overview.
                $action = 'overviewquestions';
            } else {
                // Else go to document.
                $action = 'view';
            }
        } else { // Executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            $PAGE->set_title("forward_form");
            echo $OUTPUT->heading(get_string('titleforwardform', 'pdfannotator'));
            $mform->display(); // Display form.
        }
    }
}
/*
 * This section prints a subpage of overview called 'unsolved questions'.
 */
if ($action === 'overviewquestions') {

    global $OUTPUT, $CFG;

    require_once($CFG->libdir . '/tablelib.php');
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');


    $currentpage = optional_param('page', 0, PARAM_INT);
    $itemsperpage = optional_param('itemsperpage', 5, PARAM_INT);
    $questionfilter = optional_param('questionfilter', 0, PARAM_INT); // Default 0 means: Display only unsolved/open questions.

    $thisannotator = $pdfannotator->id;
    $thiscourse = $pdfannotator->course;
    $cmid = get_coursemodule_from_instance('pdfannotator', $thisannotator, $thiscourse, false, MUST_EXIST)->id;

    pdfannotator_prepare_overviewpage($id, $myrenderer, $taburl, ['tab' => 'overview', 'action' => $action], $pdfannotator,
        $context);
    echo $OUTPUT->heading(get_string('questionstab', 'pdfannotator') . ' ' .
            $OUTPUT->help_icon('questionstabicon', 'mod_pdfannotator')) . " <span id='pdfannotator-filter'></span>";

    $questions = pdfannotator_get_questions($thiscourse, $context, $questionfilter);

    if (empty($questions)) {
        if ($questionfilter == 1) {
            $info = get_string('noquestionsclosed_overview', 'pdfannotator');
        } else if ($questionfilter == 2) {
            $info = get_string('noquestions_overview', 'pdfannotator');
        } else {
            $info = get_string('noquestionsopen_overview', 'pdfannotator');
        }
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
    } else {
        $urlparams = array('action' => 'overviewquestions', 'id' => $cmid, 'page' => $currentpage, 'itemsperpage' => $itemsperpage,
            'questionfilter' => $questionfilter);
        pdfannotator_print_questions($questions, $thiscourse, $urlparams, $currentpage, $itemsperpage, $context);
    }
}
/*
 * This section subscribes the user to a particular question and then rerenders the overview table of
 * all answers.
 */
if ($action === 'subscribeQuestion') {
    require_sesskey();
    require_capability('mod/pdfannotator:subscribe', $context);

    global $DB;

    $annotationid = required_param('annotationid', PARAM_INT);

    $annotatorid = $DB->get_field('pdfannotator_annotations', 'pdfannotatorid', ['id' => $annotationid], $strictness = MUST_EXIST);

    $subscriptionid = pdfannotator_comment::insert_subscription($annotationid, $context);

    if (!empty($subscriptionid)) {
        $info = get_string('successfullySubscribed', 'pdfannotator');
        echo "<span id='subscriptionPanel' class='usernotifications'>" .
            "<div class='alert alert-success alert-block fade in' role='alert'>$info</div></span>";
    }

    $action = 'overviewanswers';
}
/*
 * This section unsubscribes the user from a particular question and then rerenders the overview table of
 * answers to questions to which the user is subscribed.
 */
if ($action === 'unsubscribeQuestion') {
    require_sesskey();
    require_capability('mod/pdfannotator:subscribe', $context);

    global $DB;

    $annotationid = required_param('annotationid', PARAM_INT);
    $answerfilter = optional_param('answerfilter', 1, PARAM_INT);

    $annotatorid = $DB->get_field('pdfannotator_annotations', 'pdfannotatorid', ['id' => $annotationid], $strictness = MUST_EXIST);

    $entrycount = pdfannotator_comment::delete_subscription($annotationid);

    if (!empty($entrycount) && ($answerfilter == 1)) {
        if ($entrycount == 1) {
            $info = get_string('successfullyUnsubscribedSingular', 'pdfannotator', $entrycount);
        } else if ($entrycount == 2) {
            $info = get_string('successfullyUnsubscribedTwo', 'pdfannotator', $entrycount);
        } else {
            $info = get_string('successfullyUnsubscribedPlural', 'pdfannotator', $entrycount);
        }
    } else {
        $info = get_string('successfullyUnsubscribed', 'pdfannotator', $entrycount);
    }
    echo "<span id='pdfannotator_notificationpanel' class='usernotifications'>" .
        "<div class='alert alert-success alert-block fade in' role='alert'>$info</div></span>";

    $action = 'overviewanswers';
}
/*
 * This section prints a subpage of overview called 'answers'. It lists all answers to questions the current
 * user asked or subscribed to.
 */
if ($action === 'overviewanswers') {

    require_once($CFG->libdir . '/tablelib.php');
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

    global $CFG, $OUTPUT, $DB;

    $currentpage = optional_param('page', 0, PARAM_INT);

    $itemsperpage = optional_param('itemsperpage', 5, PARAM_INT);
    $answerfilter = optional_param('answerfilter', 1, PARAM_INT);

    $thisannotator = $pdfannotator->id;
    $thiscourse = $pdfannotator->course;
    $cmid = get_coursemodule_from_instance('pdfannotator', $thisannotator, $thiscourse, false, MUST_EXIST)->id;

    pdfannotator_prepare_overviewpage($id, $myrenderer, $taburl, ['tab' => 'overview', 'action' => $action], $pdfannotator,
        $context);
    echo $OUTPUT->heading(get_string('answerstab', 'pdfannotator') . ' ' .
            $OUTPUT->help_icon('answerstabicon', 'pdfannotator')) . " <span id='pdfannotator-filter'></span>";

    $data = pdfannotator_get_answers_for_this_user($thiscourse, $context, $answerfilter);

    if (empty($data)) {
        if ($answerfilter == 1) {
            $info = get_string('noanswerssubscribed', 'pdfannotator');
        } else {
            $info = get_string('noanswers', 'pdfannotator');
        }
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
    } else {
        $urlparams = array('action' => 'overviewanswers', 'id' => $cmid, 'page' => $currentpage, 'itemsperpage' => $itemsperpage,
            'answerfilter' => $answerfilter);
        $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);
        pdfannotator_print_answers($data, $thiscourse, $url, $currentpage, $itemsperpage, $cmid, $answerfilter, $context);
    }
}
/*
 * This section prints a subpage of overview called "My posts".
 */
if ($action === 'overviewownposts') {

    require_once($CFG->libdir . '/tablelib.php');
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

    global $CFG, $OUTPUT;

    $currentpage = optional_param('page', 0, PARAM_INT);
    $itemsperpage = optional_param('itemsperpage', 5, PARAM_INT);

    $thisannotator = $pdfannotator->id;
    $thiscourse = $pdfannotator->course;
    $cmid = get_coursemodule_from_instance('pdfannotator', $thisannotator, $thiscourse, false, MUST_EXIST)->id;

    pdfannotator_prepare_overviewpage($id, $myrenderer, $taburl, ['tab' => 'overview', 'action' => $action], $pdfannotator,
        $context);
    echo $OUTPUT->heading(get_string('ownpoststab', 'pdfannotator') . ' ' .
        $OUTPUT->help_icon('ownpoststabicon', 'mod_pdfannotator'));

    $posts = pdfannotator_get_posts_by_this_user($thiscourse, $context);

    if (empty($posts)) {
        $info = get_string('nomyposts', 'pdfannotator');
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
    } else {
        $urlparams = array('action' => 'overviewownposts', 'id' => $cmid, 'page' => $currentpage, 'itemsperpage' => $itemsperpage);
        $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);
        pdfannotator_print_this_users_posts($posts, $thiscourse, $url, $currentpage, $itemsperpage);
    }
}
/*
 * This section marks a report as read and then rerenders the overview table of reports
 * (either unread reports (reportfiler == 0) or all reports (reportfilter == 2)).
 */
if ($action === 'markreportasread') { // XXX Rename key and move it into $action === 'overviewreports'.
    require_sesskey();
    require_capability('mod/pdfannotator:viewreports', $context);

    global $DB;

    $reportid = required_param('reportid', PARAM_INT);
    $itemsperpage = optional_param('itemsperpage', 5, PARAM_INT);
    $reportfilter = optional_param('reportfilter', 0, PARAM_INT);

    $success = $DB->update_record('pdfannotator_reports', array("id" => $reportid, "seen" => 1), $bulk = false);

    // Give feedback to the user.
    if ($success) {
        switch ($reportfilter) {
            case 0:// Filter is currently set to show read reports only.
                $info = get_string('successfullymarkedasreadandnolongerdisplayed', 'pdfannotator');
                break;
            case 2: // Filter is currently set to show all reports in this course.
                $info = get_string('successfullymarkedasread', 'pdfannotator');
                break;
            default:
                $info = get_string('successfullymarkedasread', 'pdfannotator');
        }
        echo "<span id='pdfannotator_notificationpanel' class='usernotifications'>" .
            "<div class='alert alert-success alert-block fade in' role='alert'>$info</div></span>";
    } else {
        $info = get_string('error:markasread', 'pdfannotator');
        echo "<span id='pdfannotator_notificationpanel' class='usernotifications'>" .
            "<div class='alert alert-error alert-block fade in' role='alert'>$info</div></span>";
    }

    $action = 'overviewreports'; // This will do the actual rerendering of the page (see below).
}
/*
 * This section marks a report as read and then rerenders the overview table of reports
 * (either unread reports (reportfiler == 0) or all reports (reportfilter == 2)).
 */
if ($action === 'markreportasunread') { // XXX Rename key and move it into $action === 'overviewreports'.
    require_sesskey();
    require_capability('mod/pdfannotator:viewreports', $context);

    global $DB;

    $reportid = required_param('reportid', PARAM_INT);
    $itemsperpage = optional_param('itemsperpage', 5, PARAM_INT);
    $reportfilter = optional_param('reportfilter', 2, PARAM_INT);

    $success = $DB->update_record('pdfannotator_reports', array("id" => $reportid, "seen" => 0), $bulk = false);

    // Give feedback to the user.
    if ($success) {
        switch ($reportfilter) {
            case 1: // I.e.: Filter is currently set to show unread reports only.
                $info = get_string('successfullymarkedasunreadandnolongerdisplayed', 'pdfannotator');
                break;
            case 2: // I.e.: Filter is currently set to show all reports in this course.
                $info = get_string('successfullymarkedasunread', 'pdfannotator');
                break;
            default:
                $info = get_string('successfullymarkedasunread', 'pdfannotator');
        }
        echo "<span id='pdfannotator_notificationpanel' class='usernotifications'>" .
            "<div class='alert alert-success alert-block fade in' role='alert'>$info</div></span>";
    } else {
        $info = get_string('error:markasunread', 'pdfannotator');
        echo "<span id='pdfannotator_notificationpanel' class='usernotifications'>" .
            "<div class='alert alert-error alert-block fade in' role='alert'>$info</div></span>";
    }

    $action = 'overviewreports'; // This will do the actual rerendering of the page (see below).
}
/*
 * This section prints a subpage of overview called "Reports" were comments that were reported as inappropriate are listed.
 */
if ($action === 'overviewreports') {

    require_once($CFG->libdir . '/tablelib.php');
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

    global $CFG, $OUTPUT;

    $currentpage = optional_param('page', 0, PARAM_INT);
    $itemsperpage = optional_param('itemsperpage', 5, PARAM_INT);
    $reportfilter = optional_param('reportfilter', 0, PARAM_INT);

    $thisannotator = $pdfannotator->id;
    $thiscourse = $pdfannotator->course;
    $cmid = get_coursemodule_from_instance('pdfannotator', $thisannotator, $thiscourse, false, MUST_EXIST)->id;

    pdfannotator_prepare_overviewpage($id, $myrenderer, $taburl, ['tab' => 'overview', 'action' => $action], $pdfannotator,
        $context);
    echo $OUTPUT->heading(get_string('reportstab', 'pdfannotator') . ' ' .
            $OUTPUT->help_icon('reportstabicon', 'mod_pdfannotator')) . " <span id='pdfannotator-filter'></span>";

    $reports = pdfannotator_get_reports($thiscourse, $context,  $reportfilter);

    if (empty($reports)) {
        switch ($reportfilter) {
            case 0:
                $info = get_string('nounreadreports', 'pdfannotator');
                break;
            case 1:
                $info = get_string('noreadreports', 'pdfannotator');
                break;
            case 2:
                $info = get_string('noreports', 'pdfannotator');
                break;
        }
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
    } else {
        $urlparams = array('action' => 'overviewreports', 'id' => $cmid, 'page' => $currentpage, 'itemsperpage' => $itemsperpage,
            'reportfilter' => $reportfilter);
        $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);
        pdfannotator_print_reports($reports, $thiscourse, $url, $currentpage, $itemsperpage, $cmid, $reportfilter, $context);
    }
}

/* * ********************************** Display the pdf in its editor (default action) *************************************** */

if ($action === 'view') { // Default.
    $PAGE->set_title("annotatorview");
    echo $myrenderer->pdfannotator_render_tabs($taburl, $pdfannotator->name, $context, $action);

    pdfannotator_display_embed($pdfannotator, $cm, $course, $file, $page, $annoid, $commid);
}

/* * ********************************************** Display statistics *********************************************** */

if ($action === 'statistic') {

    require_capability('mod/pdfannotator:viewstatistics', $context);

    require_once($CFG->dirroot . '/mod/pdfannotator/model/statistics.class.php');

    echo $myrenderer->pdfannotator_render_tabs($taburl, $pdfannotator->name, $context, $action);
    $PAGE->set_title("statisticview");
    echo $OUTPUT->heading(get_string('activities', 'pdfannotator'));

    // Give javascript access to the language string repository.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('pdfannotator', 'en'); // Method gets the strings of the language files.
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator'); // Method to use the language-strings in javascript.
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/locallib.js?ver=00002"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/statistic.js?ver=0004"));
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    $capabilities = new stdClass();
    $capabilities->viewquestions = has_capability('mod/pdfannotator:viewquestions', $context);
    $capabilities->viewanswers = has_capability('mod/pdfannotator:viewanswers', $context);
    $capabilities->viewposts = has_capability('mod/pdfannotator:viewposts', $context);
    $capabilities->viewreports = has_capability('mod/pdfannotator:viewreports', $context);
    $capabilities->viewteacherstatistics = has_capability('mod/pdfannotator:viewteacherstatistics', $context);

    echo $myrenderer->render_statistic(new statistics($cm->instance, $course->id, $capabilities, $id));
}

/* * ***************************************** Display form for reporting a comment  ******************************************** */

if ($action === 'report') {

    require_once($CFG->dirroot . '/mod/pdfannotator/reportform.php');
    require_once($CFG->dirroot . '/mod/pdfannotator/model/comment.class.php');

    global $DB;

    // Get comment id.
    $commentid = optional_param('commentid', 0, PARAM_INT);

    // Contextual data to pass on to the report form.
    $data = new stdClass();
    $data->course = $cm->course;
    $data->pdfannotatorid = $cm->instance;
    $data->pdfname = format_string($cm->name, true);
    $data->commentid = $commentid;
    $data->id = $id; // Course module id.
    $data->action = 'report';

    // Initialise mform and pass on $data-object to it.
    $mform = new pdfannotator_reportform();
    $mform->set_data($data);

    /*     * ******************* Form processing and displaying is done here ************************ */
    if ($mform->is_cancelled()) {
        $action = 'view';
        echo $myrenderer->pdfannotator_render_tabs($taburl, $pdfannotator->name, $context, $action);
        pdfannotator_display_embed($pdfannotator, $cm, $course, $file);
    } else if ($report = $mform->get_data()) { // Process validated data. $mform->get_data() returns data posted in form.
        require_sesskey();
        global $USER;

        // 1. Notify course manager(s).
        $recipients = get_enrolled_users($context, 'mod/pdfannotator:viewreports');
        $name = 'newreport';
        $report->reportinguser = fullname($USER);
        $report->url = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '&action=overviewreports';
        $messagetext = new stdClass();
        $modulename = format_string($cm->name, true);
        $messagetext->text = pdfannotator_format_notification_message_text($course, $cm, $context,
            get_string('modulename', 'pdfannotator'), $modulename, $report, 'reportadded');
        $messagetext->url = $report->url;
        try {
            foreach ($recipients as $recipient) {
                $messagetext->html = pdfannotator_format_notification_message_html($course, $cm, $context,
                    get_string('modulename', 'pdfannotator'), $modulename, $report, 'reportadded', $recipient->id);
                $messageid = pdfannotator_notify_manager($recipient, $course, $cm, $name, $messagetext);
            }
            // 2. Notify the reporting user that their report has been sent off (display blue toast box at top of page).
            \core\notification::info(get_string('reportwassentoff', 'pdfannotator'));
        } catch (Exception $ex) {
            $info = $ex->getMessage();
            \core\notification::error($info);
        }

        // 3. Save report in db.
        $record = new stdClass();
        $record->commentid = $report->commentid;
        $record->courseid = $cm->course;
        $record->pdfannotatorid = $cm->instance;
        $record->message = $report->introduction;
        $record->userid = $USER->id;
        $record->timecreated = time();
        $record->seen = 0;

        $reportid = $DB->insert_record('pdfannotator_reports', $record, $returnid = true, $bulk = false);
        if (empty($reportid)) {
            \core\notification::error(get_string('error:reportComment', 'pdfannotator'));
        }

        $action = 'view';
        echo $myrenderer->pdfannotator_render_tabs($taburl, $pdfannotator->name, $context, $action);
        pdfannotator_display_embed($pdfannotator, $cm, $course, $file);
    } else { // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
        // or on the first display of the form.
        $PAGE->set_title("reportform");
        echo $OUTPUT->heading(get_string('titleforreportcommentform', 'pdfannotator'));

        // Get information about the comment to be reported.
        $comment = $DB->get_record('pdfannotator_comments', ['id' => $commentid]);
        $comment->content = pdfannotator_get_relativelink($comment->content, $comment->id, $context);
        $info = pdfannotator_comment_info::make_from_comment($comment);

        // Display it in a table.
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        echo $myrenderer->render_pdfannotator_comment_info($info);

        // Now display the complaint form itself.
        $mform->display();
    }
    return;
}

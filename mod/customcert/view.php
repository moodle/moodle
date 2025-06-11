<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Handles viewing a customcert.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$downloadown = optional_param('downloadown', false, PARAM_BOOL);
$downloadall = optional_param('downloadall', 0, PARAM_INT);
$downloadtable = optional_param('download', null, PARAM_ALPHA);
$downloadissue = optional_param('downloadissue', 0, PARAM_INT);
$deleteissue = optional_param('deleteissue', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', \mod_customcert\certificate::CUSTOMCERT_PER_PAGE, PARAM_INT);

$cm = get_coursemodule_from_id('customcert', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', MUST_EXIST);
$template = $DB->get_record('customcert_templates', ['id' => $customcert->templateid], '*', MUST_EXIST);

// Ensure the user is allowed to view this page.
require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/customcert:view', $context);

$canreceive = has_capability('mod/customcert:receiveissue', $context);
$canmanage = has_capability('mod/customcert:manage', $context);
$canviewreport = has_capability('mod/customcert:viewreport', $context);

// Initialise $PAGE.
$pageurl = new moodle_url('/mod/customcert/view.php', ['id' => $cm->id]);
\mod_customcert\page_helper::page_setup($pageurl, $context, format_string($customcert->name));

// Check if the user can view the certificate based on time spent in course.
if ($customcert->requiredtime && !$canmanage) {
    if (\mod_customcert\certificate::get_course_time($course->id) < ($customcert->requiredtime * 60)) {
        $a = new stdClass;
        $a->requiredtime = $customcert->requiredtime;
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        notice(get_string('requiredtimenotmet', 'customcert', $a), $url);
        die;
    }
}

// Check if we are deleting an issue.
if ($deleteissue && $canmanage && confirm_sesskey()) {
    if (!$confirm) {
        $nourl = new moodle_url('/mod/customcert/view.php', ['id' => $id]);
        $yesurl = new moodle_url('/mod/customcert/view.php',
            [
                'id' => $id,
                'deleteissue' => $deleteissue,
                'confirm' => 1,
                'sesskey' => sesskey(),
            ]
        );

        // Show a confirmation page.
        $PAGE->navbar->add(get_string('deleteconfirm', 'customcert'));
        $message = get_string('deleteissueconfirm', 'customcert');
        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($customcert->name));
        echo $OUTPUT->confirm($message, $yesurl, $nourl);
        echo $OUTPUT->footer();
        exit();
    }

    // Delete the issue.
    $DB->delete_records('customcert_issues', ['id' => $deleteissue, 'customcertid' => $customcert->id]);

    // Redirect back to the manage templates page.
    redirect(new moodle_url('/mod/customcert/view.php', ['id' => $id]));
}

// Get the current groups mode.
if ($groupmode = groups_get_activity_groupmode($cm)) {
    groups_get_activity_group($cm, true);
}

// Check if we are downloading all certificates.
if ($downloadall && $canviewreport && confirm_sesskey()) {
    $template = new \mod_customcert\template($template);
    $issues = \mod_customcert\certificate::get_issues($customcert->id, $groupmode, $cm, 0, 0);

    // The button is not visible if there are no issues, so in this case just redirect back to this page.
    if (empty($issues)) {
        redirect(new moodle_url('/mod/customcert/view.php', ['id' => $id]));
    }

    \mod_customcert\certificate::download_all_issues_for_instance($template, $issues);
    exit();
}

$event = \mod_customcert\event\course_module_viewed::create([
    'objectid' => $customcert->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('customcert', $customcert);
$event->trigger();

// Check that we are not downloading a certificate PDF.
if (!$downloadown && !$downloadissue) {
    // Generate the table to the report if there are issues to display.
    if ($canviewreport) {
        // Get the total number of issues.
        $reporttable = new \mod_customcert\report_table($customcert->id, $cm, $groupmode, $downloadtable);
        $reporttable->define_baseurl($pageurl);

        if ($reporttable->is_downloading()) {
            $reporttable->download();
            exit();
        }
    }

    // If the current user has been issued a customcert generate HTML to display the details.
    $issuehtml = '';
    $issues = $DB->get_records('customcert_issues', ['userid' => $USER->id, 'customcertid' => $customcert->id]);
    if ($issues && !$canmanage) {
        // Get the most recent issue (there should only be one).
        $issue = reset($issues);
        $issuestring = get_string('receiveddate', 'customcert') . ': ' . userdate($issue->timecreated);
        $issuehtml = $OUTPUT->box($issuestring);
    }

    // Create the button to download the customcert.
    $downloadbutton = '';
    if ($canreceive) {
        $linkname = get_string('getcustomcert', 'customcert');
        $link = new moodle_url('/mod/customcert/view.php', ['id' => $cm->id, 'downloadown' => true]);
        $downloadbutton = new single_button($link, $linkname, 'get', single_button::BUTTON_PRIMARY);
        $downloadbutton->class .= ' m-b-1';  // Seems a bit hackish, ahem.
        $downloadbutton = $OUTPUT->render($downloadbutton);
    }

    $numissues = \mod_customcert\certificate::get_number_of_issues($customcert->id, $cm, $groupmode);

    $downloadallbutton = '';
    if ($canviewreport && $numissues > 0) {
        $linkname = get_string('downloadallissuedcertificates', 'customcert');
        $link = new moodle_url('/mod/customcert/view.php',
            [
                'id' => $cm->id,
                'downloadall' => true,
                'sesskey' => sesskey(),
            ]
        );
        $downloadallbutton = new single_button($link, $linkname, 'get', single_button::BUTTON_SECONDARY);
        $downloadallbutton->class .= ' m-b-1';  // Seems a bit hackish, ahem.
        $downloadallbutton = $OUTPUT->render($downloadallbutton);
    }

    // Output all the page data.
    echo $OUTPUT->header();
    echo $issuehtml;
    echo $downloadbutton;
    echo $downloadallbutton;
    if (isset($reporttable)) {
        echo $OUTPUT->heading(get_string('listofissues', 'customcert', $numissues), 3);
        groups_print_activity_menu($cm, $pageurl);
        echo $reporttable->out($perpage, false);
    }
    echo $OUTPUT->footer($course);
    exit();
} else if ($canreceive || $canviewreport) { // Output to pdf.
    // Set the userid value of who we are downloading the certificate for.
    $userid = $USER->id;
    if ($downloadown) {
        // Create new customcert issue record if one does not already exist.
        if (!$DB->record_exists('customcert_issues', ['userid' => $USER->id, 'customcertid' => $customcert->id])) {
            \mod_customcert\certificate::issue_certificate($customcert->id, $USER->id);
        }

        // Set the custom certificate as viewed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);
    } else if ($downloadissue && $canviewreport) {
        $userid = $downloadissue;
    }

    // Hack alert - don't initiate the download when running Behat.
    if (defined('BEHAT_SITE_RUNNING')) {
        redirect(new moodle_url('/mod/customcert/view.php', ['id' => $cm->id]));
    }

    \core\session\manager::write_close();

    // Now we want to generate the PDF.
    $template = new \mod_customcert\template($template);
    $template->generate_pdf(false, $userid);
    exit();
}

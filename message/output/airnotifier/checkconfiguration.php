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
 * Check and test Push notifications configuration.
 *
 * @package    message_airnotifier
 * @copyright  2020 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->libdir . '/filelib.php');

$pageurl = new moodle_url('/message/output/airnotifier/checkconfiguration.php');
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/site:config', context_system::instance());

// Build a path.
$strheading = get_string('checkconfiguration', 'message_airnotifier');
$PAGE->navbar->add(get_string('administrationsite'));
$returl = new moodle_url('/admin/category.php', ['category' => 'messaging']);
$PAGE->navbar->add(get_string('messagingcategory', 'admin'), $returl);
$returl = new moodle_url('/admin/settings.php', ['section' => 'messagesettingairnotifier']);
$PAGE->navbar->add(get_string('pluginname', 'message_airnotifier'), $returl);
$PAGE->navbar->add($strheading);

$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($strheading);

$manager = new message_airnotifier_manager();

// Sending a test Push notification.
if (data_submitted()) {
    require_sesskey();

    if (optional_param('confirm', 0, PARAM_INT)) {
        $manager->send_test_notification($USER);

        redirect($pageurl, get_string('eventnotificationsent', 'message'), 5);
    } else {

        if (!$manager->has_enabled_devices($CFG->airnotifiermobileappname)) {
            // The user has not connected to the site with the app yet.
            redirect($pageurl, get_string('nodevices', 'message_airnotifier'), 5, \core\output\notification::NOTIFY_ERROR);
        }

        echo $OUTPUT->header();
        $message = get_string('sendtestconfirmation', 'message_airnotifier');
        $confirmurl = new moodle_url($pageurl->out(false), ['confirm' => 1]);
        $continueb = new single_button($confirmurl, get_string('continue'), 'post');
        $cancelb = new single_button($pageurl, get_string('cancel'), 'get');
        echo $OUTPUT->confirm($message, $continueb, $cancelb);
        echo $OUTPUT->footer();
    }
    die;
}

$checkresults = $manager->check_configuration();

$table = new \html_table();
$table->data = [];
$table->head  = [
    get_string('status'),
    get_string('check'),
    get_string('summary'),
];
$table->colclasses = [
    'rightalign status',
    'leftalign check',
    'leftalign summary',
];
$table->id = 'message_airnotifier_checkconfiguration';
$table->attributes = ['class' => 'admintable generaltable'];
$table->data = [];

$senddisabled = false;
foreach ($checkresults as $result) {
    if ($result->get_status() == core\check\result::CRITICAL || $result->get_status() == core\check\result::ERROR) {
        $senddisabled = true;
    }
    $table->data[] = [$OUTPUT->check_result($result), $result->get_summary(), $result->get_details()];
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strheading);

// Check table.
echo \html_writer::table($table);

// Test notification button.
$button = $OUTPUT->single_button($PAGE->url, get_string('sendtest', 'message_airnotifier'), 'post', ['disabled' => $senddisabled]);
echo $OUTPUT->box($button, 'clearfix mdl-align');

echo $OUTPUT->footer();

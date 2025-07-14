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
 * Registration configuration for the Brickfield too.
 *
 * @package    tool_brickfield
 * @author     2020 JM Tomas <jmtomas@tresipunt.com>
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

use tool_brickfield\brickfieldconnect;
use tool_brickfield\form\registration_form;
use tool_brickfield\manager;
use tool_brickfield\registration;

require(__DIR__ . '/../../../config.php');

global $CFG, $OUTPUT, $PAGE;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/moodlelib.php');

// If this feature has been disabled, do nothing.
\tool_brickfield\accessibility::require_accessibility_enabled();

admin_externalpage_setup('tool_brickfield_activation');
$thisurl = new moodle_url(\tool_brickfield\accessibility::get_plugin_url().'/registration.php');
$PAGE->set_url($thisurl);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_title(get_string('registration', manager::PLUGINNAME));
$PAGE->set_heading(get_string('registration', manager::PLUGINNAME));

$registrationform = new registration_form();

echo $OUTPUT->header();

echo html_writer::img($OUTPUT->image_url('brickfield-logo-small', manager::PLUGINNAME), 'logo',
    ['style' => 'display: block; margin: 0 auto; float: right;']);
echo $OUTPUT->heading(get_string('pluginname', manager::PLUGINNAME), 3);

$registration = new registration();
$reginfo = get_string('registrationinfo', manager::PLUGINNAME, $registration->get_termsurl());
echo format_text($reginfo, FORMAT_HTML, ['noclean' => true]);

if ($fromform = $registrationform->get_data()) {
    if (!$registration->set_keys_for_registration($fromform->key, $fromform->hash)) {
        echo $OUTPUT->notification(get_string('hashincorrect', manager::PLUGINNAME), 'notifyproblem');
    }
}

if (!$registration->toolkit_is_active()) {
    echo $OUTPUT->notification(get_string('inactive', manager::PLUGINNAME), 'error');
} else if ($registration->validation_pending()) {
    if ($registration->validation_error()) {
        echo $OUTPUT->notification(get_string('validationerror', manager::PLUGINNAME), 'error');
    } else {
        echo $OUTPUT->notification(get_string('notvalidated', manager::PLUGINNAME), 'warning');
    }
} else {
    echo $OUTPUT->notification(get_string('activated', manager::PLUGINNAME), 'success');
}

$keyinfo = get_string('activationinfo', manager::PLUGINNAME, $registration->get_regurl());
echo format_text($keyinfo, FORMAT_HTML, ['noclean' => true]);

$registrationform->display();
echo $OUTPUT->footer();

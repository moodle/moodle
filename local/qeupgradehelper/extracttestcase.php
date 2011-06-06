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
 * Script to help create unit tests for the upgrade using example data from the
 * database.
 *
 * (The theory is that if the upgrade dies with an error, you can restore the
 * database from backup, and then use this script to extract the problem case
 * as a unit test. Then you can fix that unit tests. Then you can repeat the upgrade.)
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/extracttestcase_form.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir . '/adminlib.php');


require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

admin_externalpage_setup('qeupgradehelper', '', array(),
        local_qeupgradehelper_url('extracttestcase'));
$PAGE->navbar->add(get_string('extracttestcase', 'local_qeupgradehelper'));

$renderer = $PAGE->get_renderer('local_qeupgradehelper');

$mform = new local_qeupgradehelper_extract_options_form(
        new moodle_url('/local/qeupgradehelper/extracttestcase.php'), null, 'get');

echo $OUTPUT->header();
if ($fromform = $mform->get_data()) {
    $qsid = null;
    if (!empty($fromform->attemptid) && !empty($fromform->questionid)) {
        $qsid = local_qeupgradehelper_get_session_id($fromform->attemptid, $fromform->questionid);
        $name = 'qsession' . $qsid;

    } else if (!empty($fromform->statehistory)) {
        notify('Searching ...', 'notifysuccess');
        flush();
        $qsid = local_qeupgradehelper_find_test_case($fromform->behaviour, $fromform->statehistory,
                $fromform->qtype, $fromform->extratests);
        $name = 'history' . $fromform->statehistory;
    }

    if ($qsid) {
        local_qeupgradehelper_generate_unit_test($qsid, $name);
    } else {
        notify('No suitable attempts found.');
    }
}

$mform->display();
echo $OUTPUT->footer();

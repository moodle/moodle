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
 * Script to upgrade the attempts at a particular quiz, after confirmation.
 *
 * @package    tool
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/afterupgradelib.php');
require_once($CFG->libdir . '/adminlib.php');

$quizid = required_param('quizid', PARAM_INT);
$confirmed = optional_param('confirmed', false, PARAM_BOOL);

require_login();
require_capability('moodle/site:config', context_system::instance());
tool_qeupgradehelper_require_upgraded();

admin_externalpage_setup('qeupgradehelper', '', array(),
        tool_qeupgradehelper_url('convertquiz', array('quizid' => $quizid)));
$PAGE->navbar->add(get_string('listtodo', 'tool_qeupgradehelper'),
        tool_qeupgradehelper_url('listtodo'));
$PAGE->navbar->add(get_string('convertattempts', 'tool_qeupgradehelper'));

$renderer = $PAGE->get_renderer('tool_qeupgradehelper');

$quizsummary = tool_qeupgradehelper_get_quiz($quizid);
if (!$quizsummary) {
    print_error('invalidquizid', 'tool_qeupgradehelper',
            tool_qeupgradehelper_url('listtodo'));
}

$quizsummary->name = format_string($quizsummary->name);

if ($confirmed && data_submitted() && confirm_sesskey()) {
    // Actually do the conversion.
    echo $renderer->header();
    echo $renderer->heading(get_string(
            'upgradingquizattempts', 'tool_qeupgradehelper', $quizsummary));

    $upgrader = new tool_qeupgradehelper_attempt_upgrader(
            $quizsummary->id, $quizsummary->numtoconvert);
    $upgrader->convert_all_quiz_attempts();

    echo $renderer->heading(get_string('conversioncomplete', 'tool_qeupgradehelper'));
    echo $renderer->end_of_page_link(
            new moodle_url('/mod/quiz/report.php', array('q' => $quizsummary->id)),
            get_string('gotoquizreport', 'tool_qeupgradehelper'));
    echo $renderer->end_of_page_link(tool_qeupgradehelper_url('listtodo'),
            get_string('listtodo', 'tool_qeupgradehelper'));

    echo $renderer->footer();
    exit;
}

echo $renderer->convert_quiz_are_you_sure($quizsummary);

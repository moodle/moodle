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
 * Script to show all the quizzes in the site with how many attempts they have
 * that will need to be upgraded.
 *
 * @package    tool
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
tool_qeupgradehelper_require_not_upgraded();

admin_externalpage_setup('qeupgradehelper', '', array(), tool_qeupgradehelper_url(''));
$PAGE->navbar->add(get_string('listpreupgrade', 'tool_qeupgradehelper'));

$renderer = $PAGE->get_renderer('tool_qeupgradehelper');

$quizzes = new tool_qeupgradehelper_pre_upgrade_quiz_list();

// Look to see if the admin has set things up to only upgrade certain attempts.
$partialupgradefile = $CFG->dirroot . '/' . $CFG->admin . '/tool/qeupgradehelper/partialupgrade.php';
$partialupgradefunction = 'tool_qeupgradehelper_get_quizzes_to_upgrade';
if (is_readable($partialupgradefile)) {
    include_once($partialupgradefile);
    if (function_exists($partialupgradefunction)) {
        $quizzes = new tool_qeupgradehelper_pre_upgrade_quiz_list_restricted(
                $partialupgradefunction());
    }
}

$numveryoldattemtps = tool_qeupgradehelper_get_num_very_old_attempts();

if ($quizzes->is_empty()) {
    echo $renderer->simple_message_page(get_string('noquizattempts', 'tool_qeupgradehelper'));

} else {
    echo $renderer->quiz_list_page($quizzes, $numveryoldattemtps);
}

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
 * Script to show all the quizzes with attempts that still need to be upgraded
 * after the main upgrade.
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
local_qeupgradehelper_require_upgraded();

admin_externalpage_setup('qeupgradehelper', '', array(),
        local_qeupgradehelper_url('listtodo'));
$PAGE->navbar->add(get_string('listtodo', 'local_qeupgradehelper'));

$renderer = $PAGE->get_renderer('local_qeupgradehelper');

$quizzes = new local_qeupgradehelper_upgradable_quiz_list();

if ($quizzes->is_empty()) {
    echo $renderer->simple_message_page(get_string('alreadydone', 'local_qeupgradehelper'));

} else {
    echo $renderer->quiz_list_page($quizzes);
}

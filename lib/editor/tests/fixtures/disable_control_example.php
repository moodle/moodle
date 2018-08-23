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
 * Demonstrates use of editor with enable/disable function.
 *
 * This fixture is only used by the Behat test.
 *
 * @package core_editor
 * @copyright 2018 Jake Hau <phuchau1509@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../config.php');
require_once('./editor_form.php');

// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

// Require login.
require_login();

$PAGE->set_url('/lib/editor/tests/fixtures/disable_control_example.php');
$PAGE->set_context(context_system::instance());

// Create moodle form.
$mform = new editor_form();

echo $OUTPUT->header();

// Display moodle form.
$mform->display();

echo $OUTPUT->footer();

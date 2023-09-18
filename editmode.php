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
 * Set Mode.
 *
 * @package    core
 * @copyright  2021 Andrew Lyons
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('config.php');

$setmode = optional_param('setmode', false, PARAM_BOOL);
$contextid = required_param('context', PARAM_INT);
$pageurl = required_param('pageurl', PARAM_LOCALURL);

// Behat requires JS to get the session. To allow browserkit
// to enable edit mode sesskey validation is skipped for behat scripts.
if (!defined('BEHAT_SITE_RUNNING')) {
    require_sesskey();
}

require_login();

$context = \context_helper::instance_by_id($contextid);
$PAGE->set_context($context);

$USER->editing = $setmode;

redirect($pageurl);

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
 * Used by ajax calls to toggle the flagged state of a question in an attempt.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('AJAX_SCRIPT', true);

require_once('../config.php');
require_once($CFG->dirroot . '/question/engine/lib.php');

// Parameters
$qaid = required_param('qaid', PARAM_INT);
$qubaid = required_param('qubaid', PARAM_INT);
$questionid = required_param('qid', PARAM_INT);
$slot = required_param('slot', PARAM_INT);
$newstate = required_param('newstate', PARAM_BOOL);
$checksum = required_param('checksum', PARAM_ALPHANUM);

// Check user is logged in.
require_login();
require_sesskey();

// Check that the requested session really exists
question_flags::update_flag($qubaid, $questionid, $qaid, $slot, $checksum, $newstate);

echo 'OK';

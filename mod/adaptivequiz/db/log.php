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
 * Definition of log events for the adaptive quiz module.
 *
 * @package    mod_adaptivequiz
 * @category   log
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module' => 'adaptivequiz', 'action' => 'view', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'add', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'update', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'report', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'attempt', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'submit', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'review', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'start attempt', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'close attempt', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'start attempt', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'continue attempt', 'mtable' => 'adaptivequiz', 'field' => 'name'),
    array('module' => 'adaptivequiz', 'action' => 'start attempt', 'mtable' => 'adaptivequiz', 'field' => 'name'),
);

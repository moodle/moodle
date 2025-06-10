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
 * Definition of log events
 *
 * @package    mod_flashcard
 * @category   log
 * @copyright  213 Valery Fremaux (valery.Fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB; // TODO: this is a hack, we should really do something with the SQL in SQL tables.

$logs = array(
    array('module' => 'flashcard', 'action' => 'view', 'mtable' => 'flashcard', 'field' => 'name'),
    array('module' => 'flashcard', 'action' => 'play', 'mtable' => 'flashcard', 'field' => 'name'),
    array('module' => 'flashcard', 'action' => 'freeplay', 'mtable' => 'flashcard', 'field' => 'name'),
);
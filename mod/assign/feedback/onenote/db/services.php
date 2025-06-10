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
 * Web service for mod assign feedback onenote
 * @package    assignfeedback_onenote
 * @subpackage db
 * @since      Moodle 3.9
 * @copyright  Enovation Solutions Ltd. {@link https://enovation.ie}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_assign_feedback_onenote_delete' => [
        'classname' => 'assignfeedback_onenote_external',
        'methodname' => 'feedback_onenote_delete_foruser',
        'classpath' => 'mod/assign/feedback/onenote/externallib.php',
        'description' => 'Delete a teachers feedback for a student.',
        'type' => 'write',
        'ajax' => true,
    ],
];

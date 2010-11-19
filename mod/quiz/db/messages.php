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
 * Defines message providers (types of messages being sent)
 *
 * @package mod-quiz
 * @copyright  2010 onwards  Andrew Davis  http://moodle.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$messageproviders = array (
    
    // notify teacher that a student has submitted a quiz attempt
    'submission' => array (
        'capability'  => 'mod/quiz:emailnotifysubmission'
    ),
    
    // confirm a student's quiz attempt
    'confirmation' => array (
        'capability'  => 'mod/quiz:emailconfirmsubmission'
    )

);




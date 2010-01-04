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
 * Library of internal functions for module workshop
 *
 * All the workshop specific functions, needed to implement the module 
 * logic, should go to here.
 * 
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return the user's submission record in the given workshop
 *
 * Example submissions are not returned. This is intended to return a submission for given
 * student.
 * 
 * @param int $workshopid Workshop id
 * @param int $userid Owner id
 * @return mixed A fieldset object containing the first matching record or false if not found
 */
function workshop_get_user_submission($workshopid, $userid) {
    global $DB;

    return $DB->get_record('workshop_submissions', array('workshopid' => $workshopid, 'userid' => $userid, 'example' => 0));
}



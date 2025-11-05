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
 * Observer of the user_merged_success to ensure user to keep is not suspended.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2019 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\observer;

use dml_exception;
use tool_mergeusers\event\user_merged_success;

/**
 * Observer of the user_merged_success to ensure user to keep is not suspended.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2019 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class keptuser {
    /**
     * Ensure kept user is not suspended.
     *
     * @param user_merged_success $event Event data.
     * @throws dml_exception
     */
    public static function make_kept_user_as_not_suspended(user_merged_success $event): void {
        global $DB;

        $usertokeep = new \stdClass();
        $usertokeep->id = $event->other['usersinvolved']['toid'];
        $usertokeep->suspended = 0;
        $usertokeep->timemodified = time();
        $DB->update_record('user', $usertokeep);
    }
}

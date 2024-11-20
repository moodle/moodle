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
 * Transformer utility for retrieving a users fullname.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility for retrieving a users fullname.
 *
 * @param \stdClass $user The user object.
 * @return string
 */
function get_full_name(\stdClass $user) {
    $hasfirstname = property_exists($user, 'firstname');
    $haslastname = property_exists($user, 'lastname');

    if ($hasfirstname && $haslastname) {
        return $user->firstname.' '.$user->lastname;
    }
    if ($hasfirstname) {
        return $user->firstname;
    }
    if ($haslastname) {
        return $user->lastname;
    }
    return '';
}

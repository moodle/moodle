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
 * A class containing the data to filter the list of users with attempts by.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\filter;

use mod_adaptivequiz\local\report\users_attempts\user_preferences\filter_user_preferences;

final class filter {

    /**
     * @var int $adaptivequizid
     */
    public $adaptivequizid;

    /**
     * @var int $groupid
     */
    public $groupid;

    /**
     * @var int $users
     */
    public $users;

    /**
     * @var int $includeinactiveenrolments Represents a bool value, as bool values normally come as int (0 or 1)
     * from a request.
     */
    public $includeinactiveenrolments;

    public function fill_from_array(array $request): void {
        foreach ($request as $propertyname => $propertyvalue) {
            if (property_exists($this, $propertyname)) {
                $this->$propertyname = $propertyvalue;
            }
        }
    }

    public function fill_from_preference(filter_user_preferences $filter): void {
        $this->users = $filter->users();
        $this->includeinactiveenrolments = $filter->include_inactive_enrolments();
    }

    public static function from_vars(int $adaptivequizid, int $groupid): self {
        $return = new self();
        $return->adaptivequizid = $adaptivequizid;
        $return->groupid = $groupid;

        return $return;
    }
}

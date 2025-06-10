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
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\user_preferences;

use mod_adaptivequiz\local\report\users_attempts\filter\filter_options;

final class filter_user_preferences {

    /**
     * @var int $users
     */
    private $users;

    /**
     * @var int $includeinactiveenrolments
     */
    private $includeinactiveenrolments;

    private function __construct(int $users, int $includeinactiveenrolments) {
        $this->users = filter_options::users_option_exists($users) ? $users : filter_options::users_option_default();
        $this->includeinactiveenrolments = in_array($includeinactiveenrolments, [0, 1])
            ? $includeinactiveenrolments
            : filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT;
    }

    public function users(): int {
        return $this->users;
    }

    public function include_inactive_enrolments(): int {
        return $this->includeinactiveenrolments;
    }

    public function as_array(): array {
        return ['users' => $this->users, 'includeinactiveenrolments' => $this->includeinactiveenrolments];
    }

    public static function from_array(array $filter): self {
        return new self(
            array_key_exists('users', $filter) ? $filter['users'] : filter_options::users_option_default(),
            array_key_exists('includeinactiveenrolments', $filter)
                ? $filter['includeinactiveenrolments']
                : filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT
        );
    }
}

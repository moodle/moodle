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
 * Emulates an enum type to keep available filtering options. Defines default values for the options as well.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\filter;

final class filter_options {

    public const ENROLLED_USERS_WITH_NO_ATTEMPTS = 1;

    public const ENROLLED_USERS_WITH_ATTEMPTS = 2;

    public const BOTH_ENROLLED_AND_NOT_ENROLLED_USERS_WITH_ATTEMPTS = 3;

    public const NOT_ENROLLED_USERS_WITH_ATTEMPTS = 4;

    public const INCLUDE_INACTIVE_ENROLMENTS_DEFAULT = 1;

    public static function users_option_default(): int {
        return self::BOTH_ENROLLED_AND_NOT_ENROLLED_USERS_WITH_ATTEMPTS;
    }

    public static function users_option_exists(int $option): bool {
        return in_array($option, [
            self::ENROLLED_USERS_WITH_NO_ATTEMPTS,
            self::ENROLLED_USERS_WITH_ATTEMPTS,
            self::BOTH_ENROLLED_AND_NOT_ENROLLED_USERS_WITH_ATTEMPTS,
            self::NOT_ENROLLED_USERS_WITH_ATTEMPTS
        ]);
    }
}

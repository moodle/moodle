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

namespace core_user\hook;

use stdClass;

/**
 * Hook before user information and data updates.
 *
 * @package    core_user
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions before a user is updated.')]
#[\core\attribute\tags('user')]
class before_user_updated {
    /**
     * Constructor for the hook.
     *
     * @param stdClass $user The user instance
     * @param stdClass $currentuserdata The old user instance
     */
    public function __construct(
        /** @var stdClass The user instance */
        public readonly stdClass $user,
        /** @var stdClass The old user instance */
        public readonly stdClass $currentuserdata,
    ) {
    }
}

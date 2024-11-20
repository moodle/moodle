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

namespace core_communication;

/**
 * Constants for communication api.
 *
 * @package    core_communication
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class constants {

    /** @var string GROUP_COMMUNICATION_INSTANCETYPE The group communication instance type. */
    public const GROUP_COMMUNICATION_INSTANCETYPE = 'groupcommunication';

    /** @var string GROUP_COMMUNICATION_COMPONENT The group communication component. */
    public const GROUP_COMMUNICATION_COMPONENT = 'core_group';

    /** @var string COURSE_COMMUNICATION_INSTANCETYPE The course communication instance type. */
    public const COURSE_COMMUNICATION_INSTANCETYPE = 'coursecommunication';

    /** @var string COURSE_COMMUNICATION_COMPONENT The course communication component. */
    public const COURSE_COMMUNICATION_COMPONENT = 'core_course';

    /** @var string COMMUNICATION_STATUS_PENDING The communication status pending. */
    public const COMMUNICATION_STATUS_PENDING = 'pending';

    /** @var string COMMUNICATION_STATUS_READY The communication status sent. */
    public const COMMUNICATION_STATUS_READY = 'ready';
}

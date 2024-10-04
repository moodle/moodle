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

namespace core_enrol\hook;

use stdClass;

/**
 * Hook after enrolment status is changed.
 *
 * @package    core_enrol
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions after the enrolment instance status is changed.')]
#[\core\attribute\tags('enrol')]
class after_enrol_instance_status_updated {
    /**
     * Constructor for the hook.
     *
     * @param stdClass $enrolinstance The enrol instance.
     * @param int $newstatus The new status.
     */
    public function __construct(
        /** @var stdClass The enrol instance */
        public readonly stdClass $enrolinstance,
        /** @var int The new status */
        public readonly int $newstatus,
    ) {
    }
}

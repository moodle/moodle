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
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Hook before enrolment instance is deleted.
 *
 * @package    core_enrol
 * @copyright  20234 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions before the enrolment instance is deleted.')]
#[\core\attribute\tags('enrol', 'user')]
class before_enrol_instance_deleted implements
    StoppableEventInterface
{
    use \core\hook\stoppable_trait;

    /**
     * Constructor for the hook.
     *
     * @param stdClass $enrolinstance The enrol instance.
     */
    public function __construct(
        /** @var stdClass The enrol instance */
        public readonly stdClass $enrolinstance,
    ) {
    }
}

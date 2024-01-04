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
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Hook before user deletion.
 *
 * @package    core_user
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions before a user is deleted.')]
#[\core\attribute\tags('user')]
class before_user_deleted implements
    StoppableEventInterface {

    /**
     * @var bool Whether the propagation of this event has been stopped.
     */
    protected bool $stopped = false;

    /**
     * Constructor for the hook.
     *
     * @param stdClass $user The user instance
     */
    public function __construct(
        public readonly stdClass $user,
    ) {
    }

    public function isPropagationStopped(): bool {
        return $this->stopped;
    }

    /**
     * Stop the propagation of this event.
     */
    public function stop(): void {
        $this->stopped = true;
    }
}

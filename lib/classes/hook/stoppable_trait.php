<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\hook;

/**
 * Moodle specific implementation of
 * \Psr\EventDispatcher\StoppableEventInterface
 * interface from PSR-14.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2023 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait stoppable_trait {
    /** @var bool $propagationstopped propagation is stopped */
    private $propagationstopped = false;

    /**
     * Allows plugins to notify the hook dispatcher that hook propagation should be stopped
     */
    public function stop_propagation(): void {
        $this->propagationstopped = true;
    }

    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     * @return bool True if the Event is complete and no further listeners should be called.
     *              False to continue calling listeners.
     */
    public function isPropagationStopped(): bool {
        return $this->propagationstopped;
    }
}

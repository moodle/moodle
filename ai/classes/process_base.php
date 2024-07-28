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

namespace core_ai;

use core_ai\aiactions\base;
use core_ai\aiactions\responses;

/**
 * Base class for provider processors.
 * Each provider processor should extend this class.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class process_base {
    /**
     * Class constructor.
     *
     * @param provider $provider The provider that will process the action.
     * @param base $action The action to process.
     */
    public function __construct(
        /** @var provider The provider that will process the action. */
        protected provider $provider,
        /** @var base The action to process. */
        protected base $action
    ) {
        $this->provider = $provider;
        $this->action = $action;
    }

    /**
     * Process the AI request.
     *
     * @return responses\response_base The result of the action.
     */
    abstract public function process(): responses\response_base;
}

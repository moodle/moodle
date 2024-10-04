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

namespace core_ai\hook;

use core_ai\provider;
use core\hook\stoppable_trait;

/**
 * Hook before ai provider is disabled.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read provider $provider The provider instance
 */
#[\core\attribute\label('Allows plugins or features to check the usage of an AI provider before disabling the provider.')]
#[\core\attribute\tags('ai')]
class before_provider_disabled implements
        \Psr\EventDispatcher\StoppableEventInterface {

    use stoppable_trait;

    /**
     * Constructor for the hook.
     *
     * @param provider $provider The provider instance.
     */
    public function __construct(
        /** @var provider The provider instance. */
        public readonly provider $provider,
    ) {
    }
}


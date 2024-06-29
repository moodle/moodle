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

namespace core_sms\hook;

use core_sms\gateway;
use core\hook\stoppable_trait;

/**
 * Hook before sms gateway is deleted.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read gateway $gateway The gateway instance
 */
#[\core\attribute\label('Allows plugins or features to check the usage of an SMS gateway before deleting the record.')]
#[\core\attribute\tags('sms')]
class before_gateway_deleted implements
    \Psr\EventDispatcher\StoppableEventInterface {

    use stoppable_trait;

    /**
     * Constructor for the hook.
     *
     * @param gateway $gateway The gateway instance.
     */
    public function __construct(
        public readonly gateway $gateway,
    ) {
    }
}

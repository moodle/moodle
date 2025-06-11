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

namespace tool_mfa\hook;

use core\hook\stoppable_trait;

/**
 * Allow plugins to callback as soon possible after user has passed MFA.
 *
 * @package    tool_mfa
 * @copyright  2024 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allow plugins to callback as soon possible after user has passed MFA.')]
#[\core\attribute\tags('user', 'login')]
class after_user_passed_mfa implements
    \Psr\EventDispatcher\StoppableEventInterface
{
    use stoppable_trait;
}

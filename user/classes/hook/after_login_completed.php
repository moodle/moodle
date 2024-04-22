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

use core\hook\described_hook;
use core\hook\stoppable_trait;

/**
 * Allow plugins to callback as soon possible after user has completed login.
 *
 * @package    core_user
 * @copyright  2024 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_login_completed implements
    described_hook,
    \Psr\EventDispatcher\StoppableEventInterface
{
    use stoppable_trait;
    public static function get_hook_description(): string {
        return 'Allow plugins to callback as soon possible after user has completed login.';
    }

    public static function get_hook_tags(): array {
        return [
            'login',
            'user',
        ];
    }
}

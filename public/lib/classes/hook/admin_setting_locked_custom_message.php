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

namespace core\hook;

/**
 * Hook for providing a custom locked setting message in admin settings.
 * 
 * @package   core
 * @copyright 2026 Abhinav Gandham <abhinavgandham@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('settings')]
#[\core\attribute\label('Allows the customization of locked setting messages for admin settings.')]
final class admin_setting_locked_custom_message {

    /** @var null|string $custom_message The custom message to display when a setting is locked. */
    public ?string $custom_message = null;

    public function __construct(
        /** @var string $name The name of the setting. */
        public string $name,

        /** @var string $component The component the setting belongs to. */
        public string $component,

        /** @var mixed $value The current value of the setting. */
        public mixed $value,
    ) {}
}
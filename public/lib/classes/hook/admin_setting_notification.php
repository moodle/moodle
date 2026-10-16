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

use core\output\notification;

/**
 * Hook for providing a custom message for an admin setting.
 *
 * @package   core
 * @author    Abhinav Gandham <abhinavgandham@catalyst-au.net>
 * @copyright 2026 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('settings')]
#[\core\attribute\label('Displays a notification for the setting being targeted.')]
final class admin_setting_notification {
    /** @var array $notifications to be displayed. */
    private array $notifications = [];

    /**
     * Constructor for the hook.
     *
     * @param object $setting The admin setting object that is being processed.
     */
    public function __construct(
        /** @var object The admin setting object.*/
        public object $setting,
    ) {
    }

    /**
     * Returns the notifications to be displayed.
     *
     * @return array The notifications to be displayed.
     */
    public function get_notifications(): array {
        return $this->notifications;
    }

    /**
     * Adds a notification to be displayed.
     *
     * @param string $message The notification message.
     * @param string $messagetype The type of notification message (e.g., \core\output\notification::NOTIFY_SUCCESS,
     * \core\output\notification::NOTIFY_ERROR, etc.).
     *
     * @return void
     */
    public function add_notification(string $message, string $messagetype): void {
        $this->notifications[] = new notification($message, $messagetype);
    }
}

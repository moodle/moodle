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

namespace core_admin\admin;

use admin_setting;

/**
 * Render a notification as part of other admin settings.
 *
 * @package    core_admin
 * @subpackage admin
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_notification extends admin_setting {
    /**
     * Constructor.
     *
     * @param string $name The name of the setting.
     * @param string $notification The notification to display.
     * @param string $type The type of notification.
     * @param bool $cancelable Whether the notification can be canceled.
     */
    public function __construct(
        string $name,
        /** @var string The notification to display. */
        protected string $notification,
        /** @var string The type of notification. */
        protected string $type = 'info',
        /** @var bool Whether the notification can be canceled. */
        protected bool $cancelable = false
    ) {
        $this->nosave = true;

        parent::__construct($name, '', '', '');
    }

    #[\Override]
    public function get_setting(): bool {
        return true;
    }

    #[\Override]
    public function get_defaultsetting(): bool {
        return true;
    }

    #[\Override]
    public function write_setting($data): string {
        // Do not write any setting.
        return '';
    }

    #[\Override]
    public function output_html($data, $query = ''): string {
        global $OUTPUT;

        return $OUTPUT->notification($this->notification, $this->type, $this->cancelable);
    }
}

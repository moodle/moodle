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

/**
 * Settings for the Tiny Premium plugin.
 *
 * @package     tiny_premium
 * @category    admin
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    if ($ADMIN->fulltree) {
        // Set API key.
        $setting = new admin_setting_configpasswordunmask(
            'tiny_premium/apikey',
            get_string('apikey', 'tiny_premium'),
            get_string('apikey_desc', 'tiny_premium'),
            '',
        );
        $settings->add($setting);

        // Set individual Tiny Premium plugins.
        $settings->add(new \tiny_premium\local\admin_setting_tiny_premium_plugins());
    }
}

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
 * File for managing and overriding default Moodle core Font Awesome icons.
 *
 * @package     theme_snap
 * @copyright   Copyright (c) 2025 Open LMS (https://www.openlms.net)
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap\output;

/**
 * Font Awesome class for overriding default Moodle core icons.
 *
 * @package     theme_snap
 * @copyright   Copyright (c) 2025 Open LMS (https://www.openlms.net)
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_system_fontawesome extends \core\output\icon_system_fontawesome {

    /**
     * Retrieves the Moodle core icon mapping and allows overriding of custom icons.
     *
     * @return array Array containing the custom icons.
     */
    public function get_core_icon_map(): array {
        $iconmap = parent::get_core_icon_map();
        $iconmap['core:i/notifications'] = 'fa-bell-o';
        $iconmap['core:a/search'] = 'fa-search';
        return $iconmap;
    }
}

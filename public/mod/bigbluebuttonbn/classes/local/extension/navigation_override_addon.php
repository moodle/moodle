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

namespace mod_bigbluebuttonbn\local\extension;

/**
 * Interface for overriding the settings navigation in BigBlueButtonBN subplugins.
 *
 * Implement this interface in a subplugin if you want to override the settings navigation.
 * Only the first subplugin implementing this will be called, and it will replace the default logic.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2025 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
interface navigation_override_addon {
    /**
     * Overrides the settings navigation.
     *
     * @param \settings_navigation $settingsnav
     * @param \navigation_node $nodenav
     * @return void
     */
    public function override_settings_navigation(\settings_navigation $settingsnav, \navigation_node $nodenav): void;
}

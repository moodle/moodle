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

/**
 * Fixture callback for the add_setting_before_merging hook.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\fixtures;

use tool_mergeusers\hook\add_settings_before_merging;

/**
 * Callback implementation for testing the add_setting_before_merging hook.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class add_settings_before_merging_callbacks {
    /** @var string name of the gathering populated by the hook. */
    public static string $gatheringname = 'hookgathering';

    /**
     * Specifies a gathering implementation for the merge users tool.
     *
     * @param add_settings_before_merging $hook
     * @return void
     */
    public static function add_settings_before_merging(
        add_settings_before_merging $hook,
    ): void {
        $hook->add_setting('gathering', self::$gatheringname);
    }
}

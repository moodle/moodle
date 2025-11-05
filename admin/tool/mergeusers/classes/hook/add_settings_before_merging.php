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
 * Provides the hook to load database configuration settings before merging users.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\hook;

use core\attribute\label;
use core\attribute\tags;
use tool_mergeusers\local\db_config;

/**
 * Provides the hook to load database configuration settings before merging users.
 *
 * The order of the hook callbacks will define the final result of the populated settings.
 * \core\hook\manager processes hooks first the one with the highest priority, and
 * then the following one until the one with the lowest priority.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[label('Populate database configuration settings before merging users')]
#[tags('tool_mergeusers', 'database_settings')]
final class add_settings_before_merging {
    /** @var db_config list of settings loaded from hooks. */
    private db_config $config;

    /**
     * Initializes the hook.
     */
    public function __construct() {
        $this->config = new db_config();
    }

    /**
     * Adds a single setting into the set of settings.
     *
     * @param string $name setting name from the root.
     * @param mixed $value any value (string, bool, array) as expected for the plugin.
     * @return void
     */
    public function add_setting(string $name, mixed $value): void {
        $this->config->add($name, $value);
    }

    /**
     * Adds a set of settings, all at once, into the set of settings.
     *
     * These $settings are provided by a hook callback which has the same
     * or lower priority than the already called ones.
     *
     * So, new settings are replaced, if matched, with already loaded,
     * since they have more priority.
     *
     * @param array $settings
     * @return void
     */
    public function add_raw_settings(array $settings): void {
        $this->config->add_raw($settings);
    }

    /**
     * Returns the populated database-related settings.
     *
     * @return db_config
     */
    public function get_settings(): db_config {
        return $this->config;
    }
}

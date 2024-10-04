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

namespace qbank_columnsortorder\event;

use core\event\qbank_plugin_disabled;
use core\event\qbank_plugin_enabled;
use qbank_columnsortorder\column_manager;

/**
 * Observer for qbank plugin enabled/disabled events
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_observer {

    /**
     * When a plugin is enabled, enable its columns.
     *
     * @param qbank_plugin_enabled $event
     * @return void
     */
    public static function plugin_enabled(qbank_plugin_enabled $event): void {
        (new column_manager())->enable_columns($event->other['pluginname']);
    }

    /**
     * When a plugin is disabled, disable its columns.
     *
     * @param qbank_plugin_disabled $event
     * @return void
     */
    public static function plugin_disabled(qbank_plugin_disabled $event): void {
        (new column_manager())->disable_columns($event->other['pluginname']);
    }
}

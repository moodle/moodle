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

namespace core\event;

/**
 * Question bank plugin event.
 *
 * This describes an administrative event relating to a question bank plugin, in the system context.
 * The pluginname will be stored in the other property, and userid will be the user who performed the action on the plugin.
 *
 * @package   core
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qbank_plugin_base extends base {

    protected function init() {
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['crud'] = 'u';
        $this->context = \context_system::instance();
    }

    protected function validate_data() {
        if (!str_starts_with($this->data['other']['pluginname'], 'qbank_')) {
            throw new \coding_exception('You must provide the full frankenstyle name of a qbank plugin (e.g. qbank_usage)');
        }
    }

    /**
     * Return an event instance with $this->other['pluginname'] set to the provided plugin name.
     *
     * @param string $pluginname
     * @return base
     * @throws \coding_exception
     */
    public static function create_for_plugin(string $pluginname): base {
        return self::create([
            'other' => ['pluginname' => $pluginname],
        ]);
    }
}

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
 * Class after_config
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('configuration', 'core')]
#[\core\attribute\label('Allows plugins to perform actions immediately after configuration')]
#[\core\attribute\hook\replaces_callbacks('after_config')]
class after_config {
    /**
     * Process legacy callbacks.
     */
    public function process_legacy_callbacks(): void {
        $pluginswithfunction = get_plugins_with_function(
            function: 'after_config',
            migratedtohook: true,
        );
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                try {
                    $function();
                } catch (\Throwable $e) {
                    debugging("Exception calling '$function'", DEBUG_DEVELOPER, $e->getTrace());
                }
            }
        }

    }
}

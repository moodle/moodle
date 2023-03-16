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

namespace core_xapi;

/**
 * The xAPI internal API.
 *
 * @package    core_xapi
 * @copyright  2023 Ferran Recio
 * @since      Moodle 4.2
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {
    /**
     * Delete all states from a component.
     *
     * @param string $component The component name in frankenstyle.
     * @return void
     */
    public static function remove_states_from_component(string $component): void {
        global $DB;

        $statestore = null;
        $dbman = $DB->get_manager();
        try {
            $handler = handler::create($component);
            $statestore = $handler->get_state_store();
        } catch (xapi_exception $exception) {
            // If the component is not available but the xapi_states table exists, use the standard one to ensure we clean it.
            $table = new \xmldb_table('xapi_states');
            if ($dbman->table_exists($table)) {
                $statestore = new state_store($component);
            }
        }
        if ($statestore) {
            $statestore->wipe();
        }
    }

    /**
     * Execute the states clean up for all compatible components.
     *
     * @return void
     */
    public static function execute_state_cleanup(): void {
        foreach (\core_component::get_plugin_types() as $ptype => $unused) {
            $components = \core_component::get_plugin_list_with_class($ptype, 'xapi\handler');
            foreach ($components as $component => $unused) {
                $handler = handler::create($component);
                $statestore = $handler->get_state_store();
                $statestore->cleanup();
            }
        }
    }
}

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

namespace local_ai_manager\local;

use core\hook\navigation\primary_extend;
use moodle_url;
use navigation_node;

/**
 * Hook listener callbacks.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {

    /**
     * Hook callback function to extend the primary navigation.
     *
     * @param primary_extend $hook the primary_extend hook object
     */
    public static function extend_primary_navigation(primary_extend $hook): void {
        if (empty(get_config('local_ai_manager', 'addnavigationentry'))) {
            return;
        }
        global $USER;
        $accessmanager = \core\di::get(access_manager::class);
        $tenant = \core\di::get(tenant::class);
        if (!$accessmanager->is_tenant_manager() || !$tenant->is_tenant_allowed()) {
            return;
        }
        $node = navigation_node::create(get_string('aiadministrationlink', 'local_ai_manager'),
                new moodle_url('/local/ai_manager/tenant_config.php'));
        $hook->get_primaryview()->add_node($node);
    }
}

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

use moodle_url;

/**
 * Utils class for aggregating code to avoid duplication.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tenant_config_output_utils {

    /**
     * Helper function which is being called from every tenant config (sub)page.
     *
     * Important to control access and set important basic settings which are identical for all the pages.
     *
     * @param moodle_url $url the moodle url object of the current page
     */
    public static function setup_tenant_config_page(moodle_url $url): void {
        global $PAGE;
        $tenantid = optional_param('tenant', '', PARAM_ALPHANUM);

        if (!empty($tenantid)) {
            $tenant = new \local_ai_manager\local\tenant($tenantid);
            \core\di::set(\local_ai_manager\local\tenant::class, $tenant);
        }
        $tenant = \core\di::get(\local_ai_manager\local\tenant::class);
        $accessmanager = \core\di::get(\local_ai_manager\local\access_manager::class);
        $accessmanager->require_tenant_manager();

        $url->param('tenant', $tenant->get_identifier());
        $PAGE->set_url($url);
        $PAGE->set_context($tenant->get_context());
        $PAGE->set_pagelayout('admin');

        $strtitle = get_string('tenantconfig_heading', 'local_ai_manager');
        $strtitle .= ' (' . $tenant->get_identifier() . ')';
        $PAGE->set_title($strtitle);
        $PAGE->set_heading($strtitle);
        $PAGE->navbar->add($strtitle);
        $PAGE->set_secondary_navigation(false);

        if (!$tenant->is_tenant_allowed()) {
            throw new \moodle_exception('exception_tenantnotallowed', 'local_ai_manager');
        }
    }
}

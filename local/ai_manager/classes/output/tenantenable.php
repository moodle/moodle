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

namespace local_ai_manager\output;

use html_writer;
use local_ai_manager\local\config_manager;
use local_ai_manager\local\tenant;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;

/**
 * Tenant enable switch widget shown on the tenant_config.php page.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tenantenable implements renderable, \templatable {

    #[\Override]
    public function export_for_template(renderer_base $output): stdClass {
        $tenant = \core\di::get(tenant::class);
        $configmanager = \core\di::get(config_manager::class);
        $istenantenabled = $configmanager->is_tenant_enabled();

        $rightsconfiglink = html_writer::link(new moodle_url('/local/ai_manager/rights_config.php'),
                get_string('rightsconfig', 'local_ai_manager'));

        return (object) [
                'checked' => $istenantenabled,
                'text' => $istenantenabled ? get_string('tenantenabled', 'local_ai_manager') :
                        get_string('tenantdisabled', 'local_ai_manager'),
                'targetwhenchecked' => (new moodle_url('/local/ai_manager/tenant_config.php',
                        ['tenant' => $tenant->get_identifier(), 'enabletenant' => 0]))->out(false),
                'targetwhennotchecked' => (new moodle_url('/local/ai_manager/tenant_config.php',
                        ['tenant' => $tenant->get_identifier(), 'enabletenant' => 1]))->out(false),
                'tenantfullname' => $tenant->get_fullname(),
                'rightsconfiglink' => $rightsconfiglink,
        ];
    }
}

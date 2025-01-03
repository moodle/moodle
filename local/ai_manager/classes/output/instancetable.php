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
use local_ai_manager\base_instance;
use local_ai_manager\local\config_manager;
use local_ai_manager\local\tenant;
use local_ai_manager\local\userinfo;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Instance table widget shown on the tenant_config.php page.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class instancetable implements renderable, templatable {

    #[\Override]
    public function export_for_template(renderer_base $output): stdClass {
        $tenant = \core\di::get(tenant::class);
        $configmanager = \core\di::get(config_manager::class);

        $instances = [];

        // Purposes with role basic.
        $purposeconfigrolebasic = $configmanager->get_purpose_config(userinfo::ROLE_BASIC);
        $purposeswithtoolrolebasic = [];
        foreach ($purposeconfigrolebasic as $purpose => $instanceid) {
            if (!is_null($instanceid)) {
                $purposeswithtoolrolebasic[] = $purpose;
            }
        }
        $purposesheadingrolebasic = get_string('purposesheading', 'local_ai_manager', [
                'role' => get_string('role_basic', 'local_ai_manager'),
                'currentcount' => count($purposeswithtoolrolebasic),
                'maxcount' => count($purposeconfigrolebasic),
        ]);

        // Purposes with role extended.
        $purposeconfigroleextended = $configmanager->get_purpose_config(userinfo::ROLE_EXTENDED);
        $purposeswithtoolroleextended = [];
        foreach ($purposeconfigroleextended as $purpose => $instanceid) {
            if (!is_null($instanceid)) {
                $purposeswithtoolroleextended[] = $purpose;
            }
        }
        $purposesheadingroleextended = get_string('purposesheading', 'local_ai_manager', [
                'role' => get_string('role_extended', 'local_ai_manager'),
                'currentcount' => count($purposeswithtoolroleextended),
                'maxcount' => count($purposeconfigroleextended),
        ]);

        foreach (base_instance::get_all_instances() as $instance) {
            $purposesrolebasic = [];
            foreach ($purposeconfigrolebasic as $purpose => $instanceid) {
                if (intval($instanceid) === $instance->get_id()) {
                    $purposesrolebasic[] = ['fullname' => get_string('pluginname', 'aipurpose_' . $purpose)];
                }
            }
            $purposesroleextended = [];
            foreach ($purposeconfigroleextended as $purpose => $instanceid) {
                if (intval($instanceid) === $instance->get_id()) {
                    $purposesroleextended[] = ['fullname' => get_string('pluginname', 'aipurpose_' . $purpose)];
                }
            }
            $linkedname = html_writer::link(new moodle_url('/local/ai_manager/edit_instance.php',
                    ['id' => $instance->get_id(), 'tenant' => $tenant->get_identifier()]), $instance->get_name());

            $instances[] = [
                    'name' => $linkedname,
                    'toolname' => get_string('pluginname', 'aitool_' . $instance->get_connector()),
                    'model' => $instance->get_model() === base_instance::PRECONFIGURED_MODEL
                            ? get_string('preconfiguredmodel', 'local_ai_manager')
                            : $instance->get_model(),
                    'purposesrolebasic' => $purposesrolebasic,
                    'purposesroleextended' => $purposesroleextended,
                    'nopurposeslink' => html_writer::link(new moodle_url('/local/ai_manager/purpose_config.php',
                            ['tenant' => $tenant->get_identifier()]),
                            '<i class="fa fa-arrow-right"></i> ' . get_string('assignpurposes', 'local_ai_manager')),
            ];
        }
        return (object) [
                'tenant' => $tenant->get_identifier(),
                'purposesheadingrolebasic' => $purposesheadingrolebasic,
                'purposesheadingroleextended' => $purposesheadingroleextended,
                'instances' => $instances,
        ];
    }
}

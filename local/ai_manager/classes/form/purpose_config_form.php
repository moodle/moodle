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

namespace local_ai_manager\form;

use local_ai_manager\base_purpose;
use local_ai_manager\local\connector_factory;
use local_ai_manager\local\tenant;
use local_ai_manager\local\userinfo;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Purpose config form.
 *
 * This form enables a tenant manager to select the AI tools for the existing purposes.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose_config_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $tenant = \core\di::get(tenant::class);
        $mform = &$this->_form;

        $mform->addElement('hidden', 'tenant', $tenant->get_identifier());
        $mform->setType('tenant', PARAM_ALPHANUM);

        foreach (base_purpose::get_all_purposes() as $purpose) {
            $factory = \core\di::get(connector_factory::class);
            $instances = $factory::get_connector_instances_for_purpose($purpose);
            $instances = array_map(fn ($instance) => $instance->get_name(), $instances);
            $instances[0] = get_string('notselected', 'local_ai_manager');

            $mform->addElement(
                    'header',
                    'purpose_config_purpose_' . $purpose . '_header',
                    get_string('select_tool_for_purpose', 'local_ai_manager',
                            get_string('pluginname', 'aipurpose_' . $purpose))
            );

            $mform->addElement(
                'select',
                base_purpose::get_purpose_tool_config_key($purpose, userinfo::ROLE_BASIC),
                get_string('role_basic', 'local_ai_manager'),
                $instances,
            );
            $mform->setDefault('purpose_' . $purpose . '_tool_role_basic', 0);
            $mform->addElement(
                    'select',
                    base_purpose::get_purpose_tool_config_key($purpose, userinfo::ROLE_EXTENDED),
                    get_string('role_extended', 'local_ai_manager'),
                    $instances
            );
            $mform->setDefault('purpose_' . $purpose . '_tool_role_extended', 0);
        }
        $this->add_action_buttons();
    }
}

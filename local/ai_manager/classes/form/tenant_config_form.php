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

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * A form for configuring tenant configurations.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tenant_config_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $tenant = $this->_customdata['tenant'];

        $mform = &$this->_form;

        $mform->addElement('hidden', 'tenant', $tenant);
        $mform->setType('tenant', PARAM_ALPHANUM);

        $mform->addElement('selectyesno', 'tenantenabled', get_string('enable_ai_integration', 'local_ai_manager'));

        $this->add_action_buttons();
    }
}

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
use local_ai_manager\local\userusage;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Quota config form.
 *
 * This form gathers information for configuring user specific configurations for local_ai_manager.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quota_config_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $tenant = $this->_customdata['tenant'];

        $mform = &$this->_form;

        $mform->addElement('hidden', 'tenant', $tenant);
        $mform->setType('tenant', PARAM_ALPHANUM);

        $mform->addElement(
                'header',
                'general_user_config_settings_header',
                get_string('general_user_settings', 'local_ai_manager')
        );

        $mform->addElement(
                'duration',
                'max_requests_period',
                get_string('max_request_time_window', 'local_ai_manager'),
                ['units' => [HOURSECS, DAYSECS, WEEKSECS]]
        );
        $mform->setType('max_requests_period', PARAM_INT);
        $mform->setDefault('max_requests_period', userusage::MAX_REQUESTS_DEFAULT_PERIOD);

        foreach (base_purpose::get_all_purposes() as $purpose) {
            $mform->addElement(
                    'header',
                    $purpose . '_purpose_config_header',
                    get_string('max_requests_purpose_heading', 'local_ai_manager',
                            get_string('pluginname', 'aipurpose_' . $purpose))
            );
            $mform->addElement(
                    'text',
                    $purpose . '_max_requests_basic',
                    get_string('max_requests_purpose', 'local_ai_manager', get_string('role_basic', 'local_ai_manager'))
            );
            $mform->setType($purpose . '_max_requests_basic', PARAM_INT);
            $mform->setDefault($purpose . '_max_requests_basic', userusage::MAX_REQUESTS_DEFAULT_ROLE_BASE);

            $mform->addElement(
                    'text',
                    $purpose . '_max_requests_extended',
                    get_string('max_requests_purpose', 'local_ai_manager', get_string('role_extended', 'local_ai_manager'))
            );
            $mform->setType($purpose . '_max_requests_extended', PARAM_INT);
            $mform->setDefault($purpose . '_max_requests_extended', userusage::MAX_REQUESTS_DEFAULT_ROLE_EXTENDED);
        }

        $this->add_action_buttons();
    }

    /**
     * Some extra validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files): array {
        $errors = [];
        if (isset($data['max_requests_period']) && intval($data['max_requests_period']) < userusage::MAX_REQUESTS_MIN_PERIOD) {
            $errors['max_requests_period'] = get_string('error_max_requests_period', 'local_ai_manager');
        }
        return $errors;
    }
}

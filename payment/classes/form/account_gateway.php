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

/**
 * Class account_gateway
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment\form;

use core\form\persistent;

/**
 * Class account_gateway
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class account_gateway extends persistent {

    /** @var string The persistent class. */
    protected static $persistentclass = \core_payment\account_gateway::class;

    protected static $fieldstoremove = ['accountname', 'gatewayname', 'submitbutton'];

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'accountid');
        $mform->addElement('hidden', 'gateway');

        $mform->addElement('static', 'accountname', get_string('accountname', 'payment'),
            $this->get_gateway_persistent()->get_account()->get_formatted_name());

        $mform->addElement('static', 'gatewayname', get_string('type_paygw', 'plugin'),
            $this->get_gateway_persistent()->get_display_name());

        $mform->addElement('advcheckbox', 'enabled', get_string('enable'));

        /** @var \core_payment\gateway $classname */
        $classname = '\paygw_' . $this->get_gateway_persistent()->get('gateway') . '\gateway';
        if (class_exists($classname)) {
            $classname::add_configuration_to_gateway_form($this);
        }

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param \stdClass $data
     * @param array $files
     * @param array $errors
     */
    protected function extra_validation($data, $files, array &$errors) {
        /** @var \core_payment\gateway $classname */
        $classname = '\paygw_' . $this->get_gateway_persistent()->get('gateway') . '\gateway';
        if (class_exists($classname)) {
            $classname::validate_gateway_form($this, $data, $files, $errors);
        }
    }

    /**
     * Exposes the protected attribute to be accessed by the \core_payment\gateway callback
     *
     * @return \MoodleQuickForm
     */
    public function get_mform(): \MoodleQuickForm {
        return $this->_form;
    }

    /**
     * Exposes the protected attribute to be accessed by the \core_payment\gateway callback
     *
     * @return \core_payment\account_gateway
     */
    public function get_gateway_persistent(): \core_payment\account_gateway {
        return $this->get_persistent();
    }

    /**
     * Filter out the foreign fields of the persistent.
     *
     * This can be overridden to filter out more complex fields.
     *
     * @param \stdClass $data The data to filter the fields out of.
     * @return \stdClass.
     */
    protected function filter_data_for_persistent($data) {
        $data = parent::filter_data_for_persistent($data);
        return (object) array_intersect_key((array)$data, \core_payment\account_gateway::properties_definition());
    }

    /**
     * Overwrite parent method to json encode config
     *
     * @return object|\stdClass|null
     * @throws \coding_exception
     */
    public function get_data() {
        if (!$data = parent::get_data()) {
            return $data;
        }
        // Everything that is not a property of the account_gateway class is a gateway config.
        $data = (array)$data;
        $properties = \core_payment\account_gateway::properties_definition() + ['id' => 1];
        $config = array_diff_key($data, $properties, ['timemodified' => 1, 'timecreated' => 1]);
        $data = array_intersect_key($data, $properties);
        $data['config'] = json_encode($config);
        return (object)$data;
    }

    /**
     * Overwrite parent method to json decode config
     *
     * @param array|\stdClass $values
     */
    public function set_data($values) {
        if (($config = isset($values->config) ? @json_decode($values->config, true) : null) && is_array($config)) {
            $values = (object)((array)$values + $config);
        }
        unset($values->config);
        parent::set_data($values);
    }
}

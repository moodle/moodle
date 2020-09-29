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

namespace core_payment;

use core\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Class account_gateway
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class account_gateway extends persistent {
    /**
     * Database table.
     */
    const TABLE = 'payment_gateways';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() : array {
        return array(
            'accountid' => [
                'type' => PARAM_INT,
            ],
            'gateway' => [
                'type' => PARAM_COMPONENT,
                // TODO select with options?
            ],
            'enabled' => [
                'type' => PARAM_BOOL,
                'default' => true
            ],
            'config' => [
                'type' => PARAM_RAW,
                'optional' => true,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
        );
    }

    /**
     * Return the gateway name ready for display
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_display_name(): string {
        return get_string('pluginname', 'pg_' . $this->get('gateway'));
    }

    /**
     * Gateway management url
     *
     * @return \moodle_url
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function get_edit_url(): \moodle_url {
        $params = $this->get('id') ? ['id' => $this->get('id')] :
            ['accountid' => $this->get('accountid'), 'gateway' => $this->get('gateway')];
        return new \moodle_url('/payment/manage_gateway.php', $params);
    }

    /**
     * Get corresponding account
     *
     * @return account
     * @throws \coding_exception
     */
    public function get_account(): account {
        return new account($this->get('accountid'));
    }

    /**
     * Parse configuration from the json-encoded stored value
     *
     * @return array
     * @throws \coding_exception
     */
    public function get_configuration(): array {
        $config = @json_decode($this->get('config'), true);
        return ($config && is_array($config)) ? $config : [];
    }
}

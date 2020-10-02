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
 * Class account
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment;

use core\persistent;

/**
 * Class account
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class account extends persistent {
    /**
     * Database table.
     */
    const TABLE = 'payment_accounts';

    /** @var array */
    protected $gateways;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() : array {
        return array(
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'idnumber' => [
                'type' => PARAM_RAW_TRIMMED,
            ],
            'contextid' => [
                'type' => PARAM_INT,
                'default' => function() {
                    return \context_system::instance()->id;
                }
            ],
            'enabled' => [
                'type' => PARAM_BOOL,
                'default' => true
            ],
            'archived' => [
                'type' => PARAM_BOOL,
                'default' => false
            ],
        );
    }

    /**
     * Account context
     *
     * @return \context
     * @throws \coding_exception
     */
    public function get_context(): \context {
        return \context::instance_by_id($this->get('contextid'));
    }

    /**
     * Account name ready for display
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_formatted_name(): string {
        return format_string($this->get('name'), true, ['context' => $this->get_context(), 'escape' => false]);
    }

    /**
     * Manage account url
     *
     * @param array $extraparams
     * @return \moodle_url
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function get_edit_url(array $extraparams = []): \moodle_url {
        return new \moodle_url('/payment/manage_account.php',
            ($this->get('id') ? ['id' => $this->get('id')] : []) + $extraparams);
    }

    /**
     * List of gateways configured (or possible) for this account
     *
     * @param bool $enabledpluginsonly only return payment plugins that are enabled
     * @return account_gateway[]
     * @throws \coding_exception
     */
    public function get_gateways(bool $enabledpluginsonly = true): array {
        $id = $this->get('id');
        if (!$id) {
            return [];
        }
        if ($this->gateways === null) {
            \core_component::get_plugin_list('pg');
            $this->gateways = [];
            foreach (\core_component::get_plugin_list('pg') as $gatewayname => $unused) {
                $gateway = account_gateway::get_record(['accountid' => $id, 'gateway' => $gatewayname]);
                if (!$gateway) {
                    $gateway = new account_gateway(0, (object)['accountid' => $id, 'gateway' => $gatewayname,
                        'enabled' => false, 'config' => null]);
                }
                $this->gateways[$gatewayname] = $gateway;
            }
        }
        if ($enabledpluginsonly) {
            $enabledplugins = \core\plugininfo\pg::get_enabled_plugins();
            return array_intersect_key($this->gateways, $enabledplugins);
        }
        return $this->gateways;
    }

    /**
     * Is this account available (used in management interface)
     *
     * @return bool
     * @throws \coding_exception
     */
    public function is_available(): bool {
        if (!$this->get('id') || !$this->get('enabled')) {
            return false;
        }
        foreach ($this->get_gateways() as $gateway) {
            if ($gateway->get('id') && $gateway->get('enabled')) {
                return true;
            }
        }
        return false;
    }
}

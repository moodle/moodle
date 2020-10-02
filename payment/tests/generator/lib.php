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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz module test data generator class
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_payment_generator extends component_generator_base {

    protected $accountcounter = 0;

    /**
     * Create a payment account
     *
     * @param array $data account data (name, idnumber, enabled) and additionally field 'gateways' that can include
     *    a list of gateways that should be mock-enabled for this account.
     */
    public function create_payment_account(array $data): \core_payment\account {
        $this->accountcounter++;
        $gateways = [];
        if (!empty($data['gateways'])) {
            $gateways = preg_split('/,/', $data['gateways']);
        }
        unset($data['gateways']);
        $account = \core_payment\helper::save_payment_account(
            (object)($data + ['name' => 'Test '.$this->accountcounter, 'idnumber' => '', 'enabled' => 1]));
        foreach ($gateways as $gateway) {
            \core_payment\helper::save_payment_gateway(
                (object)['accountid' => $account->get('id'), 'gateway' => $gateway, 'enabled' => 1]);
        }
        return $account;
    }

    /**
     * Create a payment account
     *
     * @param array $data
     */
    public function create_payment(array $data): int {
        global $DB;
        if (empty($data['accountid']) || !\core_payment\account::get_record(['id' => $data['accountid']])) {
            throw new coding_exception('Account id is not specified or does not exist');
        }

        if (empty($data['amount'])) {
            throw new coding_exception('Amount must be specified');
        }

        $gateways = \core\plugininfo\pg::get_enabled_plugins();
        if (empty($data['gateway'])) {
            $data['gateway'] = reset($gateways);
        }

        $id = $DB->insert_record('payments', $data +
            ['component' => 'testcomponent',
                'componentarea' => 'teatarea',
                'componentid' => 0,
                'currency' => 'AUD']);
        return $id;
    }

}

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
 * Testing helper class methods in payments API
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment;

use advanced_testcase;
use core\plugininfo\paygw;

/**
 * Testing helper class methods in payments API
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accounts_testcase extends advanced_testcase {

    protected function enable_paypal_gateway(): bool {
        if (!array_key_exists('paypal', \core_component::get_plugin_list('paygw'))) {
            return false;
        }
        return true;
    }

    public function test_create_account() {
        global $DB;
        $this->resetAfterTest();

        $account = helper::save_payment_account((object)['name' => 'Test 1', 'idnumber' => '']);
        $this->assertNotEmpty($account->get('id'));
        $this->assertEquals('Test 1', $DB->get_field('payment_accounts', 'name', ['id' => $account->get('id')]));
    }

    public function test_update_account_details() {
        global $DB;
        $this->resetAfterTest();

        $account = helper::save_payment_account((object)['name' => 'Test 1', 'idnumber' => '']);
        $record = $account->to_record();
        $record->name = 'Edited name';
        $editedaccount = helper::save_payment_account($record);
        $this->assertEquals($account->get('id'), $editedaccount->get('id'));
        $this->assertEquals('Edited name', $DB->get_field('payment_accounts', 'name', ['id' => $account->get('id')]));
    }

    public function test_update_account_gateways() {
        global $DB;
        if (!$this->enable_paypal_gateway()) {
            $this->markTestSkipped('Paypal payment gateway plugin not found');
        }

        $this->resetAfterTest();

        $account = helper::save_payment_account((object)['name' => 'Test 1', 'idnumber' => '']);
        $gateway = helper::save_payment_gateway(
            (object)['accountid' => $account->get('id'), 'gateway' => 'paypal', 'config' => 'T1']);
        $this->assertNotEmpty($gateway->get('id'));
        $this->assertEquals('T1', $DB->get_field('payment_gateways', 'config', ['id' => $gateway->get('id')]));

        // Update by id.
        $editedgateway = helper::save_payment_gateway(
            (object)['id' => $gateway->get('id'), 'accountid' => $account->get('id'), 'gateway' => 'paypal', 'config' => 'T2']);
        $this->assertEquals($gateway->get('id'), $editedgateway->get('id'));
        $this->assertEquals('T2', $DB->get_field('payment_gateways', 'config', ['id' => $gateway->get('id')]));

        // Update by account/gateway.
        $editedgateway = helper::save_payment_gateway(
            (object)['accountid' => $account->get('id'), 'gateway' => 'paypal', 'config' => 'T3']);
        $this->assertEquals($gateway->get('id'), $editedgateway->get('id'));
        $this->assertEquals('T3', $DB->get_field('payment_gateways', 'config', ['id' => $gateway->get('id')]));
    }

    public function test_delete_account() {
        global $DB;
        if (!$this->enable_paypal_gateway()) {
            $this->markTestSkipped('Paypal payment gateway plugin not found');
        }
        $this->resetAfterTest();

        // Delete account without payments, it will be deleted, gateways will also be deleted.
        $account = helper::save_payment_account((object)['name' => 'Test 1', 'idnumber' => '']);
        $gateway = helper::save_payment_gateway(
            (object)['accountid' => $account->get('id'), 'gateway' => 'paypal', 'config' => 'T1']);

        helper::delete_payment_account(account::get_record(['id' => $account->get('id')]));
        $this->assertEmpty($DB->get_records('payment_accounts', ['id' => $account->get('id')]));
        $this->assertEmpty($DB->get_records('payment_gateways', ['id' => $gateway->get('id')]));
    }

    public function test_archive_restore_account() {
        global $DB, $USER;
        $this->resetAfterTest();

        // Delete account with payments - it will be archived.
        $this->setAdminUser();
        $account = helper::save_payment_account((object)['name' => 'Test 1', 'idnumber' => '']);
        $DB->insert_record('payments', [
            'accountid' => $account->get('id'),
            'component' => 'test',
            'paymentarea' => 'test',
            'componentid' => 1,
            'userid' => $USER->id,
        ]);
        helper::delete_payment_account(account::get_record(['id' => $account->get('id')]));
        $this->assertEquals(1, $DB->get_field('payment_accounts', 'archived', ['id' => $account->get('id')]));

        // Restore account.
        helper::restore_payment_account(account::get_record(['id' => $account->get('id')]));
        $this->assertEquals(0, $DB->get_field('payment_accounts', 'archived', ['id' => $account->get('id')]));
    }

    /**
     * Provider for format_cost test
     *
     * @return array
     */
    public function get_rounded_cost_provider(): array {
        return [
            'IRR 0 surcharge' => [5.345, 'IRR', 0, 5],
            'IRR 12% surcharge' => [5.345, 'IRR', 12, 6],
            'USD 0 surcharge' => [5.345, 'USD', 0, 5.34],
            'USD 1% surcharge' => [5.345, 'USD', 1, 5.4],
        ];
    }

    /**
     * Provier for test_get_cost_as_string
     *
     * @return array[]
     */
    public function get_cost_as_string_provider(): array {
        return [
            'IRR 0 surcharge' => [5.345, 'IRR', 0, 'IRR'."\xc2\xa0".'5'],
            'IRR 12% surcharge' => [5.345, 'IRR', 12, 'IRR'."\xc2\xa0".'6'],
            'USD 0 surcharge' => [5.345, 'USD', 0, 'USD'."\xc2\xa0".'5.34'],
            'USD 1% surcharge' => [5.345, 'USD', 1, 'USD'."\xc2\xa0".'5.40'],
        ];
    }

    /**
     * Test for test_format_cost function
     *
     * @dataProvider get_rounded_cost_provider
     * @param float $amount
     * @param string $currency
     * @param float $surcharge
     * @param string $expected
     */
    public function test_get_rounded_cost(float $amount, string $currency, float $surcharge, string $expected) {
        $this->assertEquals($expected, helper::get_rounded_cost($amount, $currency, $surcharge));
    }

    /**
     * Test for get_cost_as_string function
     *
     * @dataProvider get_cost_as_string_provider
     * @param float $amount
     * @param string $currency
     * @param float $surcharge
     * @param string $expected
     */
    public function test_get_cost_as_string(float $amount, string $currency, float $surcharge, string $expected) {
        $this->assertEquals($expected, helper::get_cost_as_string($amount, $currency, $surcharge));
    }
}

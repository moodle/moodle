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
 * Testing generator in payments API
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment;

/**
 * Testing generator in payments API
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {

    public function test_create_account() {
        global $DB;
        $this->resetAfterTest();
        /** @var \core_payment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_payment');
        $this->assertTrue($generator instanceof \core_payment_generator);

        $account1 = $generator->create_payment_account();
        $account2 = $generator->create_payment_account(['name' => 'My name', 'gateways' => 'paypal']);

        $record1 = $DB->get_record('payment_accounts', ['id' => $account1->get('id')]);
        $record2 = $DB->get_record('payment_accounts', ['id' => $account2->get('id')]);

        $this->assertEquals(1, $record1->enabled);
        $this->assertEquals('My name', $record2->name);

        // First account does not have gateways configurations.
        $this->assertEmpty($DB->get_records('payment_gateways', ['accountid' => $account1->get('id')]));
        // Second account has.
        $this->assertCount(1, $DB->get_records('payment_gateways', ['accountid' => $account2->get('id')]));
    }

    public function test_create_payment() {
        global $DB;
        $this->resetAfterTest();
        /** @var \core_payment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_payment');
        $account = $generator->create_payment_account(['gateways' => 'paypal']);
        $user = $this->getDataGenerator()->create_user();

        $paymentid = $generator->create_payment(['accountid' => $account->get('id'), 'amount' => 10, 'userid' => $user->id]);

        $this->assertEquals('testcomponent', $DB->get_field('payments', 'component', ['id' => $paymentid]));
    }
}

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
 * Unit tests for the enrol_fee's payment subsystem callback implementation.
 *
 * @package    enrol_fee
 * @category   test
 * @copyright  2021 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_fee\payment;

/**
 * Unit tests for the enrol_fee's payment subsystem callback implementation.
 *
 * @coversDefaultClass service_provider
 */
class service_provider_testcase extends \advanced_testcase {

    /**
     * Test for service_provider::get_payable().
     *
     * @covers ::get_payable
     */
    public function test_get_payable() {
        global $DB;
        $this->resetAfterTest();

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $feeplugin = enrol_get_plugin('fee');
        $generator = $this->getDataGenerator();
        $account = $generator->get_plugin_generator('core_payment')->create_payment_account(['gateways' => 'paypal']);
        $course = $generator->create_course();

        $data = [
            'courseid' => $course->id,
            'customint1' => $account->get('id'),
            'cost' => 250,
            'currency' => 'USD',
            'roleid' => $studentrole->id,
        ];
        $id = $feeplugin->add_instance($course, $data);

        $payable = service_provider::get_payable('fee', $id);

        $this->assertEquals($account->get('id'), $payable->get_account_id());
        $this->assertEquals(250, $payable->get_amount());
        $this->assertEquals('USD', $payable->get_currency());
    }

    /**
     * Test for service_provider::get_success_url().
     *
     * @covers ::get_success_url
     */
    public function test_get_success_url() {
        global $CFG, $DB;
        $this->resetAfterTest();

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $feeplugin = enrol_get_plugin('fee');
        $generator = $this->getDataGenerator();
        $account = $generator->get_plugin_generator('core_payment')->create_payment_account(['gateways' => 'paypal']);
        $course = $generator->create_course();

        $data = [
            'courseid' => $course->id,
            'customint1' => $account->get('id'),
            'cost' => 250,
            'currency' => 'USD',
            'roleid' => $studentrole->id,
        ];
        $id = $feeplugin->add_instance($course, $data);

        $successurl = service_provider::get_success_url('fee', $id);
        $this->assertEquals(
            $CFG->wwwroot . '/course/view.php?id=' . $course->id,
            $successurl->out(false)
        );
    }

    /**
     * Test for service_provider::deliver_order().
     *
     * @covers ::deliver_order
     */
    public function test_deliver_order() {
        global $DB;
        $this->resetAfterTest();

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $feeplugin = enrol_get_plugin('fee');
        $generator = $this->getDataGenerator();
        $account = $generator->get_plugin_generator('core_payment')->create_payment_account(['gateways' => 'paypal']);
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $user = $generator->create_user();

        $data = [
            'courseid' => $course->id,
            'customint1' => $account->get('id'),
            'cost' => 250,
            'currency' => 'USD',
            'roleid' => $studentrole->id,
        ];
        $id = $feeplugin->add_instance($course, $data);

        $paymentid = $generator->get_plugin_generator('core_payment')->create_payment([
            'accountid' => $account->get('id'),
            'amount' => 10,
            'userid' => $user->id
        ]);

        service_provider::deliver_order('fee', $id, $paymentid, $user->id);
        $this->assertTrue(is_enrolled($context, $user));
        $this->assertTrue(user_has_role_assignment($user->id, $studentrole->id, $context->id));
    }
}

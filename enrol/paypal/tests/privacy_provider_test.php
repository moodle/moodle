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
 * Privacy provider tests.
 *
 * @package    enrol_paypal
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use enrol_paypal\privacy\provider;
use core_privacy\local\request\writer;

/**
 * Class enrol_paypal_privacy_provider_testcase.
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_paypal_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /** @var stdClass A user whose email address matches the business field in some of the PayPal transactions. */
    protected $businessuser1;

    /** @var stdClass A user whose email address matches the business field in some of the PayPal transactions. */
    protected $businessuser2;

    /** @var stdClass A user whose email address matches the business field in some of the PayPal transactions. */
    protected $businessuser3;

    /** @var stdClass A user whose email address matches the receiver_email field in some of the PayPal transactions. */
    protected $receiveruser1;

    /** @var stdClass A user whose email address matches the receiver_email field in some of the PayPal transactions. */
    protected $receiveruser2;

    /** @var stdClass A user whose email address matches the receiver_email field in some of the PayPal transactions. */
    protected $receiveruser3;

    /** @var stdClass A user who is not enrolled in any course. */
    protected $student0;

    /** @var stdClass A student who is only enrolled in course1. */
    protected $student1;

    /** @var stdClass A student who is only enrolled in course2 with 2 transaction histories in the course. */
    protected $student2;

    /** @var stdClass A student who is only enrolled in course3 with 1 transaction histories in the course. */
    protected $student3;

    /** @var stdClass A student who is enrolled in both course1 and course2. */
    protected $student12;

    /** @var stdClass A test course with 2 enrolments for student1 and student12. */
    protected $course1;

    /** @var stdClass A test course with 2 enrolments for student2 and student12. */
    protected $course2;

    /** @var stdClass A test course with 2 enrolments for student2 and student12. */
    protected $course3;

    protected function setUp(): void {
        global $DB;

        $this->resetAfterTest();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $generator = $this->getDataGenerator();

        // Create seller accounts.
        $this->businessuser1 = $generator->create_user(['email' => 'business1@domain.invalid']);
        $this->businessuser2 = $generator->create_user(['email' => 'business2@domain.invalid']);
        $this->businessuser3 = $generator->create_user(['email' => 'business3@domain.invalid']);
        $this->receiveruser1 = $generator->create_user(['email' => 'receiver1@domain.invalid']);
        $this->receiveruser2 = $generator->create_user(['email' => 'receiver2@domain.invalid']);
        $this->receiveruser3 = $generator->create_user(['email' => 'receiver3@domain.invalid']);

        // Create courses.
        $this->course1 = $generator->create_course();
        $this->course2 = $generator->create_course();
        $this->course3 = $generator->create_course();

        // Create enrolment instances.
        $paypalplugin = enrol_get_plugin('paypal');

        $enrolinstanceid = $paypalplugin->add_instance($this->course1,
                ['roleid'   => $studentrole->id, 'courseid' => $this->course1->id]);
        $enrolinstance1  = $DB->get_record('enrol', array('id' => $enrolinstanceid));

        $enrolinstanceid = $paypalplugin->add_instance($this->course2,
                ['roleid'   => $studentrole->id, 'courseid' => $this->course2->id]);
        $enrolinstance2 = $DB->get_record('enrol', array('id' => $enrolinstanceid));

        $enrolinstanceid = $paypalplugin->add_instance($this->course3,
                ['roleid'   => $studentrole->id, 'courseid' => $this->course3->id]);
        $enrolinstance3 = $DB->get_record('enrol', array('id' => $enrolinstanceid));

        // Create students.
        $this->student0 = $generator->create_user();    // This user will not be enrolled in any course.
        $this->student1 = $generator->create_user();
        $this->student2 = $generator->create_user();
        $this->student3 = $generator->create_user();
        $this->student12 = $generator->create_user();

        // Enrol student1 in course1.
        $paypalplugin->enrol_user($enrolinstance1, $this->student1->id, $studentrole->id);
        $this->create_enrol_paypal_record(
            $this->businessuser1,
            $this->receiveruser1,
            $this->course1,
            $this->student1,
            $enrolinstance1,
            'STUDENT1-IN-COURSE1-00',
            time()
        );

        // Enrol student2 in course2.
        $paypalplugin->enrol_user($enrolinstance2, $this->student2->id, $studentrole->id);
        // This user has 2 transaction histories.
        // Here is the first one.
        $this->create_enrol_paypal_record(
            $this->businessuser1,
            $this->receiveruser2,
            $this->course2,
            $this->student2,
            $enrolinstance2,
            'STUDENT2-IN-COURSE2-00',
            // Yesterday.
            time() - DAYSECS
        );
        // And now, the second one.
        $this->create_enrol_paypal_record(
            $this->businessuser1,
            $this->receiveruser2,
            $this->course2,
            $this->student2,
            $enrolinstance2,
            'STUDENT2-IN-COURSE2-01',
            time()
        );

        // Enrol student12 in course1 and course2.
        // First in course1.
        $paypalplugin->enrol_user($enrolinstance1, $this->student12->id, $studentrole->id);
        $this->create_enrol_paypal_record(
            $this->businessuser2,
            $this->receiveruser1,
            $this->course1,
            $this->student12,
            $enrolinstance1,
            'STUDENT12-IN-COURSE1-00',
            time()
        );
        // Then in course2.
        $paypalplugin->enrol_user($enrolinstance2, $this->student12->id, $studentrole->id);
        $this->create_enrol_paypal_record(
            $this->businessuser2,
            $this->receiveruser2,
            $this->course2,
            $this->student12,
            $enrolinstance2,
            'STUDENT12-IN-COURSE2-00',
            time()
        );

        // Enrol student3 in course3 with businessuser3 as the receiver.
        $paypalplugin->enrol_user($enrolinstance1, $this->student3->id, $studentrole->id);
        $this->create_enrol_paypal_record(
            $this->businessuser3,
            $this->receiveruser3,
            $this->course3,
            $this->student3,
            $enrolinstance3,
            'STUDENT3-IN-COURSE3-00',
            time()
        );
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('enrol_paypal');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(2, $itemcollection);

        $location = reset($itemcollection);
        $this->assertEquals('paypal.com', $location->get_name());
        $this->assertEquals('privacy:metadata:enrol_paypal:paypal_com', $location->get_summary());

        $privacyfields = $location->get_privacy_fields();
        $this->assertArrayHasKey('os0', $privacyfields);
        $this->assertArrayHasKey('custom', $privacyfields);
        $this->assertArrayHasKey('first_name', $privacyfields);
        $this->assertArrayHasKey('last_name', $privacyfields);
        $this->assertArrayHasKey('address', $privacyfields);
        $this->assertArrayHasKey('city', $privacyfields);
        $this->assertArrayHasKey('email', $privacyfields);
        $this->assertArrayHasKey('country', $privacyfields);

        $table = next($itemcollection);
        $this->assertEquals('enrol_paypal', $table->get_name());
        $this->assertEquals('privacy:metadata:enrol_paypal:enrol_paypal', $table->get_summary());

        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('business', $privacyfields);
        $this->assertArrayHasKey('receiver_email', $privacyfields);
        $this->assertArrayHasKey('receiver_id', $privacyfields);
        $this->assertArrayHasKey('item_name', $privacyfields);
        $this->assertArrayHasKey('courseid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('instanceid', $privacyfields);
        $this->assertArrayHasKey('memo', $privacyfields);
        $this->assertArrayHasKey('tax', $privacyfields);
        $this->assertArrayHasKey('option_selection1_x', $privacyfields);
        $this->assertArrayHasKey('payment_status', $privacyfields);
        $this->assertArrayHasKey('pending_reason', $privacyfields);
        $this->assertArrayHasKey('reason_code', $privacyfields);
        $this->assertArrayHasKey('txn_id', $privacyfields);
        $this->assertArrayHasKey('parent_txn_id', $privacyfields);
        $this->assertArrayHasKey('payment_type', $privacyfields);
        $this->assertArrayHasKey('timeupdated', $privacyfields);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        // Student1 is only enrolled in 1 course.
        $contextlist = provider::get_contexts_for_userid($this->student1->id);
        $this->assertCount(1, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEquals([$coursecontext1->id], $contextids);

        // Student12 is enrolled in 2 course.
        $contextlist = provider::get_contexts_for_userid($this->student12->id);
        $this->assertCount(2, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertContainsEquals($coursecontext1->id, $contextids);
        $this->assertContainsEquals($coursecontext2->id, $contextids);
    }

    /**
     * Test for provider::get_contexts_for_userid with a user who is a receiver.
     */
    public function test_get_contexts_for_userid_receiver() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        // Receiver User 1 is the Receiver of one course.
        $contextlist = provider::get_contexts_for_userid($this->receiveruser1->id);
        $this->assertCount(1, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEquals([$coursecontext1->id], $contextids);

        // Receiver User 2 is the Receiver of course.
        $contextlist = provider::get_contexts_for_userid($this->receiveruser2->id);
        $this->assertCount(1, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEquals([$coursecontext2->id], $contextids);
    }

    /**
     * Test for provider::get_contexts_for_userid with a user who is a business.
     */
    public function test_get_contexts_for_userid_business() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);
        $coursecontext3 = context_course::instance($this->course3->id);

        // Business User 1 is the Receiver of course 1 and course 2.
        $contextlist = provider::get_contexts_for_userid($this->businessuser1->id);
        $this->assertCount(2, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEqualsCanonicalizing([$coursecontext1->id, $coursecontext2->id], $contextids);

        // Business User 3 is the Receiver of course 3 only.
        $contextlist = provider::get_contexts_for_userid($this->businessuser3->id);
        $this->assertCount(1, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEquals([$coursecontext3->id], $contextids);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_user_data() {
        $coursecontext1 = context_course::instance($this->course1->id);

        $this->setUser($this->student1);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student1->id, $coursecontext1, 'enrol_paypal');
        $writer = writer::with_context($coursecontext1);
        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data([get_string('transactions', 'enrol_paypal')]);

    }

    /**
     * Test for provider::export_user_data() when user is not enrolled.
     */
    public function test_export_user_data_not_enrolled() {
        $coursecontext1 = context_course::instance($this->course1->id);

        $this->setUser($this->student2);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student2->id, $coursecontext1, 'enrol_paypal');
        $writer = writer::with_context($coursecontext1);
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::export_user_data() when user has no enrolment.
     */
    public function test_export_user_data_no_enrolment() {
        $coursecontext1 = context_course::instance($this->course1->id);

        $this->setUser($this->student0);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student0->id, $coursecontext1, 'enrol_paypal');
        $writer = writer::with_context($coursecontext1);
        $this->assertFalse($writer->has_any_data());
    }

    public function test_export_user_data_multiple_paypal_history() {
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->student2);
        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student2->id, $coursecontext2, 'enrol_paypal');
        $writer = writer::with_context($coursecontext2);
        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data([get_string('transactions', 'enrol_paypal')]);
        $this->assertCount(2, $data->transactions);
        $this->assertEqualsCanonicalizing(
                ['STUDENT2-IN-COURSE2-00', 'STUDENT2-IN-COURSE2-01'],
                array_column($data->transactions, 'txn_id'));
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);
        $this->setUser($this->student1);

        // Before deletion, we should have 2 PayPal transactions in course1 and 3 PayPal transactions in course2.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );

        // Delete data based on context.
        provider::delete_data_for_all_users_in_context($coursecontext1);

        // After deletion, PayPal transactions in course1 should have been deleted.
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context() when there is multiple transaction histories for a user.
     */
    public function test_delete_data_for_all_users_in_context_multiple_transactions() {
        global $DB;

        $coursecontext2 = context_course::instance($this->course2->id);
        $this->setUser($this->student2);

        // Before deletion, we should have 2 PayPal transactions in course1 and 3 PayPal transactions in course2.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );

        // Delete data based on context.
        provider::delete_data_for_all_users_in_context($coursecontext2);

        // After deletion, PayPal transactions in course2 should have been deleted.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user() when student is enrolled in multiple courses and deleting from one of them.
     */
    public function test_delete_data_for_user_from_single_context() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);

        $this->setUser($this->student12);

        // Before deletion, we should have 2 PayPal transactions (1 of them for student12) in course1
        // and 3 PayPal transactions (1 of them for student12) in course2.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id, 'userid' => $this->student12->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id, 'userid' => $this->student12->id])
        );

        // Delete data for user.
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student12, 'enrol_paypal',
                [$coursecontext1->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, PayPal transactions for student12 in course1 should have been deleted.
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id, 'userid' => $this->student12->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id, 'userid' => $this->student12->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user() when student is enrolled in multiple courses and deleting from all of them.
     */
    public function test_delete_data_for_user_from_multiple_context() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->student12);

        // Before deletion, we should have 2 PayPal transactions (1 of them for student12) in course1
        // and 3 PayPal transactions (1 of them for student12) in course2.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id, 'userid' => $this->student12->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id, 'userid' => $this->student12->id])
        );

        // Delete data for user.
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student12, 'enrol_paypal',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, PayPal enrolment data for student12 in both course1 and course2 should have been deleted.
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id, 'userid' => $this->student12->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id, 'userid' => $this->student12->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user() when user is not enrolled, but is the receiver of the payment.
     */
    public function test_delete_data_for_user_for_business_user() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);

        $this->setUser($this->businessuser1);

        // Before deletion, we should have 5 PayPal enrolments.
        // 3 of which paid to businessuser1 and 2 of which paid to businessuser2.
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['business' => $this->businessuser1->email])
        );
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['business' => $this->businessuser2->email])
        );

        // Delete data for user in $coursecontext1.
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->businessuser1, 'enrol_paypal',
                [$coursecontext1->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, PayPal enrolment data for businessuser1 in course1 should have been deleted.
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id, 'business' => $this->businessuser1->email])
        );
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['business' => $this->businessuser1->email])
        );
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id, 'business' => ''])
        );
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['business' => $this->businessuser2->email])
        );
    }

    /**
     * Test for provider::delete_data_for_user() when user is not enrolled, but is the receiver of the payment.
     */
    public function test_delete_data_for_user_for_receiver_user() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);

        $this->setUser($this->receiveruser1);

        // Before deletion, we should have 5 PayPal enrolments.
        // 2 of which paid to receiveruser1 and 3 of which paid to receiveruser2.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['receiver_email' => $this->receiveruser1->email])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['receiver_email' => $this->receiveruser2->email])
        );

        // Delete data for user.
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->receiveruser1, 'enrol_paypal',
                [$coursecontext1->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, PayPal enrolment data for receiveruser1 in course1 should have been deleted.
        $this->assertEquals(
                0,
                $DB->count_records('enrol_paypal', ['receiver_email' => $this->receiveruser1->email])
        );
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['receiver_email' => ''])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['receiver_email' => $this->receiveruser2->email])
        );
    }

    /**
     * Helper function to create an enrol_paypal record.
     *
     * @param   \stdClass   $business The user associated with the business
     * @param   \stdClass   $receiver The user associated with the receiver
     * @param   \stdClass   $course The course to associate with
     * @param   \stdClass   $user The user associated with the student
     * @param   \stdClass   $enrol The enrolment instance
     * @param   String      $txnid The Paypal txnid to use
     * @param   int         $time The txn time
     */
    protected function create_enrol_paypal_record($business, $receiver, $course, $user, $enrol, $txnid, $time) {
        global $DB;

        $paypaldata = [
            'business'       => $business->email,
            'receiver_email' => $receiver->email,
            'receiver_id'    => 'SELLERSID',
            'item_name'      => $course->fullname,
            'courseid'       => $course->id,
            'userid'         => $user->id,
            'instanceid'     => $enrol->id,
            'payment_status' => 'Completed',
            'txn_id'         => $txnid,
            'payment_type'   => 'instant',
            'timeupdated'    => $time,
        ];
        $DB->insert_record('enrol_paypal', $paypaldata);
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);
        $coursecontext3 = context_course::instance($this->course3->id);

        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, 'enrol_paypal');
        provider::get_users_in_context($userlist1);
        $this->assertEqualsCanonicalizing(
                [
                    $this->businessuser1->id,
                    $this->businessuser2->id,
                    $this->receiveruser1->id,
                    $this->student1->id,
                    $this->student12->id
                ],
                $userlist1->get_userids()
        );

        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, 'enrol_paypal');
        provider::get_users_in_context($userlist2);
        $this->assertEqualsCanonicalizing(
                [
                    $this->businessuser1->id,
                    $this->businessuser2->id,
                    $this->receiveruser2->id,
                    $this->student2->id,
                    $this->student12->id
                ],
                $userlist2->get_userids()
        );

        $userlist3 = new \core_privacy\local\request\userlist($coursecontext3, 'enrol_paypal');
        provider::get_users_in_context($userlist3);
        $this->assertEqualsCanonicalizing(
                [
                    $this->businessuser3->id,
                    $this->receiveruser3->id,
                    $this->student3->id
                ],
                $userlist3->get_userids()
        );
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);

        // Before deletion, we should have 2 PayPal transactions (1 of them for student12) in course1
        // and 3 PayPal transactions (1 of them for student12) in course2.
        // Student12 is enrolled in course1 and course2.
        // There is 1 transaction in course1 and 2 transactions in course2 under the name of businessuser1.
        $this->assertEquals(
                2,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEqualsCanonicalizing(
                [$this->course1->id, $this->course2->id],
                $DB->get_fieldset_select('enrol_paypal', 'courseid', 'userid = ?', [$this->student12->id])
        );
        $this->assertEqualsCanonicalizing(
                [$this->course1->id, $this->course2->id, $this->course2->id],
                $DB->get_fieldset_select('enrol_paypal', 'courseid', 'business = ?',
                        [\core_text::strtolower($this->businessuser1->email)])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );

        // Delete data of student12 and businessuser1 in course1.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'enrol_paypal',
                [$this->student12->id, $this->businessuser1->id]);
        provider::delete_data_for_users($approveduserlist);

        // After deletion, PayPal transactions for student12 in course1 should have been deleted.
        // Now, we should have 1 PayPal transaction (which is not related to student12) in course1.
        // There should still be 3 PayPal transactions (1 of them for student12) in course2.
        // Student12 is not enrolled in course1 anymore, but s/he is still enrolled in course2.
        // There is no transaction in course1 under the name of businessuser1, but the 2 transactions in course2
        // that were under his/her name are intact.
        $this->assertEquals(
                1,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course1->id])
        );
        $this->assertEqualsCanonicalizing(
                [$this->course2->id],
                $DB->get_fieldset_select('enrol_paypal', 'courseid', 'userid = ?', [$this->student12->id])
        );
        $this->assertEqualsCanonicalizing(
                [$this->course2->id, $this->course2->id],
                $DB->get_fieldset_select('enrol_paypal', 'courseid', 'business = ?',
                        [\core_text::strtolower($this->businessuser1->email)])
        );
        $this->assertEquals(
                3,
                $DB->count_records('enrol_paypal', ['courseid' => $this->course2->id])
        );
    }

    /**
     * Test for provider::delete_data_for_users() for business user deletion.
     */
    public function test_delete_data_for_users_business() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);

        // Before deletion, there are 3 transactions under the name of businessuser1 and one of them is in course1.
        $this->assertEquals(3, $DB->count_records('enrol_paypal', ['business' => $this->businessuser1->email]));
        $transactions = $DB->get_records('enrol_paypal', [
            'courseid' => $this->course1->id,
            'business' => $this->businessuser1->email
        ]);
        $this->assertCount(1, $transactions);
        $transaction = reset($transactions);

        // Delete data of businessuser1 in course1.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'enrol_paypal',
                [$this->businessuser1->id]);
        provider::delete_data_for_users($approveduserlist);

        // After deletion, there should be 2 transactions under the name of businessuser1 and none of them should be in course1.
        $this->assertEquals(2, $DB->count_records('enrol_paypal', ['business' => $this->businessuser1->email]));
        $this->assertEquals(0, $DB->count_records('enrol_paypal', [
            'courseid' => $this->course1->id,
            'business' => $this->businessuser1->email
        ]));

        // Also, the transaction in course1 that was under the name of businessuser1 should still exist,
        // but it should not be under the name of businessuser1 anymore.
        $newtransaction = $DB->get_record('enrol_paypal', ['id' => $transaction->id]);
        $this->assertEquals('', $newtransaction->business);
    }

    /**
     * Test for provider::delete_data_for_users() for receiver user deletion.
     */
    public function test_delete_data_for_users_receiver() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);

        // Before deletion, there are 2 transactions under the name of receiveruser1 and both of them are in course1.
        $this->assertEquals(2, $DB->count_records('enrol_paypal', ['receiver_email' => $this->receiveruser1->email]));
        $transactions = $DB->get_records('enrol_paypal', [
            'courseid' => $this->course1->id,
            'receiver_email' => $this->receiveruser1->email
        ]);
        $this->assertCount(2, $transactions);

        // Delete data of receiveruser1 in course1.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'enrol_paypal',
                [$this->receiveruser1->id]);
        provider::delete_data_for_users($approveduserlist);

        // After deletion, there should be no transaction under the name of receiveruser1.
        $this->assertEquals(0, $DB->count_records('enrol_paypal', ['receiver_email' => $this->receiveruser1->email]));

        // Also, the transactions in course1 that were under the name of receiveruser1 should still exist,
        // but they should not be under the name of receiveruser1 anymore.
        foreach ($transactions as $transaction) {
            $newtransaction = $DB->get_record('enrol_paypal', ['id' => $transaction->id]);
            $this->assertEquals('', $newtransaction->receiver_email);
        }
    }
}

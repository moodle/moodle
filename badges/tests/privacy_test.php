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
 * Data provider tests.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_badges\privacy\provider;

require_once($CFG->libdir . '/badgeslib.php');

/**
 * Data provider testcase class.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_badges_privacy_testcase extends provider_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_get_contexts_for_userid_for_badge_editing() {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        // Assert that we find contexts where we created/modified a badge.
        $this->create_badge(['usercreated' => $u1->id, 'usermodified' => $u5->id]);
        $this->create_badge(['usercreated' => $u2->id, 'type' => BADGE_TYPE_COURSE, 'courseid' => $c1->id]);
        $this->create_badge(['usermodified' => $u3->id]);
        $this->create_badge(['usermodified' => $u4->id, 'type' => BADGE_TYPE_COURSE, 'courseid' => $c2->id,
            'usercreated' => $u5->id]);

        $contexts = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($sysctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($c1ctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u3->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($sysctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u4->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($c2ctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u5->id)->get_contextids();
        $this->assertCount(2, $contexts);
        $this->assertTrue(in_array($sysctx->id, $contexts));
        $this->assertTrue(in_array($c2ctx->id, $contexts));
    }

    public function test_get_contexts_for_userid_for_manual_award() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $c1 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);
        $u3ctx = context_user::instance($u3->id);
        $u4ctx = context_user::instance($u4->id);
        $b1 = $this->create_badge();
        $b2 = $this->create_badge(['type' => BADGE_TYPE_COURSE, 'courseid' => $c1->id]);

        $this->create_manual_award(['recipientid' => $u4->id, 'issuerid' => $u1->id, 'badgeid' => $b1->id]);
        $this->create_manual_award(['recipientid' => $u3->id, 'issuerid' => $u2->id, 'badgeid' => $b1->id]);
        $this->create_manual_award(['recipientid' => $u3->id, 'issuerid' => $u2->id, 'badgeid' => $b2->id]);

        $contexts = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u4ctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u3ctx->id, $contexts[0]);
    }

    public function test_get_contexts_for_userid_for_my_stuff() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $c1 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);
        $u3ctx = context_user::instance($u3->id);
        $u4ctx = context_user::instance($u4->id);
        $b1 = $this->create_badge();
        $b2 = $this->create_badge(['type' => BADGE_TYPE_COURSE, 'courseid' => $c1->id]);

        $this->create_backpack(['userid' => $u1->id]);
        $this->create_manual_award(['recipientid' => $u2->id, 'badgeid' => $b1->id]);
        $this->create_issued(['badgeid' => $b2->id, 'userid' => $u3->id]);

        $crit = $this->create_criteria_manual($b1->id);
        $crit->mark_complete($u4->id);

        $contexts = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u1ctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u2ctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u3->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u3ctx->id, $contexts[0]);

        $contexts = provider::get_contexts_for_userid($u4->id)->get_contextids();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u4ctx->id, $contexts[0]);
    }

    public function test_delete_data_for_user() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $b1 = $this->create_badge(['usercreated' => $u1->id, 'usermodified' => $u2->id]);
        $b2 = $this->create_badge(['usercreated' => $u2->id, 'usermodified' => $u1->id,
            'type' => BADGE_TYPE_COURSE, 'courseid' => $c1->id]);

        $this->create_backpack(['userid' => $u1->id]);
        $this->create_backpack(['userid' => $u2->id]);
        $this->create_manual_award(['recipientid' => $u1->id, 'badgeid' => $b1->id]);
        $this->create_manual_award(['recipientid' => $u2->id, 'badgeid' => $b1->id, 'issuerid' => $u1->id]);
        $this->create_issued(['badgeid' => $b2->id, 'userid' => $u1->id]);
        $this->create_issued(['badgeid' => $b2->id, 'userid' => $u2->id]);

        $crit = $this->create_criteria_manual($b1->id);
        $crit->mark_complete($u2->id);
        $crit = $this->create_criteria_manual($b2->id);
        $crit->mark_complete($u1->id);

        $this->assertTrue($DB->record_exists('badge_backpack', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_backpack', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge_manual_award', ['recipientid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_manual_award', ['recipientid' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge_issued', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_issued', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge_criteria_met', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_criteria_met', ['userid' => $u2->id]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'core_badges', [$sysctx->id, $c1ctx->id,
            $u1ctx->id, $u2ctx->id]));

        $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_backpack', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_backpack', ['userid' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_manual_award', ['recipientid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_manual_award', ['recipientid' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_issued', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_issued', ['userid' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_criteria_met', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_criteria_met', ['userid' => $u2->id]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $b1 = $this->create_badge(['usercreated' => $u1->id, 'usermodified' => $u2->id]);
        $b2 = $this->create_badge(['usercreated' => $u2->id, 'usermodified' => $u1->id,
            'type' => BADGE_TYPE_COURSE, 'courseid' => $c1->id]);

        $this->create_backpack(['userid' => $u1->id]);
        $this->create_backpack(['userid' => $u2->id]);
        $this->create_manual_award(['recipientid' => $u1->id, 'badgeid' => $b1->id]);
        $this->create_manual_award(['recipientid' => $u2->id, 'badgeid' => $b1->id, 'issuerid' => $u1->id]);
        $this->create_issued(['badgeid' => $b2->id, 'userid' => $u1->id]);
        $this->create_issued(['badgeid' => $b2->id, 'userid' => $u2->id]);

        $crit = $this->create_criteria_manual($b1->id);
        $crit->mark_complete($u2->id);
        $crit = $this->create_criteria_manual($b2->id);
        $crit->mark_complete($u1->id);

        $assertnochange = function() use ($DB, $u1, $u2) {
            $this->assertTrue($DB->record_exists('badge_backpack', ['userid' => $u1->id]));
            $this->assertTrue($DB->record_exists('badge_backpack', ['userid' => $u2->id]));
            $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u1->id]));
            $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u1->id]));
            $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u2->id]));
            $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u2->id]));
            $this->assertTrue($DB->record_exists('badge_manual_award', ['recipientid' => $u1->id]));
            $this->assertTrue($DB->record_exists('badge_manual_award', ['recipientid' => $u2->id]));
            $this->assertTrue($DB->record_exists('badge_issued', ['userid' => $u1->id]));
            $this->assertTrue($DB->record_exists('badge_issued', ['userid' => $u2->id]));
            $this->assertTrue($DB->record_exists('badge_criteria_met', ['userid' => $u1->id]));
            $this->assertTrue($DB->record_exists('badge_criteria_met', ['userid' => $u2->id]));
        };
        $assertnochange();

        provider::delete_data_for_all_users_in_context($sysctx);
        $assertnochange();

        provider::delete_data_for_all_users_in_context($c1ctx);
        $assertnochange();

        provider::delete_data_for_all_users_in_context($u1ctx);
        $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge', ['usercreated' => $u2->id]));
        $this->assertTrue($DB->record_exists('badge', ['usermodified' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_backpack', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_backpack', ['userid' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_manual_award', ['recipientid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_manual_award', ['recipientid' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_issued', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_issued', ['userid' => $u2->id]));
        $this->assertFalse($DB->record_exists('badge_criteria_met', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('badge_criteria_met', ['userid' => $u2->id]));
    }

    public function test_export_data_for_user() {
        global $DB;

        $yes = transform::yesno(true);
        $no = transform::yesno(false);

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $c1 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $b1 = $this->create_badge(['usercreated' => $u3->id]);
        $b2 = $this->create_badge(['type' => BADGE_TYPE_COURSE, 'courseid' => $c1->id, 'usermodified' => $u3->id]);
        $b3 = $this->create_badge();
        $b3crit = $this->create_criteria_manual($b3->id);
        $b4 = $this->create_badge();

        // Create things for user 2, to check it's not exported it.
        $this->create_issued(['badgeid' => $b4->id, 'userid' => $u2->id]);
        $this->create_backpack(['userid' => $u2->id, 'email' => $u2->email]);
        $this->create_manual_award(['badgeid' => $b1->id, 'recipientid' => $u2->id, 'issuerid' => $u3->id]);

        // Create a set of stuff for u1.
        $this->create_issued(['badgeid' => $b1->id, 'userid' => $u1->id, 'uniquehash' => 'yoohoo']);
        $this->create_manual_award(['badgeid' => $b2->id, 'recipientid' => $u1->id, 'issuerid' => $u3->id]);
        $b3crit->mark_complete($u1->id);
        $this->create_backpack(['userid' => $u1->id, 'email' => $u1->email]);

        // Check u1.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_badges', [$u1ctx->id, $u2ctx->id,
            $sysctx->id, $c1ctx->id]));
        $this->assertFalse(writer::with_context($u2ctx)->has_any_data());
        $this->assertFalse(writer::with_context($sysctx)->has_any_data());
        $this->assertFalse(writer::with_context($c1ctx)->has_any_data());

        $path = [get_string('badges', 'core_badges'), "{$b1->name} ({$b1->id})"];
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertEquals($b1->name, $data->name);
        $this->assertNotEmpty($data->issued);
        $this->assertEmpty($data->manual_award);
        $this->assertEmpty($data->criteria_met);
        $this->assertFalse(isset($data->course));
        $this->assertEquals('yoohoo', $data->issued['unique_hash']);
        $this->assertNull($data->issued['expires_on']);

        $path = [get_string('badges', 'core_badges'), "{$b2->name} ({$b2->id})"];
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertEquals($b2->name, $data->name);
        $this->assertEmpty($data->issued);
        $this->assertNotEmpty($data->manual_award);
        $this->assertEmpty($data->criteria_met);
        $this->assertEquals($c1->fullname, $data->course);
        $this->assertEquals($u3->id, $data->manual_award['issuer']);

        $path = [get_string('badges', 'core_badges'), "{$b3->name} ({$b3->id})"];
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertEquals($b3->name, $data->name);
        $this->assertEmpty($data->issued);
        $this->assertEmpty($data->manual_award);
        $this->assertNotEmpty($data->criteria_met);
        $this->assertNotFalse(strpos($data->criteria_met[0], get_string('criteria_descr_2', 'core_badges', 'ALL')));

        $path = [get_string('badges', 'core_badges')];
        $data = writer::with_context($u1ctx)->get_related_data($path, 'backpacks');
        $this->assertCount(1, $data->backpacks);
        $this->assertEquals($u1->email, $data->backpacks[0]['email']);

        // Confirm we do not have u2.
        $path = [get_string('badges', 'core_badges'), "{$b4->name} ({$b4->id})"];
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data($path);
        $this->assertEmpty($data);

        // Export for u3.
        writer::reset();
        $path = [get_string('badges', 'core_badges')];
        provider::export_user_data(new approved_contextlist($u3, 'core_badges', [$u1ctx->id, $u2ctx->id,
            $sysctx->id, $c1ctx->id]));

        $data = writer::with_context($u2ctx)->get_related_data($path, 'manual_awards');
        $this->assertCount(1, $data->badges);
        $this->assertEquals($b1->name, $data->badges[0]['name']);
        $this->assertEquals($yes, $data->badges[0]['issued_by_you']);
        $this->assertEquals('Manager', $data->badges[0]['issuer_role']);

        $data = writer::with_context($sysctx)->get_data($path);
        $this->assertCount(1, $data->badges);
        $this->assertEquals($b1->name, $data->badges[0]['name']);
        $this->assertEquals($yes, $data->badges[0]['created_by_you']);
        $this->assertEquals($no, $data->badges[0]['modified_by_you']);

        $data = writer::with_context($c1ctx)->get_data($path);
        $this->assertCount(1, $data->badges);
        $this->assertEquals($b2->name, $data->badges[0]['name']);
        $this->assertEquals($no, $data->badges[0]['created_by_you']);
        $this->assertEquals($yes, $data->badges[0]['modified_by_you']);

        $data = writer::with_context($u1ctx)->get_related_data($path, 'manual_awards');
        $this->assertCount(1, $data->badges);
        $this->assertEquals($b3->name, $data->badges[0]['name']);
        $this->assertEquals($yes, $data->badges[0]['issued_by_you']);
        $this->assertEquals('Manager', $data->badges[0]['issuer_role']);
    }

    /**
     * Create a badge.
     *
     * @param array $params Parameters.
     * @return object
     */
    protected function create_badge(array $params = []) {
        global $DB, $USER;
        $record = (object) array_merge([
            'name' => "Test badge with 'apostrophe' and other friends (<>&@#)",
            'description' => "Testing badges",
            'timecreated' => time(),
            'timemodified' => time(),
            'usercreated' => $USER->id,
            'usermodified' => $USER->id,
            'issuername' => "Test issuer",
            'issuerurl' => "http://issuer-url.domain.co.nz",
            'issuercontact' => "issuer@example.com",
            'expiredate' => null,
            'expireperiod' => null,
            'type' => BADGE_TYPE_SITE,
            'courseid' => null,
            'messagesubject' => "Test message subject",
            'message' => "Test message body",
            'attachment' => 1,
            'notification' => 0,
            'status' => BADGE_STATUS_ACTIVE,
        ], $params);
        $record->id = $DB->insert_record('badge', $record);

        return $record;
    }

    /**
     * Create a backpack.
     *
     * @param array $params Parameters.
     * @return object
     */
    protected function create_backpack(array $params = []) {
        global $DB;
        $record = (object) array_merge([
            'userid' => null,
            'email' => 'test@example.com',
            'backpackurl' => "http://here.there.com",
            'backpackuid' => "12345",
            'autosync' => 0,
            'password' => '',
        ], $params);
        $record->id = $DB->insert_record('badge_backpack', $record);
        return $record;
    }

    /**
     * Create a criteria of type badge.
     *
     * @param int $badgeid The badge ID.
     * @param array $params Parameters.
     * @return object
     */
    protected function create_criteria_badge($badgeid, array $params = []) {
        $badge = new badge($badgeid);
        if (empty($badge->criteria)) {
            $overall = award_criteria::build(['criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id]);
            $overall->save(['agg' => BADGE_CRITERIA_AGGREGATION_ALL]);
        }

        $criteria = award_criteria::build([
            'badgeid' => $badge->id,
            'criteriatype' => BADGE_CRITERIA_TYPE_BADGE,
        ]);

        if (isset($params['badgeid'])) {
            $params['badge_' . $params['badgeid']] = $params['badgeid'];
            unset($params['badgeid']);
        }

        $criteria->save($params);
        $badge = new badge($badgeid);
        return $badge->criteria[BADGE_CRITERIA_TYPE_BADGE];
    }

    /**
     * Create a criteria of type manual.
     *
     * @param int $badgeid The badge ID.
     * @param array $params Parameters.
     * @return object
     */
    protected function create_criteria_manual($badgeid, array $params = []) {
        global $DB;

        $badge = new badge($badgeid);
        if (empty($badge->criteria)) {
            $overall = award_criteria::build(['criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id]);
            $overall->save(['agg' => BADGE_CRITERIA_AGGREGATION_ALL]);
        }

        $criteria = award_criteria::build([
            'badgeid' => $badge->id,
            'criteriatype' => BADGE_CRITERIA_TYPE_MANUAL,
        ]);

        $managerroleid = $DB->get_field_select('role', 'id', 'shortname = ?', ['manager'], IGNORE_MULTIPLE);
        if (empty($params)) {
            $params = [
                'role_' . $managerroleid = $managerroleid
            ];
        }

        $criteria->save($params);
        $badge = new badge($badgeid);
        return $badge->criteria[BADGE_CRITERIA_TYPE_MANUAL];
    }

    /**
     * Create a badge issued.
     *
     * @param array $params Parameters.
     * @return object
     */
    protected function create_issued(array $params = []) {
        global $DB, $USER;
        $record = (object) array_merge([
            'badgeid' => null,
            'userid' => null,
            'uniquehash' => random_string(40),
            'dateissued' => time(),
            'dateexpire' => null,
            'visible' => 1,
            'issuernotified' => null,
        ], $params);
        $record->id = $DB->insert_record('badge_issued', $record);
        return $record;
    }

    /**
     * Create a manual award.
     *
     * @param array $params Parameters.
     * @return object
     */
    protected function create_manual_award(array $params = []) {
        global $DB, $USER;
        $record = (object) array_merge([
            'badgeid' => null,
            'recipientid' => null,
            'issuerid' => $USER->id,
            'issuerrole' => $DB->get_field_select('role', 'id', 'shortname = ?', ['manager'], IGNORE_MULTIPLE),
            'datemet' => time()
        ], $params);
        $record->id = $DB->insert_record('badge_manual_award', $record);
        return $record;
    }

}

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
 * Expired contexts tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_dataprivacy\api;
use tool_dataprivacy\data_registry;
use tool_dataprivacy\expired_context;

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Expired contexts tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_expired_contexts_testcase extends advanced_testcase {

    /**
     * setUp.
     */
    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test expired users flagging and deletion.
     *
     * @return null
     */
    public function test_expired_users() {
        global $DB;

        $purpose = api::create_purpose((object)['name' => 'p1', 'retentionperiod' => 'PT1H', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $cat = api::create_category((object)['name' => 'a']);

        $record = (object)[
            'purposeid' => $purpose->get('id'),
            'categoryid' => $cat->get('id'),
            'contextlevel' => CONTEXT_SYSTEM,
        ];
        api::set_contextlevel($record);
        $record->contextlevel = CONTEXT_USER;
        api::set_contextlevel($record);

        $userdata = ['lastaccess' => '123'];
        $user1 = $this->getDataGenerator()->create_user($userdata);
        $user2 = $this->getDataGenerator()->create_user($userdata);
        $user3 = $this->getDataGenerator()->create_user($userdata);
        $user4 = $this->getDataGenerator()->create_user($userdata);
        $user5 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        // Old course.
        $course2 = $this->getDataGenerator()->create_course(['startdate' => '1', 'enddate' => '2']);
        // Ongoing course.
        $course3 = $this->getDataGenerator()->create_course(['startdate' => '1', 'enddate' => time() + YEARSECS]);

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course3->id, 'student');

        // Add an activity and some data for user 2.
        $assignmod = $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);
        $data = (object) [
            'assignment' => $assignmod->id,
            'userid' => $user2->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'status' => 'new',
            'groupid' => 0,
            'attemptnumber' => 0,
            'latest' => 1,
        ];
        $DB->insert_record('assign_submission', $data);
        // We should have one record in the assign submission table.
        $this->assertEquals(1, $DB->count_records('assign_submission'));

        // Users without lastaccess are skipped as well as users enroled in courses with no end date.
        $expired = new \tool_dataprivacy\expired_user_contexts();
        $numexpired = $expired->flag_expired();
        $this->assertEquals(2, $numexpired);
        $this->assertEquals(2, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_EXPIRED]));

        // Approve user2 to be deleted.
        $user2ctx = \context_user::instance($user2->id);
        $expiredctx = expired_context::get_record(['contextid' => $user2ctx->id]);
        api::set_expired_context_status($expiredctx, expired_context::STATUS_APPROVED);
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_APPROVED]));

        // Delete expired contexts.
        $deleted = $expired->delete();
        $this->assertEquals(1, $deleted);
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_EXPIRED]));
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_CLEANED]));

        // No new records are generated.
        $numexpired = $expired->flag_expired();
        $this->assertEquals(0, $numexpired);
        $this->assertEquals(2, $DB->count_records('tool_dataprivacy_ctxexpired'));
        $deleted = $expired->delete();
        $this->assertEquals(0, $deleted);

        // No user data left in mod_assign.
        $this->assertEquals(0, $DB->count_records('assign_submission'));

        // The user is deleted.
        $deleteduser = \core_user::get_user($user2->id, 'id, deleted', IGNORE_MISSING);
        $this->assertEquals(1, $deleteduser->deleted);
    }

    /**
     * Test expired course and course stuff flagging and deletion.
     *
     * @return null
     */
    public function test_expired_course_related_contexts() {
        global $DB;

        $purpose1 = api::create_purpose((object)['name' => 'p1', 'retentionperiod' => 'PT1H', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $purpose2 = api::create_purpose((object)['name' => 'p1', 'retentionperiod' => 'P1000Y', 'lawfulbases' => 'gdpr_art_6_1_b']);
        $cat = api::create_category((object)['name' => 'a']);

        $record = (object)[
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $cat->get('id'),
            'contextlevel' => CONTEXT_SYSTEM,
        ];
        api::set_contextlevel($record);

        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_COURSE)
        );
        set_config($purposevar, $purpose1->get('id'), 'tool_dataprivacy');

        // A lot more time for modules.
        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_MODULE)
        );
        set_config($purposevar, $purpose2->get('id'), 'tool_dataprivacy');

        $course1 = $this->getDataGenerator()->create_course();

        // Old course.
        $course2 = $this->getDataGenerator()->create_course(['startdate' => '1', 'enddate' => '2']);
        $forum1 = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id));

        // We want to override this last module instance purpose so we can test that modules are also
        // returned as expired.
        $forum2ctx = \context_module::instance($forum2->cmid);
        $record = (object)[
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $cat->get('id'),
            'contextid' => $forum2ctx->id,
        ];
        api::set_context_instance($record);

        // Ongoing course.
        $course3 = $this->getDataGenerator()->create_course(['startdate' => '1', 'enddate' => time()]);
        $forum3 = $this->getDataGenerator()->create_module('forum', array('course' => $course3->id));

        $expired = new \tool_dataprivacy\expired_course_related_contexts();
        $numexpired = $expired->flag_expired();

        // Only 1 module has expired.
        $this->assertEquals(1, $numexpired);
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_EXPIRED]));

        // Add a forum1 override to 1h retention period so both forum1 and course2 are also expired.
        $forum1ctx = \context_module::instance($forum1->cmid);
        $record->purposeid = $purpose1->get('id');
        $record->contextid = $forum1ctx->id;
        api::set_context_instance($record);
        $numexpired = $expired->flag_expired();
        $this->assertEquals(2, $numexpired);
        $this->assertEquals(3, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_EXPIRED]));

        // Approve forum1 to be deleted.
        $expiredctx = expired_context::get_record(['contextid' => $forum1ctx->id]);
        api::set_expired_context_status($expiredctx, expired_context::STATUS_APPROVED);

        // Delete expired contexts.
        $deleted = $expired->delete();
        $this->assertEquals(1, $deleted);
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_CLEANED]));

        $expiredctx = expired_context::get_record(['contextid' => $forum2ctx->id]);
        api::set_expired_context_status($expiredctx, expired_context::STATUS_APPROVED);

        $course2ctx = \context_course::instance($course2->id);
        $expiredctx = expired_context::get_record(['contextid' => $course2ctx->id]);
        api::set_expired_context_status($expiredctx, expired_context::STATUS_APPROVED);

        // Delete expired contexts.
        $deleted = $expired->delete();
        $this->assertEquals(2, $deleted);
        $this->assertEquals(3, $DB->count_records('tool_dataprivacy_ctxexpired', ['status' => expired_context::STATUS_CLEANED]));

        // No new records are generated.
        $numexpired = $expired->flag_expired();
        $this->assertEquals(0, $numexpired);
        $this->assertEquals(3, $DB->count_records('tool_dataprivacy_ctxexpired'));
        $deleted = $expired->delete();
        $this->assertEquals(0, $deleted);

    }
}

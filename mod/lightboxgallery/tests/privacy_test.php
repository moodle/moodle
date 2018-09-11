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
 * @package    mod_lightboxgallery
 * @category   test
 * @author     Adam Olley <adam.olley@blackboard.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use mod_lightboxgallery\privacy\provider;

require_once($CFG->dirroot . '/mod/lightboxgallery/lib.php');

/**
 * Data provider testcase class.
 *
 * @package    mod_lightboxgallery
 * @category   test
 * @author     Adam Olley <adam.olley@blackboard.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lightboxgallery_privacy_testcase extends provider_testcase {

    public function setUp() {
        global $PAGE;
        $this->resetAfterTest();
        $PAGE->get_renderer('core');
    }

    public function test_get_contexts_for_userid() {
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $cm1a = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1b = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1c = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm2a = $dg->create_module('lightboxgallery', ['course' => $c2]);
        $cm2b = $dg->create_module('lightboxgallery', ['course' => $c2]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->create_comment($cm1a->id, $u1->id, 'cm1a_u1');
        $this->create_comment($cm2a->id, $u1->id, 'cm2a_u1');
        $this->create_comment($cm2b->id, $u1->id, 'cm2b_u1');

        $this->create_comment($cm1a->id, $u2->id, 'cm1a_u2');
        $this->create_comment($cm1b->id, $u2->id, 'cm1a_u2');
        $this->create_comment($cm1c->id, $u2->id, 'cm1c_u2');

        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertTrue(in_array(context_module::instance($cm1a->cmid)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($cm2a->cmid)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($cm2b->cmid)->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertTrue(in_array(context_module::instance($cm1a->cmid)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($cm1b->cmid)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($cm1c->cmid)->id, $contextids));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $cm1a = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1b = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1c = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->create_comment($cm1a->id, $u1->id, 'cm1a_u1');
        $this->create_comment($cm1a->id, $u2->id, 'cm1a_u2');
        $this->create_comment($cm1b->id, $u2->id, 'cm1b_u2');
        $this->create_comment($cm1c->id, $u1->id, 'cm1c_u1');

        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1c->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1b->id]));

        // Deleting the course does nothing.
        provider::delete_data_for_all_users_in_context(context_course::instance($c1->id));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1c->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1b->id]));

        provider::delete_data_for_all_users_in_context(context_module::instance($cm1c->cmid));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1a->id]));
        $this->assertFalse($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1c->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1b->id]));

        provider::delete_data_for_all_users_in_context(context_module::instance($cm1a->cmid));
        $this->assertFalse($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1a->id]));
        $this->assertFalse($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1c->id]));
        $this->assertFalse($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1b->id]));
    }

    public function test_delete_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $cm1a = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1b = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1c = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->create_comment($cm1a->id, $u1->id, 'cm1a_u1');
        $this->create_comment($cm1a->id, $u2->id, 'cm1a_u2');
        $this->create_comment($cm1b->id, $u2->id, 'cm1b_u2');
        $this->create_comment($cm1c->id, $u1->id, 'cm1c_u1');

        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1c->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1b->id]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'mod_lightboxgallery', [
            context_course::instance($c1->id)->id,
            context_module::instance($cm1a->cmid)->id,
            context_module::instance($cm1b->cmid)->id,
        ]));
        $this->assertFalse($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u1->id, 'gallery' => $cm1c->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1a->id]));
        $this->assertTrue($DB->record_exists('lightboxgallery_comments', ['userid' => $u2->id, 'gallery' => $cm1b->id]));
    }

    public function test_export_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $cm1a = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1b = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $cm1c = $dg->create_module('lightboxgallery', ['course' => $c1]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1actx = context_module::instance($cm1a->cmid);
        $cm1bctx = context_module::instance($cm1b->cmid);
        $cm1cctx = context_module::instance($cm1c->cmid);

        $this->create_comment($cm1a->id, $u1->id, 'cm1a_u1');
        $this->create_comment($cm1a->id, $u2->id, 'cm1a_u2');
        $this->create_comment($cm1b->id, $u2->id, 'cm1b_u2');
        $this->create_comment($cm1c->id, $u1->id, 'cm1c_u1');

        provider::export_user_data(new approved_contextlist($u1, 'mod_lightboxgallery', [$cm1actx->id, $cm1bctx->id, $cm1cctx->id]));

        $data = writer::with_context($cm1actx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assert_exported_comments($data->comments, $u1, $cm1a);

        $data = writer::with_context($cm1bctx)->get_data([]);
        $this->assertEmpty($data);

        $data = writer::with_context($cm1cctx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assert_exported_comments($data->comments, $u1, $cm1c);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_lightboxgallery', [$cm1actx->id, $cm1bctx->id, $cm1cctx->id]));

        $data = writer::with_context($cm1actx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assert_exported_comments($data->comments, $u2, $cm1a);

        $data = writer::with_context($cm1bctx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assert_exported_comments($data->comments, $u2, $cm1b);

        $data = writer::with_context($cm1cctx)->get_data([]);
        $this->assertEmpty($data);
    }

    protected function assert_exported_comments($comments, $user, $lbg) {
        global $DB;

        $actualcomments = $DB->get_records('lightboxgallery_comments', ['gallery' => $lbg->id, 'userid' => $user->id]);
        $this->assertEquals(count($actualcomments), count($comments));

        // Add all the acutal comments to a list.
        $commentlist = [];
        foreach ($actualcomments as $comment) {
            $commentlist[$comment->commenttext] = true;
        }

        // Unset all the ones we exported, if all were exported correctly, there should be none left.
        foreach ($comments as $comment) {
            unset($commentlist[$comment['commenttext']]);
        }
        $this->assertEmpty($commentlist);
    }

    /**
     * Create comment.
     *
     * @param int $lightboxgalleryid The lightboxgallery ID.
     * @param int $userid The user ID.
     * @param string $commenttext The comment left by the user.
     * @return stdClass
     */
    protected function create_comment($lightboxgalleryid, $userid, $commenttext = '') {
        global $DB;
        $record = (object) [
            'gallery' => $lightboxgalleryid,
            'userid' => $userid,
            'commenttext' => $commenttext,
            'timemodified' => time()
        ];
        $record->id = $DB->insert_record('lightboxgallery_comments', $record);
        return $record;
    }

}

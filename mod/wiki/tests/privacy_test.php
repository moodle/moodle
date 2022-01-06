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
 * @package    mod_wiki
 * @category   test
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use mod_wiki\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;

require_once($CFG->dirroot.'/mod/wiki/locallib.php');

/**
 * Data provider testcase class.
 *
 * @package    mod_wiki
 * @category   test
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_wiki_privacy_testcase extends provider_testcase {

    /** @var array */
    protected $users = [];
    /** @var array */
    protected $pages = [];
    /** @var array */
    protected $contexts = [];
    /** @var array */
    protected $subwikis = [];
    /** @var array */
    protected $pagepaths = [];

    /**
     * Set up for each test.
     *
     * There are three users and four wikis.
     * 1 : collaborative wiki, has context $this->contexts[1] and has a single subwiki $this->subwikis[1]
     * 2 : individual wiki, has context $this->contexts[2] and three subwikis (one for each user):
     *        $this->subwikis[21], $this->subwikis[22], $this->subwikis[23],
     *        the subwiki for the third user is empty
     * 3 : collaborative wiki, has context $this->contexts[3] and has a single subwiki $this->subwikis[3]
     * 4 : collaborative wiki, has context $this->contexts[4], this wiki is empty
     *
     * Each subwiki (except for "23") has pages, for example, in $this->subwiki[1] there are pages
     *   $this->pages[1][1], $this->pages[1][2] and $this->pages[1][3]
     *   In the export data they have paths:
     *   $this->pagepaths[1][1], $this->pagepaths[1][2], $this->pagepaths[1][3]
     */
    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();

        $this->users[1] = $dg->create_user();
        $this->users[2] = $dg->create_user();
        $this->users[3] = $dg->create_user();
        $this->users[4] = $dg->create_user();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($this->users[1]->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->users[2]->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->users[3]->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->users[4]->id, $course->id, $studentrole->id, 'manual');

        $cm1 = $this->getDataGenerator()->create_module('wiki', ['course' => $course->id]);
        $cm2 = $this->getDataGenerator()->create_module('wiki', ['course' => $course->id, 'wikimode' => 'individual']);
        $cm3 = $this->getDataGenerator()->create_module('wiki', ['course' => $course->id]);
        $cm4 = $this->getDataGenerator()->create_module('wiki', ['course' => $course->id]); // Empty.

        // User1.
        $this->setUser($this->users[1]);

        // Create and modify pages in collaborative wiki.
        $this->pages[1][1] = $this->create_first_page($cm1);
        $this->pages[1][2] = $this->create_page($cm1, ['content' => 'initial content']);
        $this->update_page($cm1, $this->pages[1][2], ['content' => 'update1 <img src="@@PLUGINFILE@@/Dog%20jump.jpg">']);
        $this->attach_file($cm1, "Dog jump.jpg", 'jpg:Doggy');
        $this->update_page($cm1, $this->pages[1][2], ['content' => 'update2']);

        // Create pages in individual wiki, add files that are not used in text.
        $this->pages[21][1] = $this->create_first_page($cm2);
        $this->pages[21][2] = $this->create_page($cm2);
        $this->attach_file($cm2, "mycat.jpg", 'jpg:Cat');

        // User2.
        $this->setUser($this->users[2]);

        // Modify existing pages in the first collaborative wiki.
        $this->update_page($cm1, $this->pages[1][2], ['content' => 'update3 <img src="@@PLUGINFILE@@/Hamster.jpg">']);
        $this->attach_file($cm1, "Hamster.jpg", 'jpg:Hamster');

        // Create pages in individual wiki.
        $this->pages[22][1] = $this->create_first_page($cm2);
        $this->pages[22][2] = $this->create_page($cm2);

        // Create pages in the third wiki.
        $this->pages[3][1] = $this->create_first_page($cm3);

        // User3 (testing locks and empty subwiki).
        $this->setUser($this->users[3]);

        // Create a subwiki in the individual wiki without any pages.
        $subwiki23 = $dg->get_plugin_generator('mod_wiki')->get_subwiki($cm2);

        // Create a page in the first wiki and then lock it.
        $this->pages[1][3] = $this->create_page($cm1);
        wiki_set_lock($this->pages[1][3]->id, $this->users[3]->id, null, true);

        // Lock a page in the third wiki without having any revisions on it.
        wiki_set_lock($this->pages[3][1]->id, $this->users[3]->id, null, true);

        // User 4 - added to the first wiki, so all users are not part of all edited contexts.
        $this->setUser($this->users[4]);
        $this->pages[1][4] = $this->create_page($cm1);

        $this->subwikis = [
            1 => $this->pages[1][1]->subwikiid,
            21 => $this->pages[21][1]->subwikiid,
            22 => $this->pages[22][1]->subwikiid,
            23 => $subwiki23,
            3 => $this->pages[3][1]->subwikiid,
        ];

        $this->contexts = [
            1 => context_module::instance($cm1->cmid),
            2 => context_module::instance($cm2->cmid),
            3 => context_module::instance($cm3->cmid),
            4 => context_module::instance($cm4->cmid),
        ];

        $this->pagepaths = [
            1 => [
                1 => $this->pages[1][1]->id . ' ' . $this->pages[1][1]->title,
                2 => $this->pages[1][2]->id . ' ' . $this->pages[1][2]->title,
                3 => $this->pages[1][3]->id . ' ' . $this->pages[1][3]->title,
                4 => $this->pages[1][4]->id . ' ' . $this->pages[1][4]->title,
            ],
            21 => [
                1 => $this->pages[21][1]->id . ' ' . $this->pages[21][1]->title,
                2 => $this->pages[21][2]->id . ' ' . $this->pages[21][2]->title,
            ],
            22 => [
                1 => $this->pages[22][1]->id . ' ' . $this->pages[22][1]->title,
                2 => $this->pages[22][2]->id . ' ' . $this->pages[22][2]->title,
            ],
            3 => [
                1 => $this->pages[3][1]->id . ' ' . $this->pages[3][1]->title,
            ]
        ];
    }

    /**
     * Generate first page in wiki as current user
     *
     * @param stdClass $wiki
     * @param array $record
     * @return mixed
     */
    protected function create_first_page($wiki, $record = []) {
        $dg = $this->getDataGenerator();
        $wg = $dg->get_plugin_generator('mod_wiki');
        return $wg->create_first_page($wiki, $record);
    }

    /**
     * Generate a page in wiki as current user
     *
     * @param stdClass $wiki
     * @param array $record
     * @return mixed
     */
    protected function create_page($wiki, $record = []) {
        $dg = $this->getDataGenerator();
        $wg = $dg->get_plugin_generator('mod_wiki');
        return $wg->create_page($wiki, $record);
    }

    /**
     * Update an existing page in wiki as current user
     *
     * @param stdClass $wiki
     * @param stdClass $page
     * @param array $record
     * @return mixed
     */
    protected function update_page($wiki, $page, $record = []) {
        $dg = $this->getDataGenerator();
        $wg = $dg->get_plugin_generator('mod_wiki');
        return $wg->create_page($wiki, ['title' => $page->title] + $record);
    }

    /**
     * Attach file to a wiki as a current user
     *
     * @param stdClass $wiki
     * @param string $filename
     * @param string $filecontent
     * @return stored_file
     */
    protected function attach_file($wiki, $filename, $filecontent) {
        $dg = $this->getDataGenerator();
        $wg = $dg->get_plugin_generator('mod_wiki');
        $subwikiid = $wg->get_subwiki($wiki);

        $fs = get_file_storage();
        return $fs->create_file_from_string([
            'contextid' => context_module::instance($wiki->cmid)->id,
            'component' => 'mod_wiki',
            'filearea' => 'attachments',
            'itemid' => $subwikiid,
            'filepath' => '/',
            'filename' => $filename,
        ], $filecontent);
    }

    /**
     * Test getting the contexts for a user.
     */
    public function test_get_contexts_for_userid() {

        // Get contexts for the first user.
        $contextids = provider::get_contexts_for_userid($this->users[1]->id)->get_contextids();
        $this->assertEqualsCanonicalizing([
            $this->contexts[1]->id,
            $this->contexts[2]->id,
        ], $contextids);

        // Get contexts for the second user.
        $contextids = provider::get_contexts_for_userid($this->users[2]->id)->get_contextids();
        $this->assertEqualsCanonicalizing([
            $this->contexts[1]->id,
            $this->contexts[2]->id,
            $this->contexts[3]->id,
        ], $contextids);

        // Get contexts for the third user.
        $contextids = provider::get_contexts_for_userid($this->users[3]->id)->get_contextids();
        $this->assertEqualsCanonicalizing([
            $this->contexts[1]->id,
            $this->contexts[2]->id,
            $this->contexts[3]->id,
        ], $contextids);
    }

    /**
     * Test getting the users within a context.
     */
    public function test_get_users_in_context() {
        global $DB;
        $component = 'mod_wiki';

        // Add a comment from user 4 in context 3.
        $this->setUser($this->users[4]);
        $this->add_comment($this->pages[3][1], 'Look at me, getting involved!');

        // Ensure userlist for context 1 contains all users.
        $userlist = new \core_privacy\local\request\userlist($this->contexts[1], $component);
        provider::get_users_in_context($userlist);

        $this->assertCount(4, $userlist);

        $expected = [$this->users[1]->id, $this->users[2]->id, $this->users[3]->id, $this->users[4]->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Ensure userlist for context 2 contains users 1-3 only.
        $userlist = new \core_privacy\local\request\userlist($this->contexts[2], $component);
        provider::get_users_in_context($userlist);

        $this->assertCount(3, $userlist);

        $expected = [$this->users[1]->id, $this->users[2]->id, $this->users[3]->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Ensure userlist for context 3 contains users 2, 3 and 4 only.
        $userlist = new \core_privacy\local\request\userlist($this->contexts[3], $component);
        provider::get_users_in_context($userlist);

        $this->assertCount(3, $userlist);

        $expected = [$this->users[2]->id, $this->users[3]->id, $this->users[4]->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Ensure userlist for context 4 is empty.
        $userlist = new \core_privacy\local\request\userlist($this->contexts[4], $component);
        provider::get_users_in_context($userlist);

        $this->assertEmpty($userlist);
    }

    /**
     * Export data for user 1
     */
    public function test_export_user_data1() {

        // Export all contexts for the first user.
        $contextids = array_values(array_map(function($c) {
            return $c->id;
        }, $this->contexts));
        $appctx = new approved_contextlist($this->users[1], 'mod_wiki', $contextids);
        provider::export_user_data($appctx);

        // First wiki has two pages ever touched by this user.
        $data = writer::with_context($this->contexts[1])->get_related_data([$this->subwikis[1]]);
        $this->assertEquals([
                $this->pagepaths[1][1],
                $this->pagepaths[1][2]
            ], array_keys($data));
        // First page was initially created by this user and all its information is returned to this user.
        $data11 = $data[$this->pagepaths[1][1]];
        $this->assertEquals($this->pages[1][1]->cachedcontent, $data11['page']['cachedcontent']);
        $this->assertNotEmpty($data11['page']['timecreated']);
        // Wiki creates two revisions when page is created, first one with empty content.
        $this->assertEquals(2, count($data11['revisions']));
        $this->assertFalse(array_key_exists('locks', $data11));
        // Only one file is returned that was in the revision made by this user.

        // The second page was last modified by a different user, so userid in the wiki_pages table is different,
        // additional page information is not exported.
        $data12 = $data[$this->pagepaths[1][2]];
        $this->assertFalse(isset($data12['page']['timecreated']));
        // There are two revisions for creating the page and two additional revisions made by this user.
        $this->assertEquals(4, count($data12['revisions']));
        $lastrevision = array_pop($data12['revisions']);
        $this->assertEquals('update2', $lastrevision['content']);

        // There is one file that was used in this user's contents - "Dog face.jpg" and one file in page cachedcontents.
        $files = writer::with_context($this->contexts[1])->get_files([$this->subwikis[1]]);
        $this->assertEqualsCanonicalizing(['Dog jump.jpg', 'Hamster.jpg'], array_keys($files));

        // Second (individual) wiki for the first user, two pages are returned for this user's subwiki.
        $data = writer::with_context($this->contexts[2])->get_related_data([$this->subwikis[21]]);
        $this->assertEquals([
            $this->pagepaths[21][1],
            $this->pagepaths[21][2]
        ], array_keys($data));
        $files = writer::with_context($this->contexts[2])->get_files([$this->subwikis[21]]);
        $this->assertEquals(['mycat.jpg'], array_keys($files));

        // Second (individual) wiki for the first user, nothing is returned for the second user's subwiki.
        $this->assertFalse(writer::with_context($this->contexts[2])->has_any_data([$this->subwikis[22]]));

        // Third wiki for the first user, there were no contributions by the first user.
        $this->assertFalse(writer::with_context($this->contexts[3])->has_any_data([$this->subwikis[3]]));
    }

    /**
     * Test export data for user 2
     */
    public function test_export_user_data2() {

        // Export all contexts for the second user.
        $contextids = array_values(array_map(function($c) {
            return $c->id;
        }, $this->contexts));
        $appctx = new approved_contextlist($this->users[2], 'mod_wiki', $contextids);
        provider::export_user_data($appctx);

        // First wiki - this user only modified the second page.
        $data = writer::with_context($this->contexts[1])->get_related_data([$this->subwikis[1]]);
        $this->assertEquals([
            $this->pagepaths[1][2]
        ], array_keys($data));

        // This user was the last one to modify this page, so the page info is returned.
        $data12 = $data[$this->pagepaths[1][2]];
        $this->assertEquals('update3 <img src="files/Hamster.jpg" alt="Hamster.jpg" />', trim($data12['page']['cachedcontent']));
        // He made one revision.
        $this->assertEquals(1, count($data12['revisions']));
        $lastrevision = reset($data12['revisions']);
        $this->assertEquals('update3 <img src="files/Hamster.jpg">', trim($lastrevision['content']));

        // Only one file was used in the first wiki by this user - Hamster.jpg.
        $files = writer::with_context($this->contexts[1])->get_files([$this->subwikis[1]]);
        $this->assertEquals(['Hamster.jpg'], array_keys($files));

        // Export second (individual) wiki, nothing is returned for the other user's subwiki.
        $this->assertFalse(writer::with_context($this->contexts[2])->has_any_data([$this->subwikis[21]]));

        // Export second (individual) wiki, two pages are returned for this user's subwiki.
        $data = writer::with_context($this->contexts[2])->get_related_data([$this->subwikis[22]]);
        $this->assertEquals([
            $this->pagepaths[22][1],
            $this->pagepaths[22][2]
        ], array_keys($data));
        $files = writer::with_context($this->contexts[2])->get_files([$this->subwikis[22]]);
        $this->assertEmpty($files);

        // Second user made contributions to the third wiki.
        $data = writer::with_context($this->contexts[3])->get_related_data([$this->subwikis[3]]);
        $this->assertEquals([
            $this->pagepaths[3][1]
        ], array_keys($data));
        $files = writer::with_context($this->contexts[3])->get_files([$this->subwikis[3]]);
        $this->assertEmpty($files);
    }

    /**
     * Test export data for user 3 (locks, empty individual wiki)
     */
    public function test_export_user_data3() {

        // Export all contexts for the third user.
        $contextids = array_values(array_map(function($c) {
            return $c->id;
        }, $this->contexts));
        $appctx = new approved_contextlist($this->users[3], 'mod_wiki', $contextids);
        provider::export_user_data($appctx);

        // For the third page of the first wiki there are 2 revisions and 1 lock.
        $data = writer::with_context($this->contexts[1])->get_related_data([$this->subwikis[1]]);
        $this->assertEquals([
            $this->pagepaths[1][3]
        ], array_keys($data));

        $data13 = $data[$this->pagepaths[1][3]];
        $this->assertNotEmpty($data13['page']['timecreated']);
        $this->assertEquals(2, count($data13['revisions']));
        $this->assertEquals(1, count($data13['locks']));
        $files = writer::with_context($this->contexts[1])->get_files([$this->subwikis[1]]);
        $this->assertEmpty($files);

        // Empty individual wiki.
        $this->assertTrue(writer::with_context($this->contexts[2])->has_any_data());
        $data = writer::with_context($this->contexts[2])->get_data([$this->subwikis[23]]);
        $this->assertEquals((object)[
            'groupid' => 0,
            'userid' => $this->users[3]->id
        ], $data);
        $files = writer::with_context($this->contexts[2])->get_files([$this->subwikis[23]]);
        $this->assertEmpty($files);

        // For the third wiki there is no page information, no revisions and one lock.
        $data = writer::with_context($this->contexts[3])->get_related_data([$this->subwikis[3]]);
        $this->assertEquals([
            $this->pagepaths[3][1]
        ], array_keys($data));

        $data31 = $data[$this->pagepaths[3][1]];
        $this->assertTrue(empty($data31['page']['timecreated']));
        $this->assertTrue(empty($data31['revisions']));
        $this->assertEquals(1, count($data31['locks']));

        $files = writer::with_context($this->contexts[3])->get_files([$this->subwikis[3]]);
        $this->assertEmpty($files);

        // No data for the forth wiki.
        $this->assertFalse(writer::with_context($this->contexts[4])->has_any_data());
    }

    /**
     * Creates a comment object
     *
     * @param  stdClass $page
     * @param  string   $text
     * @return comment The comment object.
     */
    protected function add_comment($page, $text) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot . '/comment/lib.php');
        $record = $DB->get_record_sql('SELECT cm.id, cm.course FROM {course_modules} cm
            JOIN {modules} m ON m.name = ? AND m.id = cm.module
            JOIN {wiki} w ON cm.instance = w.id
            JOIN {wiki_subwikis} s ON s.wikiid = w.id
            WHERE s.id=?', ['wiki', $page->subwikiid]);
        $context = context_module::instance($record->id);
        $args = new stdClass;
        $args->context = $context;
        $args->courseid = $record->course;
        $args->area = 'wiki_page';
        $args->itemid = $page->id;
        $args->component = 'mod_wiki';
        $comment = new comment($args);
        $comment->set_post_permission(true);
        $comment->add($text);
        return $comment;
    }

    /**
     * Test export data when there are comments.
     */
    public function test_export_user_data_with_comments() {
        global $DB;
        // Comment on each page in the first wiki as the first user.
        $this->setUser($this->users[1]);
        $this->add_comment($this->pages[1][1], 'Hello111');
        $this->add_comment($this->pages[1][2], 'Hello112');
        $this->add_comment($this->pages[1][3], 'Hello113');

        // Comment on second and third page as the third user.
        $this->setUser($this->users[3]);
        $this->add_comment($this->pages[1][2], 'Hello312');
        $this->add_comment($this->pages[1][3], 'Hello313');

        // Export all contexts for the third user.
        $contextids = array_values(array_map(function($c) {
            return $c->id;
        }, $this->contexts));
        $appctx = new approved_contextlist($this->users[3], 'mod_wiki', $contextids);
        provider::export_user_data($appctx);

        $data = writer::with_context($this->contexts[1])->get_related_data([$this->subwikis[1]]);
        // Now user has two pages (comparing to previous test where he had one).
        $this->assertEquals([
            $this->pagepaths[1][2],
            $this->pagepaths[1][3]
        ], array_keys($data));

        // Page 1-2 was exported and it has one comment that this user made (comment from another user was not exported).
        $data12 = $data[$this->pagepaths[1][2]];
        $this->assertTrue(empty($data12['page']['timecreated']));
        $this->assertTrue(empty($data12['revisions']));
        $this->assertTrue(empty($data12['locks']));
        $this->assertEquals(1, count($data12['page']['comments']));

        // Page 1-3 was exported same way as in the previous test and it has two comments.
        $data13 = $data[$this->pagepaths[1][3]];
        $this->assertNotEmpty($data13['page']['timecreated']);
        $this->assertEquals(2, count($data13['revisions']));
        $this->assertEquals(1, count($data13['locks']));
        $this->assertEquals(2, count($data13['page']['comments']));
    }

    /**
     * Test for delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        provider::delete_data_for_all_users_in_context($this->contexts[1]);

        $appctx = new approved_contextlist($this->users[1], 'mod_wiki',
            [$this->contexts[1]->id, $this->contexts[2]->id]);
        provider::export_user_data($appctx);
        $this->assertFalse(writer::with_context($this->contexts[1])->has_any_data());
        $this->assertTrue(writer::with_context($this->contexts[2])->has_any_data());

        writer::reset();
        $appctx = new approved_contextlist($this->users[2], 'mod_wiki', [$this->contexts[1]->id]);
        provider::export_user_data($appctx);
        $this->assertFalse(writer::with_context($this->contexts[1])->has_any_data());

        writer::reset();
        $appctx = new approved_contextlist($this->users[3], 'mod_wiki', [$this->contexts[1]->id]);
        provider::export_user_data($appctx);
        $this->assertFalse(writer::with_context($this->contexts[1])->has_any_data());
    }

    /**
     * Test for delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        $appctx = new approved_contextlist($this->users[1], 'mod_wiki',
            [$this->contexts[1]->id, $this->contexts[1]->id]);
        provider::delete_data_for_user($appctx);

        provider::export_user_data($appctx);
        $this->assertTrue(writer::with_context($this->contexts[1])->has_any_data());
        $this->assertFalse(writer::with_context($this->contexts[2])->has_any_data());
    }

    /**
     * Test for delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        $component = 'mod_wiki';

        // Ensure data exists within context 2 - individual wikis.
        // Since each user owns their own subwiki in this context, they can be deleted.
        $u1ctx2 = new approved_contextlist($this->users[1], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u1ctx2);
        $u2ctx2 = new approved_contextlist($this->users[2], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u2ctx2);
        $u3ctx2 = new approved_contextlist($this->users[3], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u3ctx2);

        $this->assertTrue(writer::with_context($this->contexts[2])->has_any_data());
        writer::reset();

        // Delete user 1 and 2 data, user 3's wiki still remains.
        $approveduserids = [$this->users[1]->id, $this->users[2]->id];
        $approvedlist = new approved_userlist($this->contexts[2], $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $u1ctx2 = new approved_contextlist($this->users[1], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u1ctx2);
        $u2ctx2 = new approved_contextlist($this->users[2], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u2ctx2);
        $u3ctx2 = new approved_contextlist($this->users[3], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u3ctx2);

        $this->assertTrue(writer::with_context($this->contexts[2])->has_any_data());
        writer::reset();

        // Delete user 3's wiki. All 3 subwikis now deleted, so ensure no data is found in this context.
        $approveduserids = [$this->users[3]->id];
        $approvedlist = new approved_userlist($this->contexts[2], $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $u1ctx2 = new approved_contextlist($this->users[1], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u1ctx2);
        $u2ctx2 = new approved_contextlist($this->users[2], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u2ctx2);
        $u3ctx2 = new approved_contextlist($this->users[3], 'mod_wiki', [$this->contexts[2]->id]);
        provider::export_user_data($u3ctx2);

        $this->assertFalse(writer::with_context($this->contexts[2])->has_any_data());
        writer::reset();

        // Ensure Context 1 still contains data.
        $u1ctx1 = new approved_contextlist($this->users[1], 'mod_wiki', [$this->contexts[1]->id]);
        provider::export_user_data($u1ctx1);
        $u2ctx1 = new approved_contextlist($this->users[2], 'mod_wiki', [$this->contexts[1]->id]);
        provider::export_user_data($u2ctx1);
        $u3ctx1 = new approved_contextlist($this->users[3], 'mod_wiki', [$this->contexts[1]->id]);
        provider::export_user_data($u3ctx1);

        $this->assertTrue(writer::with_context($this->contexts[1])->has_any_data());
    }
}

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
 * Local Tests
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;
use theme_snap\local;
use theme_snap\output\core_renderer;
use theme_snap\user_forums;
use core_component;

/**
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recent_forum_activity_test extends \advanced_testcase {

    /**
     * @var stdClass
     */
    protected $user1;

    /**
     * @var stdClass
     */
    protected $user2;

    /**
     * @var stdClass
     */
    protected $teacher1;

    /**
     * @var stdClass
     */
    protected $teacher2;

    /**
     * @var stdClass
     */
    protected $course1;

    /**
     * @var stdClass
     */
    protected $course2;

    /**
     * @var stdClass
     */
    protected $group1;

    /**
     * @var stdClass
     */
    protected $group2;

    /**
     * checks if specific plugin is present and enabled
     * @param $component - plugin component
     * @return bool
     */
    protected function plugin_present($component) {
        list($type, $plugin) = core_component::normalize_component($component);
        $plugins = \core_plugin_manager::instance()->get_enabled_plugins($type);
        return in_array($plugin, $plugins);
    }

    /**
     * Pre-requisites for tests.
     * @throws \coding_exception
     */
    public function setUp():void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $this->resetAfterTest();

        $this->course1 = $this->getDataGenerator()->create_course();
        $this->course2 = $this->getDataGenerator()->create_course();

        $this->user1 = $this->getDataGenerator()->create_user();
        $this->user2 = $this->getDataGenerator()->create_user();
        $this->teacher1 = $this->getDataGenerator()->create_user();
        $this->teacher2 = $this->getDataGenerator()->create_user();

        // Enrol (as students) user1 to both courses but user2 only to course2.
        $sturole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($this->user1->id,
            $this->course1->id,
            $sturole->id);
        $this->getDataGenerator()->enrol_user($this->user1->id,
            $this->course2->id,
            $sturole->id);
        $this->getDataGenerator()->enrol_user($this->user2->id,
            $this->course2->id,
            $sturole->id);

        // Enrol teachers on both courses.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $teachers = [$this->teacher2, $this->teacher1];
        foreach ($teachers as $teacher) {
            $this->getDataGenerator()->enrol_user($teacher->id,
                $this->course1->id,
                $teacherrole->id);
            $this->getDataGenerator()->enrol_user($teacher->id,
                $this->course2->id,
                $teacherrole->id);
        }

        // Add 2 groups to course2.
        $this->group1 = $this->getDataGenerator()->create_group([
            'courseid' => $this->course2->id,
            'name' => 'Group 1',
        ]);
        $this->group2 = $this->getDataGenerator()->create_group([
            'courseid' => $this->course2->id,
            'name' => 'Group 2',
        ]);

        // Add user1 to both groups but user2 to just group1.
        groups_add_member($this->group1->id, $this->user1);
        groups_add_member($this->group2->id, $this->user1);
        groups_add_member($this->group1->id, $this->user2);
    }

    /**
     * Test single discussion.
     * @throws \coding_exception
     */
    public function test_forum_discussion_simple($ftype = 'forum', $toffset = 0, $u1offset = 0, $u2offset = 0) {

        if ($ftype == 'hsuforum' && !$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        // If this is not a combined test then check and make sure there is no activity (nothing done yet).
        if ($toffset === 0 && $u1offset === 0 && $u2offset === 0) {
            $activity = local::recent_forum_activity($this->teacher2->id);
            $this->assertEmpty($activity);
            $activity = local::recent_forum_activity($this->user1->id);
            $this->assertEmpty($activity);
            $activity = local::recent_forum_activity($this->user2->id);
            $this->assertEmpty($activity);
        }

        $record = new \stdClass();
        $record->course = $this->course1->id;
        $forum = $this->getDataGenerator()->create_module($ftype, $record);

        // Add discussion to course 1 started by teacher1.
        // Note: In testing number of posts, discussions are counted too as there is a post for each discussion created.
        $this->create_discussion($ftype, $this->course1->id, $this->teacher1->id, $forum->id);

        // Check teacher1 viewable posts is 0 as no point seeing your own posts.
        $this->assert_user_activity($this->teacher1, 0);

        // Check teacher2 viewable posts is 1.
        $this->assert_user_activity($this->teacher2, $toffset + 1);

        // Check user1 viewable posts is 1.
        $this->assert_user_activity($this->user1, $u1offset + 1);

        // Check user2 viewable posts is 0 (user2 is not enrolled in course1).
        $this->assert_user_activity($this->user2, $u2offset + 0);
    }

    /**
     * Test hsuforum single discussion.
     */
    public function test_hsuforum_discussion_simple() {
        $this->test_forum_discussion_simple('hsuforum');
    }

    /**
     * Test hsuforum single discussion.
     */
    public function test_combined_discussion_simple() {
        $this->test_forum_discussion_simple('forum');
        $this->test_forum_discussion_simple('hsuforum', 1, 1, 0);
    }

    /**
     * Test single discussion + post.
     *
     * @param string $ftype
     * @param int $toffset
     * @param int $u1offset
     * @param int $u2offset
     * @throws \coding_exception
     */
    public function test_forum_post_simple($ftype = 'forum', $toffset = 0, $u1offset = 0, $u2offset = 0) {

        if ($ftype == 'hsuforum' && !$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        $record = new \stdClass();
        $record->course = $this->course1->id;
        $forum1 = $this->getDataGenerator()->create_module($ftype, $record);

        // Add discussion to course 1 started by user1.
        // Note: In testing number of posts, discussions are counted too as there is a post for each discussion created.
        $discussion1 = $this->create_discussion($ftype, $this->course1->id, $this->teacher1->id, $forum1->id);
        $this->create_post($ftype, $this->course1->id, $this->teacher1->id, $forum1->id, $discussion1->id);
        $this->create_post($ftype, $this->course1->id, $this->teacher1->id, $forum1->id, $discussion1->id,
            ['modified' => time() - (13 * WEEKSECS)]);

        // Check teacher viewable posts is 2.
        $this->assert_user_activity($this->teacher2, $toffset + 2);

        // Check user1 viewable posts is 2.
        $this->assert_user_activity($this->user1, $u1offset + 2);

        // Check user2 viewable posts is 0 (user2 is not enrolled in course1).
        $this->assert_user_activity($this->user2, $u2offset + 0);

        // Create a forum and discussion in course2 so that user2 can see it.
        $record = new \stdClass();
        $record->course = $this->course2->id;
        $forum2 = $this->getDataGenerator()->create_module($ftype, $record);
        $discussion2 = $this->create_discussion($ftype, $this->course2->id, $this->teacher1->id, $forum2->id);
        $this->create_post($ftype, $this->course2->id, $this->teacher1->id, $forum2->id, $discussion2->id);

        // Check teacher viewable posts is 4.
        $this->assert_user_activity($this->teacher2, $toffset + 4);

        // Check user1 viewable posts is 4.
        $this->assert_user_activity($this->user1, $u1offset + 4);

        // Check user2 viewable posts is 2 (user2 can only see posts in course2).
        $this->assert_user_activity($this->user2, $u2offset + 2);
    }

    /**
     * Test hsuforum single discussion + post.
     */
    public function test_hsuforum_post_simple() {
        $this->test_forum_post_simple('hsuforum');
    }

    /**
     * Test forum & hsuforum combined single discussion + post.
     */
    public function test_combined_post_simple() {
        $this->test_forum_post_simple('forum');
        $this->test_forum_post_simple('hsuforum', 4, 4, 2);
    }

    /**
     * @param string $ftype
     *
     * @throws \coding_exception
     */
    public function test_forum_high_volume_posts($ftype = 'forum') {
        global $DB;

        if ($ftype == 'hsuforum' && !$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        $forums = [];

        // Teacher count.
        $teacherc = 0;

        // User 1 count.
        $user1c = 0;

        // User 2 count.
        $user2c = 0;

        $sturole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        $totalcourses = 20;
        $totalforums = 0;
        $forumspercourse = 40;
        $discussionsperforum = 20;

        for ($c = 0; $c < $totalcourses; $c++) {

            // Create course.
            $tmpcourse = $this->getDataGenerator()->create_course();

            // Enrol user1 as student.
            $this->getDataGenerator()->enrol_user($this->user1->id,
                $tmpcourse->id,
                $sturole->id);

            // Enrol user2 as student to the first 10 courses.
            if ($c < 10) {
                $this->getDataGenerator()->enrol_user($this->user2->id,
                    $tmpcourse->id,
                    $sturole->id);
            }

            // Enrol teacher 1 and teacher 2.
            $this->getDataGenerator()->enrol_user($this->teacher1->id,
                $tmpcourse->id,
                $teacherrole->id);
            $this->getDataGenerator()->enrol_user($this->teacher2->id,
                $tmpcourse->id,
                $teacherrole->id);

            // Log user visited course for each user.
            $users = [$this->teacher1, $this->teacher2, $this->user1, $this->user2];
            foreach ($users as $user) {
                $eventparams = [
                    'userid' => $user->id,
                    'context' => \context_course::instance($tmpcourse->id),
                ];
                $event = \core\event\course_viewed::create($eventparams);
                $event->trigger();
            }

            // All discussions and posts are made by teacher2, so that the count for teacher1, user1
            // and user2 are not affected by not being able to see their own posts.
            for ($f = 0; $f < $forumspercourse; $f++) {
                $totalforums++;
                $record = new \stdClass();
                $record->course = $tmpcourse->id;
                $forums[$f] = $this->getDataGenerator()->create_module($ftype, $record);
                for ($d = 0; $d < $discussionsperforum; $d++) {
                    $discussion = $this->create_discussion($ftype, $tmpcourse->id, $this->teacher2->id, $forums[$f]->id);
                    $teacherc++;
                    $user1c++;
                    if ($c < $discussionsperforum / 2) {
                        $user2c++;
                    }
                    $post = $this->create_post($ftype, $tmpcourse->id, $this->teacher2->id, $forums[$f]->id, $discussion->id);
                    $teacherc++;
                    $user1c++;
                    if ($c < $discussionsperforum / 2) {
                        $user2c++;
                    }
                    $this->create_reply($ftype, $this->teacher2->id, $post);
                    $teacherc++;
                    $user1c++;
                    if ($c < $discussionsperforum / 2) {
                        $user2c++;
                    }
                }
            }
        }

        $start = microtime(true);

        // Check teacher viewable posts.
        $starttchl10 = microtime(true);
        $this->assert_user_activity($this->teacher1, 10);
        $timetchl10 = microtime(true) - $starttchl10;

        // Check user1 viewable posts.
        $startu1l10 = microtime(true);
        $this->assert_user_activity($this->user1, 10);
        $timeu1l10 = microtime(true) - $startu1l10;

        // Check user2 viewable posts.
        $startu2l10 = microtime(true);
        $this->assert_user_activity($this->user2, 10);
        $timeu2l10 = microtime(true) - $startu2l10;

        // Forum limit when getting recent activity.
        $maxforums = user_forums::$forumlimit;
        $xforums = $totalforums > $maxforums ? $maxforums : $totalforums;
        $maxposts = ($xforums * $discussionsperforum) * 3; // 3 posts per discussion.

        $xteacherc = $teacherc > $maxposts ? $maxposts : $teacherc;
        $xuser1c = $user1c > $maxposts ? $maxposts : $user1c;
        $xuser2c = $user2c > $maxposts ? $maxposts : $user2c;

        // Check teacher viewable posts.
        $starttchnl = microtime(true);
        $this->assert_user_activity($this->teacher1, $xteacherc, 0);
        $timetchnl = microtime(true) - $starttchnl;

        // Check user1 viewable posts.
        $startu1nl = microtime(true);
        $this->assert_user_activity($this->user1, $xuser1c, 0);
        $timeu1nl = microtime(true) - $startu1nl;

        // Check user2 viewable posts.
        $startu2nl = microtime(true);
        $this->assert_user_activity($this->user2, $xuser2c, 0);
        $timeu2nl = microtime(true) - $startu2nl;

        $end = microtime(true);
    }

    public function test_hsuforum_high_volume_posts() {
        self::test_forum_high_volume_posts('hsuforum');
    }

    /**
     * Test an anonymous Open Forum with one anonymous discussion & reply.
     * @throws \coding_exception
     */
    public function test_hsuforum_anonymous() {

        if (!$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        $record = new \stdClass();
        $record->course = $this->course1->id;
        $record->anonymous = 1;

        $forum1 = $this->getDataGenerator()->create_module('hsuforum', $record);

        // Add discussion to course 1 started by teacher1.
        // Note: In testing number of posts, discussions are counted too as there is a post for each discussion created.
        $discussion1 = $this->create_discussion('hsuforum', $this->course1->id, $this->teacher1->id, $forum1->id);
        $this->create_post('hsuforum', $this->course1->id, $this->teacher1->id, $forum1->id, $discussion1->id);

        // Note - with an anonymous forum, none of the posts should be included in recent activity.

        // Check teacher2 viewable posts is 0.
        $this->assert_user_activity($this->teacher2, 0);

        // Check user1 viewable posts is 0.
        $this->assert_user_activity($this->user1, 0);

        // Check user2 viewable posts is 0 (user2 is not enrolled in course1).
        $this->assert_user_activity($this->user2, 0);
    }

    /**
     * Test qanda forum.
     *
     * @param string $ftype
     */
    public function test_forum_qanda($ftype = 'forum') {

        if ($ftype == 'hsuforum' && !$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        $record = new \stdClass();
        $record->course = $this->course1->id;
        $record->type = 'qanda';

        $forum1 = $this->getDataGenerator()->create_module($ftype, $record);

        // Add discussion to course 1 started by teacher.
        // Note: In testing number of posts, discussions are counted too as there is a post for each discussion created.
        $discussion1 = $this->create_discussion($ftype, $this->course1->id, $this->teacher1->id, $forum1->id);

        // Make a post by teacher1.
        $this->create_post($ftype, $this->course1->id, $this->teacher1->id, $forum1->id, $discussion1->id);

        // Nobody should see any forum activity if the forum is a qanda forum.

        // Check teacher2 viewable posts is 0.
        $this->assert_user_activity($this->teacher2, 0);

        // Check user1 viewable posts is 0.
        $this->assert_user_activity($this->user1, 0);

        // Check user2 viewable posts is 0.
        $this->assert_user_activity($this->user2, 0);
    }

    /**
     * Test qanda Open Forum.
     */
    public function test_hsuforum_qanda() {
        self::test_forum_qanda('hsuforum');
    }

    /**
     * Test qanda forum & Open Forum combined.
     */
    public function test_combined_qanda() {
        self::test_forum_qanda('forum');
        self::test_forum_qanda('hsuforum');
    }

    /**
     * Test an Open Forum with one private reply.
     * @throws \coding_exception
     */
    public function test_hsuforum_private() {
        if (!$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }
        $record = new \stdClass();
        $record->course = $this->course2->id; // Use course 2 so that both user1 and user2 can access it.
        $record->allowprivatereplies = 1;

        $forum1 = $this->getDataGenerator()->create_module('hsuforum', $record);

        // Add discussion to course 2 started by teacher1.
        // Note: In testing number of posts, discussions are counted too as there is a post for each discussion created.
        $discussion1 = $this->create_discussion('hsuforum', $this->course2->id, $this->teacher1->id, $forum1->id);

        // Make a regular post by teacher.
        $parent = $this->create_post('hsuforum', $this->course2->id, $this->teacher1->id, $forum1->id, $discussion1->id);

        // Make a private reply from user1 to teacher1.
        $this->create_reply('hsuforum', $this->user1->id, $parent, ['privatereply' => $this->teacher1->id]);

        // Check teacher1 viewable posts is 1 (can see private reply but not their own post).
        $this->assert_user_activity($this->teacher1, 1);

        // Check user1 viewable posts is 2 (user 1 can see the private reply but not their own post).
        $this->assert_user_activity($this->user1, 2);

        // Check user2 viewable posts is 2 (user2 can see the discussion and first post but not the private reply).
        $this->assert_user_activity($this->user2, 2);
    }

    /**
     * Test a date restricted forum
     *
     * @param string $ftype
     * @param int $toffset
     * @param int $u1offset
     * @param int $u2offset
     */
    public function test_forum_restricted($ftype = 'forum', $toffset = 0, $u1offset = 0, $u2offset = 0) {
        global $CFG;

        if ($ftype == 'hsuforum' && !$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        // This is crucial - without this you can't make a conditionally accsesed forum.
        $CFG->enableavailability = true;

        // Create a date restricted forum - won't be available to students until one week from now.
        $record = new \stdClass();
        $record->course = $this->course2->id;
        $opts = ['availability' => '{"op":"&","c":[{"type":"date","d":">=","t":'.(time() + WEEKSECS).'}],"showc":[true]}'];
        $record->availability = $opts['availability'];
        $forum = $this->getDataGenerator()->create_module($ftype, $record, $opts);

        // Add discussion to date restricted forum.
        $discussion = $this->create_discussion($ftype, $this->course2->id, $this->teacher1->id, $forum->id);
        $this->create_post($ftype, $this->course2->id, $this->teacher1->id, $forum->id, $discussion->id);

        // Check teacher2 viewable posts is 2.
        $this->assert_user_activity($this->teacher2, $toffset + 2);

        // Check user1 viewable posts is 0 - can't see anything in restricted forum.
        $this->assert_user_activity($this->user1, $u1offset + 0);

        // Check user2 viewable posts is 0 - can't see anything in restricted forum.
        $this->assert_user_activity($this->user2, $u2offset + 0);
    }

    /**
     * Test hsuforum single discussion + post.
     */
    public function test_hsuforum_restricted() {
        $this->test_forum_restricted('hsuforum');
    }

    /**
     * Test forum & hsuforum combined single discussion + post.
     */
    public function test_combined_restricted() {
        $this->test_forum_restricted('forum');
        $this->test_forum_restricted('hsuforum', 2, 0, 0);
    }

    /**
     * Test forum posts restricted by group.
     *
     * @param string $ftype
     * @param int $toffset
     * @param int $u1offset
     * @param int $u2offset
     */
    public function test_forum_group_posts($ftype = 'forum', $toffset = 0, $u1offset = 0, $u2offset = 0) {

        if ($ftype == 'hsuforum' && !$this->plugin_present('hsuforum')) {
            $this->markTestSkipped('Skipped test, mod_hsuforum is not installed.');
        }

        // Create a forum with group mode enabled.
        $record = new \stdClass();
        $record->course = $this->course2->id;
        $forum = $this->getDataGenerator()->create_module($ftype, $record, ['groupmode' => SEPARATEGROUPS]);

        // Add a discussion and 2 posts for group1 users.
        $discussion1 = $this->create_discussion($ftype,
            $this->course2->id, $this->teacher1->id, $forum->id,  ['groupid' => $this->group1->id]);

        for ($p = 1; $p <= 2; $p++) {
            // Create 1 post by user1 and user2.
            $user = $p == 1 ? $this->user1 : $this->user2;
            $this->create_post($ftype, $this->course2->id, $user->id, $forum->id, $discussion1->id);
        }

        // Add a discussion and 1 post for group2 users.
        $discussion2 = $this->create_discussion($ftype,
            $this->course2->id, $this->teacher1->id, $forum->id,  ['groupid' => $this->group2->id]);
        $this->create_post($ftype, $this->course2->id, $this->teacher1->id, $forum->id, $discussion2->id);

        // Check teacher viewable posts is 5 (can view all posts).
        $this->assert_user_activity($this->teacher2, $toffset + 5);

        // Check user1 viewable posts is 4 (in all groups, can view all posts except their own).
        $this->assert_user_activity($this->user1, $u1offset + 4);

        // Check user2 viewable posts is 2 (only in group2 and can't see their own posts).
        $this->assert_user_activity($this->user2, $u2offset + 2);
    }

    /**
     * Test hsuforum posts restricted by group.
     */
    public function test_hsuforum_group_posts() {
        $this->test_forum_group_posts('hsuforum');
    }

    /**
     * Test forum & hsuforum combined  posts restricted by group.
     */
    public function test_combined_group_posts() {
        $this->test_forum_group_posts('forum');
        $this->test_forum_group_posts('hsuforum', 5, 4, 2);
    }

    /**
     * Test site news date on front page
     */
    public function test_site_news_date() {
        global $SITE, $DB;
        $this->resetAfterTest();

        $forum = forum_get_course_forum($SITE->id, 'news');
        $user = $this->getDataGenerator()->create_user();
        $courseid = 1; // Id for home "course".
        $discussion = $this->create_discussion('forum', $courseid, $user->id, $forum->id);

        // Set new Moodle Page and set context.
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());

        $target = null;
        $renderer = new core_renderer($page, $target);
        $output = $renderer->site_frontpage_news();
        $dateparsed = userdate($discussion->timemodified, get_string('strftimedatetime', 'langconfig'));
        $this->assertStringContainsString($dateparsed, $output);

        $newtimestamp = time();
        $updatediscussion = new \stdClass();
        $updatediscussion->id = $discussion->id;
        $updatediscussion->timemodified = $newtimestamp;
        $DB->update_record('forum_discussions', $updatediscussion);

        $output = $renderer->site_frontpage_news();
        $this->assertStringContainsString(userdate($newtimestamp, get_string('strftimedatetime', 'langconfig')), $output);
    }

    /**
     * Create a discussion.
     *
     * @param $ftype
     * @param $courseid
     * @param $userid
     * @param $forumid
     * @return mixed
     * @throws \coding_exception
     */
    protected function create_discussion($ftype, $courseid, $userid, $forumid, Array $opts = []) {
        // Add discussion to course 1 started by user1.
        $record = new \stdClass();
        $record->course = $courseid;
        $record->userid = $userid;
        $record->forum = $forumid;
        if (!empty($opts)) {
            foreach ($opts as $key => $val) {
                $record->$key = $val;
            }
        }
        return ($this->getDataGenerator()->get_plugin_generator('mod_'.$ftype)->create_discussion($record));
    }

    /**
     * Create a post.
     *
     * @param $ftype
     * @param $courseid
     * @param $userid
     * @param $forumid
     * @param $discussionid
     * @return mixed
     * @throws \coding_exception
     */
    protected function create_post($ftype, $courseid, $userid, $forumid, $discussionid, Array $opts = []) {
        $record = new \stdClass();
        $record->course = $courseid;
        $record->userid = $userid;
        $record->forum = $forumid;
        $record->discussion = $discussionid;
        if (!empty($opts)) {
            foreach ($opts as $key => $val) {
                $record->$key = $val;
            }
        }
        return ($this->getDataGenerator()->get_plugin_generator('mod_'.$ftype)->create_post($record));
    }

    /**
     * Create a reply to a post.
     * @param string $ftype
     * @param int $userid
     * @param stdClass $parent
     * @param array $opts
     */
    protected function create_reply($ftype, $userid, $parent, Array $opts = []) {
        $opts = array_merge($opts, ['parent' => $parent->id]);
        $this->create_post($ftype, $parent->course, $userid, $parent->forum, $parent->discussion,
            $opts);
    }

    /**
     * Assert user activity.
     * @param stdClass $user
     * @param int $expected
     * @param int $limit
     */
    protected function assert_user_activity($user, $expected, $limit = 10) {
        $activity = local::recent_forum_activity($user->id, $limit);
        // Ensure number of activity items matched.
        $this->assertEquals($expected, count($activity));
        if (!empty($activity)) {
            // Ensure first activity item contains expected fields.
            $activityitem = $activity[0];
            $this->assertNotEmpty($activityitem->user);
            $this->assertNotEmpty($activityitem->type);
            $this->assertNotEmpty($activityitem->content);
            $this->assertNotEmpty($activityitem->content->discussion);
            $this->assertNotEmpty($activityitem->content->id);
            $this->assertNotEmpty($activityitem->timestamp);
            $this->assertNotEmpty($activityitem->courseshortname);
            $this->assertNotEmpty($activityitem->forumname);
            $this->assertNotEmpty($activityitem->content->subject);
        }
    }

}

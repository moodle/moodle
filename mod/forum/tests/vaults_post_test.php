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
 * The post vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/generator_trait.php');

/**
 * The post vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_forum\local\vaults\post
 */
class mod_forum_vaults_post_testcase extends advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /** @var \mod_forum\local\vaults\post */
    private $vault;

    /**
     * Set up function for tests.
     */
    public function setUp() {
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $this->vault = $vaultfactory->get_post_vault();
    }

    /**
     * Teardown for all tests.
     */
    public function tearDown() {
        unset($this->vault);
    }

    /**
     * Test get_from_id.
     */
    public function test_get_from_id() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion, $post] = $this->helper_post_to_forum($forum, $user);

        $postentity = $this->vault->get_from_id($post->id);

        $this->assertEquals($post->id, $postentity->get_id());
    }

    /**
     * Test get_from_discussion_id.
     *
     * @covers ::get_from_discussion_id
     * @covers ::<!public>
     */
    public function test_get_from_discussion_id() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        [$discussion2, $post4] = $this->helper_post_to_forum($forum, $user);

        $entities = array_values($this->vault->get_from_discussion_id($user, $discussion1->id, false));

        $this->assertCount(3, $entities);
        $this->assertEquals($post1->id, $entities[0]->get_id());
        $this->assertEquals($post2->id, $entities[1]->get_id());
        $this->assertEquals($post3->id, $entities[2]->get_id());

        $entities = array_values($this->vault->get_from_discussion_id($user, $discussion1->id + 1000, false));
        $this->assertCount(0, $entities);
    }

    /**
     * Ensure that selecting posts in a discussion only returns posts that the user can see, when considering private
     * replies.
     *
     * @covers ::get_from_discussion_id
     * @covers ::<!public>
     */
    public function test_get_from_discussion_id_private_replies() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');
        [$discussion, $post] = $this->helper_post_to_forum($forum, $teacher);
        $reply = $this->helper_post_to_discussion($forum, $discussion, $teacher, [
                'privatereplyto' => $student->id,
            ]);

        // The user is the author.
        $entities = array_values($this->vault->get_from_discussion_id($teacher, $discussion->id, true));
        $this->assertCount(2, $entities);
        $this->assertEquals($post->id, $entities[0]->get_id());
        $this->assertEquals($reply->id, $entities[1]->get_id());

        // The user is the intended recipient.
        $entities = array_values($this->vault->get_from_discussion_id($student, $discussion->id, false));
        $this->assertCount(2, $entities);
        $this->assertEquals($post->id, $entities[0]->get_id());
        $this->assertEquals($reply->id, $entities[1]->get_id());

        // The user is another teacher..
        $entities = array_values($this->vault->get_from_discussion_id($otherteacher, $discussion->id, true));
        $this->assertCount(2, $entities);
        $this->assertEquals($post->id, $entities[0]->get_id());
        $this->assertEquals($reply->id, $entities[1]->get_id());

        // The user is a different student.
        $entities = array_values($this->vault->get_from_discussion_id($otherstudent, $discussion->id, false));
        $this->assertCount(1, $entities);
        $this->assertEquals($post->id, $entities[0]->get_id());
    }

    /**
     * Test get_from_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_from_discussion_ids
     */
    public function test_get_from_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_from_discussion_ids($user, [], false));
    }

    /**
     * Test get_from_discussion_ids.
     *
     * @covers ::get_from_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_from_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        [$discussion2, $post4] = $this->helper_post_to_forum($forum, $user);

        $entities = $this->vault->get_from_discussion_ids($user, [$discussion1->id], false);
        $this->assertCount(3, $entities);
        $this->assertArrayHasKey($post1->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($post2->id, $entities);
        $this->assertArrayHasKey($post3->id, $entities);

        $entities = $this->vault->get_from_discussion_ids($user, [$discussion1->id, $discussion2->id], false);
        $this->assertCount(4, $entities);
        $this->assertArrayHasKey($post1->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($post2->id, $entities);
        $this->assertArrayHasKey($post3->id, $entities);
        $this->assertArrayHasKey($post4->id, $entities);

        // Test ordering by id descending.
        $entities = $this->vault->get_from_discussion_ids($user, [$discussion1->id, $discussion2->id], false, 'id DESC');
        $this->assertEquals($post4->id, array_values($entities)[0]->get_id());
        $this->assertEquals($post3->id, array_values($entities)[1]->get_id());
        $this->assertEquals($post2->id, array_values($entities)[2]->get_id());
        $this->assertEquals($post1->id, array_values($entities)[3]->get_id());

        // Test ordering by id ascending.
        $entities = $this->vault->get_from_discussion_ids($user, [$discussion1->id, $discussion2->id], false, 'id ASC');
        $this->assertEquals($post1->id, array_values($entities)[0]->get_id());
        $this->assertEquals($post2->id, array_values($entities)[1]->get_id());
        $this->assertEquals($post3->id, array_values($entities)[2]->get_id());
        $this->assertEquals($post4->id, array_values($entities)[3]->get_id());
    }

    /**
     * Ensure that selecting posts in a discussion only returns posts that the user can see, when considering private
     * replies.
     *
     * @covers ::get_from_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_from_discussion_ids_private_replies() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');

        // Create the posts structure below.
        // Forum:
        // -> Post (student 1)
        // ---> Post private reply (teacher 1)
        // -> Otherpost (teacher 1)
        // ---> Otherpost private reply (teacher 2)
        // ---> Otherpost reply (student 1)
        // ----> Otherpost reply private reply (teacher 1).
        [$discussion, $post] = $this->helper_post_to_forum($forum, $student);
        $postprivatereply = $this->helper_reply_to_post($post, $teacher, [
            'privatereplyto' => $student->id
        ]);
        [$otherdiscussion, $otherpost] = $this->helper_post_to_forum($forum, $teacher);
        $otherpostprivatereply = $this->helper_reply_to_post($otherpost, $otherteacher, [
            'privatereplyto' => $teacher->id,
        ]);
        $otherpostreply = $this->helper_reply_to_post($otherpost, $student);
        $otherpostreplyprivatereply = $this->helper_reply_to_post($otherpostreply, $teacher, [
            'privatereplyto' => $student->id
        ]);

        // Teacher 1. Request all posts from the vault, telling the vault that the teacher CAN see private replies made by anyone.
        $entities = $this->vault->get_from_discussion_ids($teacher, [$discussion->id, $otherdiscussion->id], true);
        $this->assertCount(6, $entities);
        $this->assertArrayHasKey($post->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Student 1. Request all posts from the vault, telling the vault that the student CAN'T see private replies made by anyone.
        // Teacher2's private reply to otherpost is omitted.
        $entities = $this->vault->get_from_discussion_ids($student, [$discussion->id, $otherdiscussion->id], false);
        $this->assertCount(5, $entities);
        $this->assertArrayHasKey($post->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostreply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Student 1. Request all posts from the vault, telling the vault that student CAN see all private replies made.
        // The private reply made by teacher 2 to otherpost is now included.
        $entities = $this->vault->get_from_discussion_ids($student, [$discussion->id, $otherdiscussion->id], true);
        $this->assertCount(6, $entities);
        $this->assertArrayHasKey($post->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Teacher 2. Request all posts from the vault, telling the vault that teacher2 CAN see all private replies made.
        $entities = $this->vault->get_from_discussion_ids($otherteacher, [$discussion->id, $otherdiscussion->id], true);
        $this->assertCount(6, $entities);
        $this->assertArrayHasKey($post->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Teacher 2. Request all posts from the vault, telling the vault that teacher2 CANNOT see all private replies made.
        // The private replies not relating to teacher 2 directly are omitted.
        $entities = $this->vault->get_from_discussion_ids($otherteacher, [$discussion->id, $otherdiscussion->id], false);
        $this->assertCount(4, $entities);
        $this->assertArrayHasKey($post->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreply->id, $entities);

        // Student 2. Request all posts from the vault, telling the vault that student2 CAN'T see all private replies made.
        // All private replies are omitted, as none relate to student2.
        $entities = $this->vault->get_from_discussion_ids($otherstudent, [$discussion->id, $otherdiscussion->id], false);
        $this->assertCount(3, $entities);
        $this->assertArrayHasKey($post->id, $entities); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostreply->id, $entities);
    }

    /**
     * Test get_replies_to_post.
     *
     * @covers ::get_replies_to_post
     * @covers ::<!public>
     */
    public function test_get_replies_to_post() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $forumgenerator = $datagenerator->get_plugin_generator('mod_forum');
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        // Create a post with the same created time as the parent post to ensure
        // we've covered every possible scenario.
        $post2 = $forumgenerator->create_post((object) [
            'discussion' => $post1->discussion,
            'parent' => $post1->id,
            'userid' => $user->id,
            'mailnow' => 1,
            'subject' => 'Some subject',
            'created' => $post1->created
        ]);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);

        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $post1 = $entityfactory->get_post_from_stdclass($post1);
        $post2 = $entityfactory->get_post_from_stdclass($post2);
        $post3 = $entityfactory->get_post_from_stdclass($post3);
        $post4 = $entityfactory->get_post_from_stdclass($post4);

        $entities = $this->vault->get_replies_to_post($user, $post1, false);
        $this->assertCount(3, $entities);
        $this->assertEquals($post2->get_id(), $entities[0]->get_id());
        $this->assertEquals($post3->get_id(), $entities[1]->get_id());
        $this->assertEquals($post4->get_id(), $entities[2]->get_id());

        $entities = $this->vault->get_replies_to_post($user, $post2, false);
        $this->assertCount(1, $entities);
        $this->assertEquals($post4->get_id(), $entities[0]->get_id());

        $entities = $this->vault->get_replies_to_post($user, $post3, false);
        $this->assertCount(0, $entities);
    }

    /**
     * Test get_replies_to_post with private replies.
     *
     * @covers ::get_replies_to_post
     * @covers ::<!public>
     */
    public function test_get_replies_to_post_private_replies() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        // Generate a structure:
        // Initial post p [student]
        // -> Reply pa [otherstudent]
        // ---> Reply paa [student]
        // ---> Private Reply pab [teacher]
        // -> Private Reply pb [teacher]
        // -> Reply pc [otherstudent]
        // ---> Reply pca [student]
        // -----> Reply pcaa [otherstudent]
        // -------> Private Reply pcaaa [teacher].

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');

        [$discussion, $p] = $this->helper_post_to_forum($forum, $student);

        $pa = $this->helper_reply_to_post($p, $otherstudent);
        $paa = $this->helper_reply_to_post($pa, $student);
        $pab = $this->helper_reply_to_post($pa, $teacher, ['privatereplyto' => $otherstudent->id]);

        $pb = $this->helper_reply_to_post($p, $teacher, ['privatereplyto' => $student->id]);

        $pc = $this->helper_reply_to_post($p, $otherteacher);
        $pca = $this->helper_reply_to_post($pc, $student);
        $pcaa = $this->helper_reply_to_post($pca, $otherstudent);
        $pcaaa = $this->helper_reply_to_post($pcaa, $teacher, ['privatereplyto' => $otherstudent->id]);

        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $ep = $entityfactory->get_post_from_stdclass($p);
        $epa = $entityfactory->get_post_from_stdclass($pa);
        $epaa = $entityfactory->get_post_from_stdclass($paa);
        $epab = $entityfactory->get_post_from_stdclass($pab);
        $epb = $entityfactory->get_post_from_stdclass($pb);
        $epc = $entityfactory->get_post_from_stdclass($pc);
        $epca = $entityfactory->get_post_from_stdclass($pca);
        $epcaa = $entityfactory->get_post_from_stdclass($pcaa);
        $epcaaa = $entityfactory->get_post_from_stdclass($pcaaa);

        // As `student`, you should see all public posts, plus all private replies intended for you.
        $entities = $this->vault->get_replies_to_post($student, $ep, false);
        $this->assertCount(6, $entities);
        $this->assertEquals($epa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epb->get_id(), $entities[2]->get_id());
        $this->assertEquals($epc->get_id(), $entities[3]->get_id());
        $this->assertEquals($epca->get_id(), $entities[4]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[5]->get_id());

        $entities = $this->vault->get_replies_to_post($student, $epa, false);
        $this->assertCount(1, $entities);
        $this->assertEquals($epaa->get_id(), $entities[0]->get_id());

        $this->assertEmpty($this->vault->get_replies_to_post($student, $epaa, false));
        $this->assertEmpty($this->vault->get_replies_to_post($student, $epab, false));
        $this->assertEmpty($this->vault->get_replies_to_post($student, $epb, false));
        $this->assertEmpty($this->vault->get_replies_to_post($student, $epcaa, false));
        $this->assertEmpty($this->vault->get_replies_to_post($student, $epcaaa, false));

        $entities = $this->vault->get_replies_to_post($student, $epc, false);
        $this->assertCount(2, $entities);
        $this->assertEquals($epca->get_id(), $entities[0]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[1]->get_id());

        // As `otherstudent`, you should see all public posts, plus all private replies intended for you.
        $entities = $this->vault->get_replies_to_post($otherstudent, $ep, false);
        $this->assertCount(7, $entities);
        $this->assertEquals($epa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epab->get_id(), $entities[2]->get_id());
        $this->assertEquals($epc->get_id(), $entities[3]->get_id());
        $this->assertEquals($epca->get_id(), $entities[4]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[5]->get_id());
        $this->assertEquals($epcaaa->get_id(), $entities[6]->get_id());

        $entities = $this->vault->get_replies_to_post($otherstudent, $epa, false);
        $this->assertCount(2, $entities);
        $this->assertEquals($epaa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epab->get_id(), $entities[1]->get_id());

        $this->assertEmpty($this->vault->get_replies_to_post($otherstudent, $epaa, false));
        $this->assertEmpty($this->vault->get_replies_to_post($otherstudent, $epab, false));
        $this->assertEmpty($this->vault->get_replies_to_post($otherstudent, $epb, false));
        $this->assertEmpty($this->vault->get_replies_to_post($otherstudent, $epcaaa, false));

        $entities = $this->vault->get_replies_to_post($otherstudent, $epc, false);
        $this->assertCount(3, $entities);
        $this->assertEquals($epca->get_id(), $entities[0]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epcaaa->get_id(), $entities[2]->get_id());

        // The teacher who authored the private replies can see all.
        $entities = $this->vault->get_replies_to_post($teacher, $ep, true);
        $this->assertCount(8, $entities);
        $this->assertEquals($epa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epab->get_id(), $entities[2]->get_id());
        $this->assertEquals($epb->get_id(), $entities[3]->get_id());
        $this->assertEquals($epc->get_id(), $entities[4]->get_id());
        $this->assertEquals($epca->get_id(), $entities[5]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[6]->get_id());
        $this->assertEquals($epcaaa->get_id(), $entities[7]->get_id());

        $entities = $this->vault->get_replies_to_post($teacher, $epa, true);
        $this->assertCount(2, $entities);
        $this->assertEquals($epaa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epab->get_id(), $entities[1]->get_id());

        $this->assertEmpty($this->vault->get_replies_to_post($teacher, $epaa, true));
        $this->assertEmpty($this->vault->get_replies_to_post($teacher, $epab, true));
        $this->assertEmpty($this->vault->get_replies_to_post($teacher, $epb, true));
        $this->assertEmpty($this->vault->get_replies_to_post($teacher, $epcaaa, true));

        $entities = $this->vault->get_replies_to_post($teacher, $epc, true);
        $this->assertCount(3, $entities);
        $this->assertEquals($epca->get_id(), $entities[0]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epcaaa->get_id(), $entities[2]->get_id());

        // Any other teacher can also see all.
        $entities = $this->vault->get_replies_to_post($otherteacher, $ep, true);
        $this->assertCount(8, $entities);
        $this->assertEquals($epa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epab->get_id(), $entities[2]->get_id());
        $this->assertEquals($epb->get_id(), $entities[3]->get_id());
        $this->assertEquals($epc->get_id(), $entities[4]->get_id());
        $this->assertEquals($epca->get_id(), $entities[5]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[6]->get_id());
        $this->assertEquals($epcaaa->get_id(), $entities[7]->get_id());

        $entities = $this->vault->get_replies_to_post($otherteacher, $epa, true);
        $this->assertCount(2, $entities);
        $this->assertEquals($epaa->get_id(), $entities[0]->get_id());
        $this->assertEquals($epab->get_id(), $entities[1]->get_id());

        $this->assertEmpty($this->vault->get_replies_to_post($otherteacher, $epaa, true));
        $this->assertEmpty($this->vault->get_replies_to_post($otherteacher, $epab, true));
        $this->assertEmpty($this->vault->get_replies_to_post($otherteacher, $epb, true));
        $this->assertEmpty($this->vault->get_replies_to_post($otherteacher, $epcaaa, true));

        $entities = $this->vault->get_replies_to_post($otherteacher, $epc, true);
        $this->assertCount(3, $entities);
        $this->assertEquals($epca->get_id(), $entities[0]->get_id());
        $this->assertEquals($epcaa->get_id(), $entities[1]->get_id());
        $this->assertEquals($epcaaa->get_id(), $entities[2]->get_id());
    }

    /**
     * Test get_reply_count_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_reply_count_for_discussion_ids
     */
    public function test_get_reply_count_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertCount(0, $this->vault->get_reply_count_for_discussion_ids($user, [], false));
    }

    /**
     * Test get_reply_count_for_discussion_ids.
     *
     * @covers ::get_reply_count_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_reply_count_for_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);
        [$discussion3, $post7] = $this->helper_post_to_forum($forum, $user);

        $counts = $this->vault->get_reply_count_for_discussion_ids($user, [$discussion1->id], false);
        $this->assertCount(1, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids($user, [$discussion1->id, $discussion2->id], false);
        $this->assertCount(2, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);
        $this->assertEquals(1, $counts[$discussion2->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids($user, [
            $discussion1->id,
            $discussion2->id,
            $discussion3->id
        ], false);
        $this->assertCount(2, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);
        $this->assertEquals(1, $counts[$discussion2->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids($user, [
            $discussion1->id,
            $discussion2->id,
            $discussion3->id,
            $discussion3->id + 1000
        ], false);
        $this->assertCount(2, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);
        $this->assertEquals(1, $counts[$discussion2->id]);
    }

    /**
     * Test get_reply_count_for_discussion_ids.
     *
     * @covers ::get_reply_count_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_reply_count_for_discussion_ids_private_replies() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        // Generate a structure:
        // Initial post p [student]
        // -> Reply pa [otherstudent]
        // ---> Reply paa [student]
        // ---> Private Reply pab [teacher]
        // -> Private Reply pb [teacher]
        // -> Reply pc [otherstudent]
        // ---> Reply pca [student]
        // -----> Reply pcaa [otherstudent]
        // -------> Private Reply pcaaa [teacher].

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');

        [$discussion, $p] = $this->helper_post_to_forum($forum, $student);

        $pa = $this->helper_reply_to_post($p, $otherstudent);
        $paa = $this->helper_reply_to_post($pa, $student);
        $pab = $this->helper_reply_to_post($pa, $teacher, ['privatereplyto' => $otherstudent->id]);

        $pb = $this->helper_reply_to_post($p, $teacher, ['privatereplyto' => $student->id]);

        $pc = $this->helper_reply_to_post($p, $otherteacher);
        $pca = $this->helper_reply_to_post($pc, $student);
        $pcaa = $this->helper_reply_to_post($pca, $otherstudent);
        $pcaaa = $this->helper_reply_to_post($pcaa, $teacher, ['privatereplyto' => $otherstudent->id]);

        $this->assertEquals([$discussion->id => 6],
            $this->vault->get_reply_count_for_discussion_ids($student, [$discussion->id], false));
        $this->assertEquals([$discussion->id => 7],
            $this->vault->get_reply_count_for_discussion_ids($otherstudent, [$discussion->id], false));
        $this->assertEquals([$discussion->id => 8],
            $this->vault->get_reply_count_for_discussion_ids($teacher, [$discussion->id], true));
        $this->assertEquals([$discussion->id => 8],
            $this->vault->get_reply_count_for_discussion_ids($otherteacher, [$discussion->id], true));
    }

    /**
     * Test get_reply_count_for_discussion_id.
     *
     * @covers ::get_reply_count_for_post_id_in_discussion_id
     * @covers ::<!public>
     */
    public function test_get_reply_count_for_post_id_in_discussion_id() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);
        [$discussion3, $post7] = $this->helper_post_to_forum($forum, $user);

        $this->assertEquals(3,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($user, $post1->id, $discussion1->id, false));
        $this->assertEquals(1,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($user, $post5->id, $discussion2->id, false));
        $this->assertEquals(0,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($user, $post7->id, $discussion3->id, false));
        $this->assertEquals(0,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($user, $post7->id + 1000, $discussion3->id, false));
    }

    /**
     * Test get_reply_count_for_post_id_in_discussion_id.
     *
     * @covers ::get_reply_count_for_post_id_in_discussion_id
     * @covers ::<!public>
     */
    public function test_get_reply_count_for_post_id_in_discussion_id_private_replies() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        // Generate a structure:
        // Initial post p [student]
        // -> Reply pa [otherstudent]
        // ---> Reply paa [student]
        // ---> Private Reply pab [teacher]
        // -> Private Reply pb [teacher]
        // -> Reply pc [otherstudent]
        // ---> Reply pca [student]
        // -----> Reply pcaa [otherstudent]
        // -------> Private Reply pcaaa [teacher].

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');

        [$discussion, $p] = $this->helper_post_to_forum($forum, $student);

        $pa = $this->helper_reply_to_post($p, $otherstudent);
        $paa = $this->helper_reply_to_post($pa, $student);
        $pab = $this->helper_reply_to_post($pa, $teacher, ['privatereplyto' => $otherstudent->id]);

        $pb = $this->helper_reply_to_post($p, $teacher, ['privatereplyto' => $student->id]);

        $pc = $this->helper_reply_to_post($p, $otherteacher);
        $pca = $this->helper_reply_to_post($pc, $student);
        $pcaa = $this->helper_reply_to_post($pca, $otherstudent);
        $pcaaa = $this->helper_reply_to_post($pcaa, $teacher, ['privatereplyto' => $otherstudent->id]);

        $this->assertEquals(6,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($student, $p->id, $discussion->id, false));
        $this->assertEquals(7,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($otherstudent, $p->id, $discussion->id, false));
        $this->assertEquals(8,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($teacher, $p->id, $discussion->id, true));
        $this->assertEquals(8,
            $this->vault->get_reply_count_for_post_id_in_discussion_id($otherteacher, $p->id, $discussion->id, true));
    }

    /**
     * Test get_unread_count_for_discussion_ids.
     *
     * @covers ::get_unread_count_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_unread_count_for_discussion_ids() {
        global $CFG;
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $otheruser = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);

        $modgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $post7 = $modgenerator->create_post((object) [
            'discussion' => $post5->discussion,
            'parent' => $post5->id,
            'userid' => $user->id,
            'mailnow' => 1,
            'subject' => 'old post',
            // Two days ago which makes it an "old post".
            'modified' => time() - 172800
        ]);

        forum_tp_add_read_record($user->id, $post1->id);
        forum_tp_add_read_record($user->id, $post4->id);
        $CFG->forum_oldpostdays = 1;

        $counts = $this->vault->get_unread_count_for_discussion_ids($user, [$discussion1->id], false);
        $this->assertCount(1, $counts);
        $this->assertEquals(2, $counts[$discussion1->id]);

        $counts = $this->vault->get_unread_count_for_discussion_ids($user, [$discussion1->id, $discussion2->id], false);
        $this->assertCount(2, $counts);
        $this->assertEquals(2, $counts[$discussion1->id]);
        $this->assertEquals(2, $counts[$discussion2->id]);

        $counts = $this->vault->get_unread_count_for_discussion_ids($user, [
            $discussion1->id,
            $discussion2->id,
            $discussion2->id + 1000
        ], false);
        $this->assertCount(2, $counts);
        $this->assertEquals(2, $counts[$discussion1->id]);
        $this->assertEquals(2, $counts[$discussion2->id]);

        $counts = $this->vault->get_unread_count_for_discussion_ids($otheruser, [$discussion1->id, $discussion2->id], false);
        $this->assertCount(2, $counts);
        $this->assertEquals(4, $counts[$discussion1->id]);
        $this->assertEquals(2, $counts[$discussion2->id]);
    }

    /**
     * Test get_unread_count_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_unread_count_for_discussion_ids
     */
    public function test_get_unread_count_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_unread_count_for_discussion_ids($user, [], false));
    }

    /**
     * Test get_latest_posts_for_discussion_ids.
     *
     * @covers ::get_latest_posts_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_latest_posts_for_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $course = $datagenerator->create_course();
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');
        [$user, $user2] = $this->helper_create_users($course, 2, 'student');
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);
        [$discussion3, $post7] = $this->helper_post_to_forum($forum, $user);
        $post8 = $this->helper_post_to_discussion($forum, $discussion3, $teacher, [
            'privatereplyto' => $user->id,
        ]);

        $ids = $this->vault->get_latest_posts_for_discussion_ids($user, [$discussion1->id], false);
        $this->assertCount(1, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]->get_id());

        $ids = $this->vault->get_latest_posts_for_discussion_ids($user,
            [$discussion1->id, $discussion2->id], false);
        $this->assertCount(2, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]->get_id());
        $this->assertEquals($post6->id, $ids[$discussion2->id]->get_id());

        $ids = $this->vault->get_latest_posts_for_discussion_ids($user,
            [$discussion1->id, $discussion2->id, $discussion3->id], false);
        $this->assertCount(3, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]->get_id());
        $this->assertEquals($post6->id, $ids[$discussion2->id]->get_id());
        $this->assertEquals($post8->id, $ids[$discussion3->id]->get_id());

        // Checks the user who doesn't have access to the private reply.
        $ids = $this->vault->get_latest_posts_for_discussion_ids($user2,
            [$discussion1->id, $discussion2->id, $discussion3->id], false);
        $this->assertCount(3, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]->get_id());
        $this->assertEquals($post6->id, $ids[$discussion2->id]->get_id());
        $this->assertEquals($post7->id, $ids[$discussion3->id]->get_id());

        // Checks the user with the private reply to.
        $ids = $this->vault->get_latest_posts_for_discussion_ids($user, [
            $discussion1->id,
            $discussion2->id,
            $discussion3->id,
            $discussion3->id + 1000
        ], false);
        $this->assertCount(3, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]->get_id());
        $this->assertEquals($post6->id, $ids[$discussion2->id]->get_id());
        $this->assertEquals($post8->id, $ids[$discussion3->id]->get_id());
    }

    /**
     * Test get_latest_posts_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_latest_posts_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_latest_posts_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_latest_posts_for_discussion_ids($user, [], false));
    }

    /**
     * Test get_first_post_for_discussion_ids.
     *
     * @covers ::get_first_post_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_first_post_for_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);
        [$discussion3, $post7] = $this->helper_post_to_forum($forum, $user);

        $firstposts = $this->vault->get_first_post_for_discussion_ids([$discussion1->id]);
        $this->assertCount(1, $firstposts);
        $this->assertEquals($post1->id, reset($firstposts)->get_id());

        $firstposts = $this->vault->get_first_post_for_discussion_ids([$discussion1->id, $discussion2->id]);
        $this->assertCount(2, $firstposts);
        $this->assertEquals($post1->id, $firstposts[$post1->id]->get_id());
        $this->assertEquals($post5->id, $firstposts[$post5->id]->get_id());

        $firstposts = $this->vault->get_first_post_for_discussion_ids([$discussion1->id, $discussion2->id, $discussion3->id]);
        $this->assertCount(3, $firstposts);
        $this->assertEquals($post1->id, $firstposts[$post1->id]->get_id());
        $this->assertEquals($post5->id, $firstposts[$post5->id]->get_id());
        $this->assertEquals($post7->id, $firstposts[$post7->id]->get_id());

        $firstposts = $this->vault->get_first_post_for_discussion_ids([
            $discussion1->id,
            $discussion2->id,
            $discussion3->id,
            $discussion3->id + 1000
        ]);
        $this->assertCount(3, $firstposts);
        $this->assertEquals($post1->id, $firstposts[$post1->id]->get_id());
        $this->assertEquals($post5->id, $firstposts[$post5->id]->get_id());
        $this->assertEquals($post7->id, $firstposts[$post7->id]->get_id());
    }

    /**
     * Test get_first_post_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_first_post_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_first_post_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_first_post_for_discussion_ids([]));
    }

    /**
     * Test get_from_discussion_ids_and_user_ids.
     *
     * @covers ::get_from_discussion_ids_and_user_ids
     * @covers ::<!public>
     */
    public function test_get_from_discussion_ids_and_user_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $course = $datagenerator->create_course();
        [$user, $user2] = $this->helper_create_users($course, 2, 'student');
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);

        [$discussion2, $post4] = $this->helper_post_to_forum($forum, $user);
        $discussionids = [$discussion1->id, $discussion2->id];

        $userids = [$user->id];
        $entities = array_values($this->vault->get_from_discussion_ids_and_user_ids($user,
            $discussionids,
            $userids,
            true,
            'id ASC'));

        $this->assertCount(4, $entities);
        $this->assertEquals($post1->id, $entities[0]->get_id());
        $this->assertEquals($post2->id, $entities[1]->get_id());
        $this->assertEquals($post3->id, $entities[2]->get_id());
        $this->assertEquals($post4->id, $entities[3]->get_id());

        $entities = $this->vault->get_from_discussion_ids_and_user_ids($user, [$discussion1->id], $userids, false);
        $this->assertCount(3, $entities);
        $this->assertArrayHasKey($post1->id, $entities);
        $this->assertArrayHasKey($post2->id, $entities);
        $this->assertArrayHasKey($post3->id, $entities);

        $entities = $this->vault->get_from_discussion_ids_and_user_ids($user, [$discussion1->id, $discussion2->id],
                [$user->id, $user2->id], false);
        $this->assertCount(4, $entities);
        $this->assertArrayHasKey($post1->id, $entities);
        $this->assertArrayHasKey($post2->id, $entities);
        $this->assertArrayHasKey($post3->id, $entities);
        $this->assertArrayHasKey($post4->id, $entities);

        // Test ordering by id descending.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($user, [$discussion1->id, $discussion2->id],
                [$user->id], false, 'id DESC');
        $this->assertEquals($post4->id, array_values($entities)[0]->get_id());
        $this->assertEquals($post3->id, array_values($entities)[1]->get_id());
        $this->assertEquals($post2->id, array_values($entities)[2]->get_id());
        $this->assertEquals($post1->id, array_values($entities)[3]->get_id());

        // Test ordering by id ascending.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($user, [$discussion1->id, $discussion2->id],
                [$user->id], false, 'id ASC');
        $this->assertEquals($post1->id, array_values($entities)[0]->get_id());
        $this->assertEquals($post2->id, array_values($entities)[1]->get_id());
        $this->assertEquals($post3->id, array_values($entities)[2]->get_id());
        $this->assertEquals($post4->id, array_values($entities)[3]->get_id());
    }

    /**
     * Test get_from_discussion_ids_and_user_ids when no discussion ids were provided.
     *
     * @covers ::get_from_discussion_ids_and_user_ids
     */
    public function test_get_from_discussion_ids_and_user_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $course = $datagenerator->create_course();
        [$student1, $student2] = $this->helper_create_users($course, 2, 'student');
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion, $post] = $this->helper_post_to_forum($forum, $student1);
        $this->assertEquals([], $this->vault->get_from_discussion_ids_and_user_ids($student1, [], [], false));
        $this->assertEquals([], $this->vault->get_from_discussion_ids_and_user_ids($student1, [$discussion->id], [], false));
        $this->assertEquals([], $this->vault->get_from_discussion_ids_and_user_ids($student1, [], [$student2->id], false));
    }

    /**
     * Ensure that selecting posts in a discussion only returns posts that the user can see, when considering private
     * replies.
     *
     * @covers ::get_from_discussion_ids_and_user_ids
     * @covers ::<!public>
     */
    public function test_get_from_discussion_ids_and_user_ids_private_replies() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');

        // Create the posts structure below.
        // Forum:
        // -> Post (student 1)
        // ---> Post private reply (teacher 1)
        // -> Otherpost (teacher 1)
        // ---> Otherpost private reply (teacher 2)
        // ---> Otherpost reply (student 1)
        // ----> Otherpost reply private reply (teacher 1).
        [$discussion, $post] = $this->helper_post_to_forum($forum, $student);
        $postprivatereply = $this->helper_reply_to_post($post, $teacher, [
            'privatereplyto' => $student->id
        ]);
        [$otherdiscussion, $otherpost] = $this->helper_post_to_forum($forum, $teacher);
        $otherpostprivatereply = $this->helper_reply_to_post($otherpost, $otherteacher, [
            'privatereplyto' => $teacher->id,
        ]);
        $otherpostreply = $this->helper_reply_to_post($otherpost, $student);
        $otherpostreplyprivatereply = $this->helper_reply_to_post($otherpostreply, $teacher, [
            'privatereplyto' => $student->id
        ]);

        $userids = [$otherstudent->id, $teacher->id, $otherteacher->id];
        $discussionids = [$discussion->id, $otherdiscussion->id];

        // Teacher 1. Request all posts from the vault, telling the vault that the teacher CAN see private replies made by anyone.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($teacher, $discussionids, $userids, true);
        $this->assertCount(4, $entities);
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Student 1. Request all posts from the vault, telling the vault that the student CAN'T see private replies made by anyone.
        // Teacher2's private reply to otherpost is omitted.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($student, $discussionids, $userids, false);
        $this->assertCount(3, $entities);
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Student 1. Request all posts from the vault, telling the vault that student CAN see all private replies made.
        // The private reply made by teacher 2 to otherpost is now included.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($student, $discussionids, $userids, true);
        $this->assertCount(4, $entities);
        $this->assertArrayHasKey($postprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Teacher 2. Request all posts from the vault, telling the vault that teacher2 CAN see all private replies made.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($otherteacher, $discussionids, $userids, true);
        $this->assertCount(4, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);
        $this->assertArrayHasKey($otherpostreplyprivatereply->id, $entities);

        // Teacher 2. Request all posts from the vault, telling the vault that teacher2 CANNOT see all private replies made.
        // The private replies not relating to teacher 2 directly are omitted.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($otherteacher, $discussionids, $userids, false);
        $this->assertCount(2, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
        $this->assertArrayHasKey($otherpostprivatereply->id, $entities);

        // Student 2. Request all posts from the vault, telling the vault that student2 CAN'T see all private replies made.
        // All private replies are omitted, as none relate to student2.
        $entities = $this->vault->get_from_discussion_ids_and_user_ids($otherstudent, $discussionids, $userids, false);
        $this->assertCount(1, $entities);
        $this->assertArrayHasKey($otherpost->id, $entities);
    }
}

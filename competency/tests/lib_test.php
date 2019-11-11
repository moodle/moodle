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
 * Lib tests.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_competency\plan;
use core_competency\url;
use core_competency\user_competency;

global $CFG;

/**
 * Lib testcase.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_lib_testcase extends advanced_testcase {

    public function test_comment_add_user_competency() {
        global $DB, $PAGE;
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user(['picture' => 1]);
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $reviewerroleid = $dg->create_role();
        assign_capability('moodle/competency:planview', CAP_ALLOW, $reviewerroleid, context_system::instance()->id, true);
        assign_capability('moodle/competency:usercompetencycomment', CAP_ALLOW, $reviewerroleid,
            context_system::instance()->id, true);
        $dg->role_assign($reviewerroleid, $u2->id, context_user::instance($u1->id));
        $dg->role_assign($reviewerroleid, $u3->id, context_user::instance($u1->id));
        accesslib_clear_all_caches_for_unit_testing();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'))); // In 1 plan.
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'))); // In 2 plans.
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'))); // Orphan.

        $p1 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p1->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $p1->get('id'), 'competencyid' => $c2->get('id')));
        $p2 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p2->get('id'), 'competencyid' => $c2->get('id')));

        $DB->set_field(plan::TABLE, 'timemodified', 1, array('id' => $p1->get('id')));   // Make plan 1 appear as old.
        $p1->read();

        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c1->get('id'),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $u2->id));
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c2->get('id'),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $u2->id));
        $uc3 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c3->get('id'),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $u2->id));

        // Post a comment for the user competency being in one plan. The reviewer is messaged.
        $this->setUser($u1);
        $comment = $uc1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = $c1->get('shortname');
        $expectedurl = url::user_competency_in_plan($u1->id, $c1->get('id'), $p1->get('id'));
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u2->id, $message->useridto);
        $this->assertTrue(strpos($message->fullmessage, 'Hello world!') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, 'Hello world!') !== false);
        $this->assertEquals(FORMAT_MOODLE, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);
        // Test customdata.
        $customdata = json_decode($message->customdata);
        $this->assertObjectHasAttribute('notificationiconurl', $customdata);
        $this->assertContains('tokenpluginfile.php', $customdata->notificationiconurl);
        $userpicture = new \user_picture($u1);
        $userpicture->size = 1; // Use f1 size.
        $userpicture->includetoken = $u2->id;
        $this->assertEquals($userpicture->get_url($PAGE)->out(false), $customdata->notificationiconurl);

        // Reviewer posts a comment for the user competency being in two plans. Owner is messaged.
        $this->setUser($u2);
        $comment = $uc2->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = $c2->get('shortname');
        $expectedurl = url::user_competency_in_plan($u1->id, $c2->get('id'), $p2->get('id'));
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message->useridto);
        $this->assertTrue(strpos($message->fullmessage, 'Hello world!') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, 'Hello world!') !== false);
        $this->assertEquals(FORMAT_MOODLE, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);

        // Reviewer posts a comment for the user competency being in no plans. User is messaged.
        $this->setUser($u2);
        $comment = $uc3->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = get_string('userplans', 'core_competency');
        $expectedurl = url::plans($u1->id);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message->useridto);
        $this->assertTrue(strpos($message->fullmessage, 'Hello world!') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, 'Hello world!') !== false);
        $this->assertEquals(FORMAT_MOODLE, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);

        // A comment is posted by another user, reviewer and owner are messaged.
        $this->setUser($u3);
        $comment = $uc3->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(2, $messages);
        $message1 = array_shift($messages);
        $message2 = array_shift($messages);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message1->useridto);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u2->id, $message2->useridto);

        // A comment is posted in HTML.
        $this->setUser($u2);
        $comment = $uc3->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('<em>Hello world!</em>', FORMAT_HTML);
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = get_string('userplans', 'core_competency');
        $expectedurl = url::plans($u1->id);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message->useridto);
        $this->assertTrue(strpos($message->fullmessage, '<em>Hello world!</em>') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, '<em>Hello world!</em>') !== false);
        $this->assertEquals(FORMAT_HTML, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);
    }

    /**
     * Commenting on a plan.
     */
    public function test_comment_add_plan() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $userroleid = $dg->create_role();
        $reviewerroleid = $dg->create_role();
        assign_capability('moodle/competency:planviewowndraft', CAP_ALLOW, $userroleid, context_system::instance()->id, true);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $userroleid, context_system::instance()->id, true);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $reviewerroleid, context_system::instance()->id, true);
        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $reviewerroleid, context_system::instance()->id, true);
        assign_capability('moodle/competency:plancomment', CAP_ALLOW, $reviewerroleid, context_system::instance()->id, true);
        $dg->role_assign($userroleid, $u1->id, context_user::instance($u1->id));
        $dg->role_assign($reviewerroleid, $u2->id, context_user::instance($u1->id));
        $dg->role_assign($reviewerroleid, $u3->id, context_system::instance());
        accesslib_clear_all_caches_for_unit_testing();

        $p1 = $lpg->create_plan(array('userid' => $u1->id));

        // Post a comment in own plan, no reviewer. Nobody is messaged.
        $this->setUser($u1);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(0, $messages);

        // Post a comment in plan as someone else, no reviewer. The owner is messages.
        $this->setUser($u3);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message->useridto);
        // Test customdata.
        $customdata = json_decode($message->customdata);
        $this->assertObjectHasAttribute('notificationiconurl', $customdata);

        // Post a comment in a plan with reviewer. The reviewer is messaged.
        $p1->set('reviewerid', $u2->id);
        $p1->update();
        $this->setUser($u1);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u2->id, $message->useridto);

        // Post a comment as reviewer in a plan being reviewed. The owner is messaged.
        $p1->set('reviewerid', $u2->id);
        $p1->update();
        $this->setUser($u2);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message->useridto);

        // Post a comment as someone else in a plan being reviewed. The owner and reviewer are messaged.
        $p1->set('reviewerid', $u2->id);
        $p1->update();
        $this->setUser($u3);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(2, $messages);
        $message1 = array_shift($messages);
        $message2 = array_shift($messages);
        $this->assertEquals(core_user::get_noreply_user()->id, $message1->useridfrom);
        $this->assertEquals($u1->id, $message1->useridto);
        $this->assertEquals(core_user::get_noreply_user()->id, $message2->useridfrom);
        $this->assertEquals($u2->id, $message2->useridto);

        $p1->set('reviewerid', null);
        $p1->update();

        // Test message content.
        $this->setUser($u3);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = $p1->get('name');
        $expectedurl = url::plan($p1->get('id'));
        $this->assertTrue(strpos($message->fullmessage, 'Hello world!') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, 'Hello world!') !== false);
        $this->assertEquals(FORMAT_MOODLE, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);

        // Test message content as HTML.
        $this->setUser($u3);
        $comment = $p1->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('<em>Hello world!</em>', FORMAT_HTML);
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = $p1->get('name');
        $expectedurl = url::plan($p1->get('id'));
        $this->assertTrue(strpos($message->fullmessage, '<em>Hello world!</em>') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, '<em>Hello world!</em>') !== false);
        $this->assertEquals(FORMAT_HTML, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);
    }

}

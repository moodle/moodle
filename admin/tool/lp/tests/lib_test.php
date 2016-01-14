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
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use tool_lp\user_competency;

global $CFG;

/**
 * Lib testcase.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_lib_testcase extends advanced_testcase {

    public function test_comment_add_user_competency() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $reviewerroleid = $dg->create_role();
        assign_capability('tool/lp:planview', CAP_ALLOW, $reviewerroleid, context_system::instance()->id, true);
        assign_capability('tool/lp:usercompetencycomment', CAP_ALLOW, $reviewerroleid, context_system::instance()->id, true);
        $dg->role_assign($reviewerroleid, $u2->id, context_user::instance($u1->id));
        $dg->role_assign($reviewerroleid, $u3->id, context_user::instance($u1->id));
        accesslib_clear_all_caches_for_unit_testing();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id())); // In 1 plan.
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id())); // In 2 plans.
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id())); // Orphan.

        $p1 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c2->get_id()));
        $p2 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p2->get_id(), 'competencyid' => $c2->get_id()));

        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c1->get_id(),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $u2->id));
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c2->get_id(),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $u2->id));
        $uc3 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c3->get_id(),
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

        $expectedurlname = $c1->get_shortname();
        $expectedurl = new moodle_url('/admin/tool/lp/user_competency_in_plan.php', array(
            'userid' => $u1->id,
            'competencyid' => $c1->get_id(),
            'planid' => $p1->get_id()
        ));
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u2->id, $message->useridto);
        $this->assertTrue(strpos($message->fullmessage, 'Hello world!') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, 'Hello world!') !== false);
        $this->assertEquals(FORMAT_MOODLE, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);

        // Reviewer posts a comment for the user competency being in two plans. Owner is messaged.
        $this->setUser($u2);
        $comment = $uc2->get_comment_object();
        $sink = $this->redirectMessages();
        $comment->add('Hello world!');
        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $expectedurlname = $c2->get_shortname();
        $expectedurl = new moodle_url('/admin/tool/lp/user_competency_in_plan.php', array(
            'userid' => $u1->id,
            'competencyid' => $c2->get_id(),
            'planid' => $p1->get_id()
        ));
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

        $expectedurlname = get_string('userplans', 'tool_lp');
        $expectedurl = new moodle_url('/admin/tool/lp/plans.php', array(
            'userid' => $u1->id,
        ));
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

        $expectedurlname = get_string('userplans', 'tool_lp');
        $expectedurl = new moodle_url('/admin/tool/lp/plans.php', array(
            'userid' => $u1->id,
        ));
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($u1->id, $message->useridto);
        $this->assertTrue(strpos($message->fullmessage, '<em>Hello world!</em>') !== false);
        $this->assertTrue(strpos($message->fullmessagehtml, '<em>Hello world!</em>') !== false);
        $this->assertEquals(FORMAT_HTML, $message->fullmessageformat);
        $this->assertEquals($expectedurl->out(false), $message->contexturl);
        $this->assertEquals($expectedurlname, $message->contexturlname);
    }

}

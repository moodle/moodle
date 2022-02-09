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
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

namespace mod_bigbluebuttonbn\local\helpers;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\local\helpers\mod_helper
 * @coversDefaultClass \mod_bigbluebuttonbn\local\helpers\mod_helper
 */
class mod_helper_trait_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Presave test
     */
    public function test_process_pre_save() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        $bbformdata->participants = '<p>this -&gt; &quot;</p>\n';
        $bbformdata->timemodified = time();
        mod_helper::process_pre_save($bbformdata);
        $this->assertTrue($bbformdata->timemodified != 0);
        $this->assertEquals('<p>this -> "</p>\n', $bbformdata->participants);
    }

    /**
     * Presave instance
     */
    public function test_process_pre_save_instance() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        $bbformdata->instance = 0;
        $bbformdata->timemodified = time();
        mod_helper::process_pre_save($bbformdata);
        $this->assertTrue($bbformdata->timemodified == 0);
    }

    /**
     * Presave checkboxes
     */
    public function test_process_pre_save_checkboxes() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        unset($bbformdata->wait);
        unset($bbformdata->recordallfromstart);
        mod_helper::process_pre_save($bbformdata);
        $this->assertTrue(isset($bbformdata->wait));
        $this->assertTrue(isset($bbformdata->recordallfromstart));
    }

    /**
     * Presave common
     */
    public function test_process_pre_save_common() {
        global $CFG;
        $this->resetAfterTest();

        list($bbactivitycontext, $bbactivitycm, $bbactivity) =
            $this->create_instance(null, ['type' => instance::TYPE_RECORDING_ONLY]);
        $bbformdata = $this->get_form_data_from_instance($bbactivity);

        $bbformdata->groupmode = '1';
        mod_helper::process_pre_save($bbformdata);
        $this->assertEquals(0, $bbformdata->groupmode);
    }

    /**
     * Post save
     */
    public function test_process_post_save() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) =
            $this->create_instance(null, ['type' => instance::TYPE_RECORDING_ONLY]);
        $bbformdata = $this->get_form_data_from_instance($bbactivity);

        // Enrol users in a course so he will receive the message.
        $teacher = $generator->create_user(['role' => 'editingteacher']);
        $generator->enrol_user($teacher->id, $this->get_course()->id);

        // Mark the form to trigger notification.
        $bbformdata->coursecontentnotification = true;
        $bbformdata->update = false;
        $messagesink = $this->redirectMessages();
        mod_helper::process_post_save($bbformdata);
        edit_module_post_actions($bbformdata, $this->course);
        // Now run cron.
        ob_start();
        $this->runAdhocTasks();
        ob_get_clean(); // Suppress output as it can fail the test.
        $this->assertEquals(1, $messagesink->count());
        $firstmessage = $messagesink->get_messages()[0];
        $this->assertStringContainsString('is new in', $firstmessage->smallmessage);
    }

    /**
     * Post save notification
     */
    public function test_process_post_save_with_add() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) =
            $this->create_instance(null, ['type' => instance::TYPE_RECORDING_ONLY]);
        $bbformdata = $this->get_form_data_from_instance($bbactivity);

        $bbformdata->update = false;
        $messagesink = $this->redirectMessages();
        // Enrol users in a course so he will receive the message.
        $teacher = $generator->create_user(['role' => 'editingteacher']);
        $generator->enrol_user($teacher->id, $this->get_course()->id);
        $bbformdata->coursecontentnotification = true;
        mod_helper::process_post_save($bbformdata);
        edit_module_post_actions($bbformdata, $this->course);
        // Now run cron.
        ob_start();
        $this->runAdhocTasks();
        ob_get_clean(); // Suppress output as it can fail the test.
        $this->assertEquals(1, $messagesink->count());
        $firstmessage = $messagesink->get_messages()[0];
        $this->assertStringContainsString('is new in', $firstmessage->smallmessage);
    }

    /**
     * Post save
     *
     * There was an issue when both the opening time and completion were set
     * and the form was saved twice.
     */
    public function test_process_post_save_twice_with_completion() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) =
            $this->create_instance(null, ['type' => instance::TYPE_RECORDING_ONLY]);
        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        $bbformdata->completionunlocked = 0;
        $bbformdata->completion = COMPLETION_AGGREGATION_ANY;
        $bbformdata->completionview = COMPLETION_VIEWED;
        $bbformdata->completionexpected = time();
        $bbformdata->openingtime = time() - 1000;
        $bbformdata->closing = time() + 1000;
        // Enrol users in a course so he will receive the message.
        $teacher = $generator->create_user();
        $generator->enrol_user($teacher->id, $this->get_course()->id, 'editingteacher');
        $this->setUser($teacher);
        // Mark the form to trigger notification.
        $bbformdata->coursecontentnotification = true;
        $bbformdata->update = false;
        $messagesink = $this->redirectMessages();
        mod_helper::process_post_save($bbformdata);
        edit_module_post_actions($bbformdata, $this->course);
        // Now run cron.
        ob_start();
        $this->runAdhocTasks();
        ob_get_clean(); // Suppress output as it can fail the test.
        $this->assertEquals(1, $messagesink->count());
        $firstmessage = $messagesink->get_messages()[0];
        $this->assertStringContainsString('is new in', $firstmessage->smallmessage);
        $messagesink->clear();
        // Do it a again, so we check we still have one event.
        mod_helper::process_post_save($bbformdata);
        // Mark the form to trigger notification.
        $bbformdata->update = true;
        edit_module_post_actions($bbformdata, $this->course);
        // Now run cron.
        ob_start();
        $this->runAdhocTasks();
        ob_get_clean(); // Suppress output as it can fail the test.
        $this->assertEquals(1, $messagesink->count());
        $firstmessage = $messagesink->get_messages()[0];
        $this->assertStringContainsString('has been changed', $firstmessage->smallmessage);
    }
}

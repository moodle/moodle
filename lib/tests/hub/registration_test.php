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

namespace core\hub;

/**
 * Class containing unit tests for the site registration class.
 *
 * @package    core
 * @copyright  2023 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\hub\registration
 */
final class registration_test extends \advanced_testcase {

    /**
     * Test getting site registration information.
     */
    public function test_get_site_info(): void {
        global $CFG;
        $this->resetAfterTest();

        // Create some courses with end dates.
        $generator = $this->getDataGenerator();
        $generator->create_course(['enddate' => time() + 1000]);
        $generator->create_course(['enddate' => time() + 1000]);

        $generator->create_course(); // Course with no end date.

        $siteinfo = registration::get_site_info();

        $this->assertNull($siteinfo['policyagreed']);
        $this->assertEquals($CFG->dbtype, $siteinfo['dbtype']);
        $this->assertEquals('manual', $siteinfo['primaryauthtype']);
        $this->assertEquals(1, $siteinfo['coursesnodates']);
    }

    /**
     * Test getting the plugin usage data.
     */
    public function test_get_plugin_usage(): void {
        global $DB;
        $this->resetAfterTest();

        // Create some courses with end dates.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create some assignments.
        $generator->create_module('assign', ['course' => $course->id]);
        $generator->create_module('assign', ['course' => $course->id]);
        $generator->create_module('assign', ['course' => $course->id]);

        // Create some quizzes.
        $generator->create_module('quiz', ['course' => $course->id]);
        $generator->create_module('quiz', ['course' => $course->id]);

        // Add some blocks.
        $generator->create_block('online_users');
        $generator->create_block('online_users');
        $generator->create_block('online_users');
        $generator->create_block('online_users');

        // Disabled a plugin.
        $DB->set_field('modules', 'visible', 0, ['name' => 'feedback']);

        // Check our plugin usage counts and enabled states are correct.
        $pluginusage = registration::get_plugin_usage_data();
        $this->assertEquals(3, $pluginusage['mod']['assign']['count']);
        $this->assertEquals(2, $pluginusage['mod']['quiz']['count']);
        $this->assertEquals(4, $pluginusage['block']['online_users']['count']);
        $this->assertEquals(0, $pluginusage['mod']['feedback']['enabled']);
        $this->assertEquals(1, $pluginusage['mod']['assign']['enabled']);
    }

    /**
     * Test the AI usage data is calculated correctly.
     */
    public function test_get_ai_usage(): void {
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen(1700000000);
        $this->generate_ai_usage_data();

        // Get our site info and check the expected calculations are correct.
        $siteinfo = registration::get_site_info();
        $aisuage = json_decode($siteinfo['aiusage']);
        // Check generated text.
        $this->assertEquals(1, $aisuage->aiprovider_openai->generate_text->success_count);
        $this->assertEquals(0, $aisuage->aiprovider_openai->generate_text->fail_count);
        // Check generated images.
        $this->assertEquals(2, $aisuage->aiprovider_openai->generate_image->success_count);
        $this->assertEquals(3, $aisuage->aiprovider_openai->generate_image->fail_count);
        $this->assertEquals(15, $aisuage->aiprovider_openai->generate_image->average_time);
        $this->assertEquals(403, $aisuage->aiprovider_openai->generate_image->predominant_error);
        // Check time range is set correctly.
        $this->assertEquals($clock->time() - WEEKSECS, $aisuage->time_range->timefrom);
        $this->assertEquals($clock->time(), $aisuage->time_range->timeto);
        // Check model counts.
        $gpt4omodel = 'gpt-4o';
        $dalle3model = 'dall-e-3';
        $this->assertEquals(1, $aisuage->aiprovider_openai->generate_text->models->{$gpt4omodel}->count);
        $this->assertEquals(2, $aisuage->aiprovider_openai->generate_image->models->{$dalle3model}->count);
        $this->assertEquals(3, $aisuage->aiprovider_openai->generate_image->models->unknown->count);
    }

    /**
     * Create some dummy AI usage data.
     */
    private function generate_ai_usage_data(): void {
        global $DB;

        $clock = $this->mock_clock_with_frozen(1700000000);

        // Record some generated text.
        $record = new \stdClass();
        $record->provider = 'aiprovider_openai';
        $record->actionname = 'generate_text';
        $record->actionid = 1;
        $record->userid = 1;
        $record->contextid = 1;
        $record->success = true;
        $record->timecreated = $clock->time() - 5;
        $record->timecompleted = $clock->time();
        $record->model = 'gpt-4o';
        $DB->insert_record('ai_action_register', $record);

        // Record a generated image.
        $record->actionname = 'generate_image';
        $record->actionid = 111;
        $record->timecreated = $clock->time() - 20;
        $record->model = 'dall-e-3';
        $DB->insert_record('ai_action_register', $record);
        // Record another image.
        $record->actionid = 222;
        $record->timecreated = $clock->time() - 10;
        $DB->insert_record('ai_action_register', $record);

        // Record some errors.
        $record->actionname = 'generate_image';
        $record->actionid = 4;
        $record->success = false;
        $record->errorcode = 403;
        $record->model = null;
        $DB->insert_record('ai_action_register', $record);
        $record->actionid = 5;
        $record->errorcode = 403;
        $DB->insert_record('ai_action_register', $record);
        $record->actionid = 6;
        $record->errorcode = 404;
        $DB->insert_record('ai_action_register', $record);
    }

    /**
     * Test the show AI usage data.
     */
    public function test_show_ai_usage(): void {
        $this->resetAfterTest();

        // Init the registration class.
        $registration = new registration();

        // There should be no data to show yet.
        $aisuagedata = $registration->show_ai_usage();
        $this->assertTrue(empty($aisuagedata));

        // After generating some data, there should now be some data to show.
        $this->generate_ai_usage_data();
        $aisuagedata = $registration->show_ai_usage();
        $this->assertTrue(!empty($aisuagedata));

        foreach ($aisuagedata['providers'] as $provider) {
            $this->assertEquals('OpenAI API provider', $provider['providername']);
            $this->assertTrue(!empty($provider['aiactions']));

            foreach ($provider['aiactions'] as $action) {
                $actionname = $action['actionname'];
                $this->assertTrue(!empty($actionname));
            }
        }

        $timerange = $aisuagedata['timerange'];
        $this->assertEquals(get_string('time_range', 'hub'), $timerange['label']);
        $this->assertTrue(!empty($timerange['values']));
    }
}

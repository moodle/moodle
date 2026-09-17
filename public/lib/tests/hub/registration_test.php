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

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class containing unit tests for the site registration class.
 *
 * @package    core
 * @copyright  2023 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(registration::class)]
final class registration_test extends \advanced_testcase {
    /**
     * Clear the static registration cache so each test sees the current database state.
     *
     * \core\hub\registration caches the registration record in a static property that is not
     * reset between tests, so without this a record from an earlier test leaks into this one.
     */
    protected function setUp(): void {
        parent::setUp();

        $this->reset_registration_cache();
    }

    /**
     * Clear the static registration cache so a mid-test change to the database is picked up.
     */
    private function reset_registration_cache(): void {
        $property = new \ReflectionProperty(\core\hub\registration::class, 'registration');
        $property->setValue(null, null);
    }


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
        \core_plugin_manager::reset_caches();

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

    /**
     * Test that get_site_info() excludes fields pending admin confirmation when requested.
     */
    public function test_get_site_info_excludes_unconfirmed_fields(): void {
        $this->resetAfterTest();

        $this->register_site();

        // Pretend the admin last confirmed just before the 'aiusage' field was introduced,
        // so it is still pending confirmation.
        set_config('site_regupdateversion', 2023072300, 'hub');

        $fullsiteinfo = registration::get_site_info();
        $this->assertArrayHasKey('aiusage', $fullsiteinfo);

        $filteredsiteinfo = registration::get_site_info([], registration::get_new_registration_fields());
        $this->assertArrayNotHasKey('aiusage', $filteredsiteinfo);

        // Fields confirmed before the pending stamp must still be present.
        $this->assertArrayHasKey('pluginusage', $filteredsiteinfo);
        $this->assertArrayHasKey('dbtype', $filteredsiteinfo);
    }

    /**
     * Test that the full payload resumes once the admin confirms the pending fields.
     */
    public function test_get_site_info_resumes_full_payload_after_confirmation(): void {
        $this->resetAfterTest();

        $this->register_site();

        set_config('site_regupdateversion', 2023072300, 'hub');
        $this->assertNotEmpty(registration::get_new_registration_fields());

        // Simulate the admin submitting the registration form to confirm the new fields.
        $formdata = new \stdClass();
        foreach (registration::FORM_FIELDS as $field) {
            $formdata->$field = null;
        }
        registration::save_site_info($formdata);

        $this->assertEmpty(registration::get_new_registration_fields());
        $this->assertEquals(
            registration::get_site_info(),
            registration::get_site_info([], registration::get_new_registration_fields())
        );
    }

    /**
     * Test that update_cron() keeps updating registration, instead of returning early, while fields are
     * pending admin confirmation.
     */
    public function test_update_cron_continues_while_fields_pending_confirmation(): void {
        global $DB;

        $this->resetAfterTest();

        $id = $this->register_site();
        $registration = $DB->get_record('registration_hubs', ['id' => $id]);

        // Pretend the admin last confirmed just before 'aiusage' was introduced, so it is still
        // pending confirmation and update_cron() would previously have returned early.
        set_config('site_regupdateversion', 2023072300, 'hub');
        $this->assertNotEmpty(registration::get_new_registration_fields());

        // Ensure the timestamp comparison below is meaningful.
        $this->waitForSecond();

        $this->expectOutputRegex('~Registration information has been changed.*Site registration updated~s');

        // Fake a successful response from the hub for hub_update_site_info, which returns a
        // JSON scalar (true) on success.
        \curl::mock_response(json_encode(true));
        registration::update_cron();

        $updated = $DB->get_record('registration_hubs', ['id' => $registration->id]);
        $this->assertGreaterThan($registration->timemodified, $updated->timemodified);
    }

    /**
     * Register the site locally so is_registered() returns true.
     *
     * @return int id of the inserted registration_hubs record
     */
    private function register_site(): int {
        global $CFG, $DB;

        // Paused reporting is only detected for publicly accessible sites.
        $CFG->site_is_public = true;

        return $DB->insert_record('registration_hubs', [
            'token' => 'abc123',
            'hubname' => 'Moodle.org',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => 'secret123',
            'timemodified' => time(),
        ]);
    }

    /**
     * Test get_reporting_paused_reason() for an unregistered site.
     */
    public function test_get_reporting_paused_reason_not_registered(): void {
        $this->resetAfterTest();

        $this->assertSame('', registration::get_reporting_paused_reason());
    }

    /**
     * Test get_reporting_paused_reason() for a registered site reporting normally.
     */
    public function test_get_reporting_paused_reason_reporting_normally(): void {
        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', max(array_keys(registration::CONFIRM_NEW_FIELDS)), 'hub');

        $this->assertSame('', registration::get_reporting_paused_reason());
    }

    /**
     * Test get_reporting_paused_reason() when new registration fields need confirming.
     */
    public function test_get_reporting_paused_reason_new_fields(): void {
        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', 0, 'hub');

        $this->assertSame(registration::REPORTING_PAUSED_NEW_FIELDS, registration::get_reporting_paused_reason());
    }

    /**
     * Test get_reporting_paused_reason() when the registration cron task is disabled.
     */
    public function test_get_reporting_paused_reason_task_disabled(): void {
        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', max(array_keys(registration::CONFIRM_NEW_FIELDS)), 'hub');

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $this->assertSame(registration::REPORTING_PAUSED_TASK_DISABLED, registration::get_reporting_paused_reason());
    }

    /**
     * Test get_reporting_paused_reason() for a site that is not publicly accessible.
     *
     * Such a site is deliberately not reported as paused: the registration task only sends updates when
     * site_is_public(), so there is nothing the admin could usefully do about it.
     */
    public function test_get_reporting_paused_reason_not_public(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', 0, 'hub');
        $CFG->site_is_public = false;

        $this->assertSame('', registration::get_reporting_paused_reason());
    }

    /**
     * Test that check_reporting_paused_notification() notifies admins once and does not repeat until cleared.
     */
    public function test_check_reporting_paused_notification(): void {
        $this->resetAfterTest();
        $sink = $this->redirectMessages();

        $this->register_site();
        set_config('site_regupdateversion', 0, 'hub');

        // First check should send a notification to the admin(s).
        registration::check_reporting_paused_notification();
        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $this->assertSame('registrationreportingpaused', $messages[0]->eventtype);

        // A second check while still paused for the same reason should not send another notification.
        registration::check_reporting_paused_notification();
        $this->assertCount(1, $sink->get_messages());

        // Once reporting resumes, the flag is cleared and a fresh pause notifies again.
        set_config('site_regupdateversion', max(array_keys(registration::CONFIRM_NEW_FIELDS)), 'hub');
        registration::check_reporting_paused_notification();
        $this->assertCount(1, $sink->get_messages());

        set_config('site_regupdateversion', 0, 'hub');
        registration::check_reporting_paused_notification();
        $this->assertCount(2, $sink->get_messages());

        $sink->close();
    }

    /**
     * Test that check_reporting_paused_notification() sends the task-disabled wording, not the
     * new-fields wording, when the registration cron task is the reason reporting has paused.
     */
    public function test_check_reporting_paused_notification_task_disabled(): void {
        global $CFG;

        $this->resetAfterTest();
        $sink = $this->redirectMessages();

        $this->register_site();
        set_config('site_regupdateversion', max(array_keys(registration::CONFIRM_NEW_FIELDS)), 'hub');

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        registration::check_reporting_paused_notification();

        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertSame('registrationreportingpaused', $message->eventtype);
        $expected = get_string(
            'registrationreportingpausedtaskdisabledmessage',
            'admin',
            (object) ['siteurl' => $CFG->wwwroot],
        );
        $this->assertSame($expected, $message->fullmessage);
        $this->assertStringNotContainsString(
            get_string('registrationreportingpausednewfieldsmessage', 'admin', (object) ['siteurl' => $CFG->wwwroot]),
            $message->fullmessage,
        );

        $sink->close();
    }

    /**
     * Test get_registration_page_notification() for an unregistered, non-initial-registration site.
     */
    public function test_get_registration_page_notification_unregistered(): void {
        $this->resetAfterTest();

        $notification = registration::get_registration_page_notification(false, false);
        $this->assertSame(get_string('registrationwarning', 'admin'), $notification['message']);
        $this->assertSame(\core\output\notification::NOTIFY_ERROR, $notification['type']);
    }

    /**
     * Test get_registration_page_notification() for an unregistered site pending its initial registration.
     */
    public function test_get_registration_page_notification_initial_registration(): void {
        $this->resetAfterTest();

        $notification = registration::get_registration_page_notification(false, true);
        $this->assertSame('', $notification['message']);
    }

    /**
     * Test get_registration_page_notification() for a registered site that has never successfully updated.
     */
    public function test_get_registration_page_notification_unknown_last_updated(): void {
        global $DB;

        $this->resetAfterTest();
        $id = $this->register_site();
        $DB->set_field('registration_hubs', 'timemodified', 0, ['id' => $id]);

        $notification = registration::get_registration_page_notification(true, false);
        $this->assertSame(get_string('pleaserefreshregistrationunknown', 'admin'), $notification['message']);
        $this->assertSame(\core\output\notification::NOTIFY_ERROR, $notification['type']);
    }

    /**
     * Test get_registration_page_notification() for a registered site with new fields pending confirmation.
     */
    public function test_get_registration_page_notification_new_fields(): void {
        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', 0, 'hub');

        $notification = registration::get_registration_page_notification(true, false);
        $this->assertSame(get_string('pleaserefreshregistrationnewdata', 'admin'), $notification['message']);
        $this->assertSame(\core\output\notification::NOTIFY_ERROR, $notification['type']);
    }

    /**
     * Test get_registration_page_notification() when the registration cron task is disabled.
     */
    public function test_get_registration_page_notification_task_disabled(): void {
        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', max(array_keys(registration::CONFIRM_NEW_FIELDS)), 'hub');

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $notification = registration::get_registration_page_notification(true, false);
        $this->assertSame(get_string('registrationtaskdisabled', 'admin', (new \moodle_url(
            '/admin/tool/task/scheduledtasks.php'
        ))->out(false)), $notification['message']);
        $this->assertSame(\core\output\notification::NOTIFY_WARNING, $notification['type']);
    }

    /**
     * Test get_registration_page_notification() for a registered site reporting normally.
     */
    public function test_get_registration_page_notification_reporting_normally(): void {
        $this->resetAfterTest();
        $this->register_site();
        set_config('site_regupdateversion', max(array_keys(registration::CONFIRM_NEW_FIELDS)), 'hub');

        $notification = registration::get_registration_page_notification(true, false);
        $this->assertSame(\core\output\notification::NOTIFY_INFO, $notification['type']);
        $this->assertNotSame('', $notification['message']);
    }

    /**
     * The initial registration redirect must carry only the token, site URL, and the small
     * fixed set of fields (policyagreed, contactemail, language) the hub's own initial
     * registration processing requires - never the full site info payload that used to be
     * silently truncated at a 2000-character URL cap.
     */
    public function test_get_registration_redirect_url_only_carries_required_fields(): void {
        $this->resetAfterTest();

        $siteinfo = [
            'url' => 'https://example.com',
            'policyagreed' => 1,
            'contactemail' => 'admin@example.com',
            'language' => 'en',
            'pluginusage' => json_encode(['some' => 'large payload']),
        ];

        $method = new \ReflectionMethod(registration::class, 'get_registration_redirect_url');
        $method->setAccessible(true);
        $url = $method->invoke(null, 'sometoken123', $siteinfo);

        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertEquals('sometoken123', $url->get_param('token'));
        $this->assertEquals('https://example.com', $url->get_param('url'));
        $this->assertEquals(1, $url->get_param('policyagreed'));
        $this->assertEquals('admin@example.com', $url->get_param('contactemail'));
        $this->assertEquals('en', $url->get_param('language'));
        $this->assertCount(5, $url->params());
    }

    /**
     * register() should create the unconfirmed registration record and attempt the hub redirect.
     * In PHPUnit, redirect() throws instead of sending headers, which is what we assert on here.
     */
    public function test_register_creates_unconfirmed_registration_and_redirects(): void {
        global $DB;
        $this->resetAfterTest();
        $this->reset_registration_cache();

        try {
            registration::register('');
            $this->fail('Expected moodle_exception from redirect() to propagate.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('redirecterrordetected', $e->errorcode);
        }

        $this->assertTrue($DB->record_exists('registration_hubs', ['confirmed' => 0]));
    }

    /**
     * When the post-confirmation full-payload push to the hub succeeds, confirm_registration()
     * should report success and leave nothing queued for retry.
     */
    public function test_confirm_registration_returns_true_when_full_payload_push_succeeds(): void {
        global $DB;
        $this->resetAfterTest();
        $this->disable_airnotifier_post_registration_hook();

        $hubid = $this->create_unconfirmed_registration('oldtoken');
        // The hub's hub_update_site_info returns a JSON scalar (true) on success.
        \curl::mock_response(json_encode(true));

        $result = registration::confirm_registration('oldtoken', 'newtoken', 'moodle');

        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('registration_hubs', ['id' => $hubid, 'confirmed' => 1]));
        $adhoctasks = \core\task\manager::get_adhoc_tasks(\core\task\complete_hub_registration_task::class);
        $this->assertCount(0, $adhoctasks);
    }

    /**
     * When the post-confirmation full-payload push fails for a reason unrelated to the token
     * itself, the just-confirmed registration must be kept and a retry queued, rather than the
     * partial record sitting untouched until the next weekly registration_cron_task run.
     */
    public function test_confirm_registration_queues_retry_when_full_payload_push_fails(): void {
        global $DB;
        $this->resetAfterTest();
        $this->disable_airnotifier_post_registration_hook();

        $hubid = $this->create_unconfirmed_registration('oldtoken');
        $response = json_encode(['exception' => 'some_other_exception', 'message' => 'hub is down']);
        \curl::mock_response($response);

        $result = registration::confirm_registration('oldtoken', 'newtoken', 'moodle');

        $this->assertFalse($result);
        $this->assertTrue($DB->record_exists('registration_hubs', ['id' => $hubid, 'confirmed' => 1]));
        $adhoctasks = \core\task\manager::get_adhoc_tasks(\core\task\complete_hub_registration_task::class);
        $this->assertCount(1, $adhoctasks);
    }

    /**
     * When the hub rejects the token during the post-confirmation push, process_curl_exception()
     * deletes the just-confirmed record via reset_token(). Queuing a retry task against that
     * would be a silent no-op forever, so confirm_registration() must instead send the site back
     * through registration from scratch.
     */
    public function test_confirm_registration_restarts_registration_when_token_rejected(): void {
        global $DB;
        $this->resetAfterTest();

        $hubid = $this->create_unconfirmed_registration('oldtoken');
        $response = json_encode([
            'exception' => 'moodle_exception',
            'errorcode' => 'invalidtoken',
            'message' => 'bad token',
        ]);
        \curl::mock_response($response);

        try {
            registration::confirm_registration('oldtoken', 'newtoken', 'moodle');
            $this->fail('Expected moodle_exception from redirect() to propagate.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('redirecterrordetected', $e->errorcode);
        }

        // The rejected-token row must be gone, not left behind as a stale confirmed record, and
        // nothing should be queued against a registration that no longer exists.
        $this->assertFalse($DB->record_exists('registration_hubs', ['id' => $hubid]));
        $adhoctasks = \core\task\manager::get_adhoc_tasks(\core\task\complete_hub_registration_task::class);
        $this->assertCount(0, $adhoctasks);
    }

    /**
     * confirm_registration() fires the post_site_registration_confirmed hook, which for
     * message_airnotifier makes its own outbound request to obtain an access key. Give it a
     * pre-set access key so the hook short-circuits, instead of that unrelated plugin's network
     * call leaking into a test of the hub registration confirmation flow.
     */
    private function disable_airnotifier_post_registration_hook(): void {
        set_config('airnotifieraccesskey', 'testkey');
    }

    /**
     * Create an unconfirmed registration_hubs record ready to be confirmed in a test.
     *
     * @param string $token
     * @return int id of the created record
     */
    private function create_unconfirmed_registration(string $token): int {
        global $DB;

        $hub = new \stdClass();
        $hub->token = $token;
        $hub->secret = $token;
        $hub->huburl = HUB_MOODLEORGHUBURL;
        $hub->hubname = 'moodle';
        $hub->confirmed = 0;
        $hub->timemodified = time();
        $id = $DB->insert_record('registration_hubs', $hub);
        $this->reset_registration_cache();

        return $id;
    }
}

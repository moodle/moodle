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

declare(strict_types=1);

namespace core_message\reportbuilder\datasource;

use core\clock;
use core_message\api;
use core_message\tests\helper;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;

/**
 * Unit tests for messages datasource
 *
 * @package     core_message
 * @covers      \core_message\reportbuilder\datasource\messages
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class messages_test extends core_reportbuilder_testcase {
    /** @var clock $clock */
    private readonly clock $clock;

    /**
     * Mock the clock
     */
    protected function setUp(): void {
        parent::setUp();

        $this->clock = $this->mock_clock_with_frozen(1622502000);
    }

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        // Test subject.
        $course = $this->getDataGenerator()->create_course();
        $userone = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe', 'lastname' => 'Zebra']);
        $usertwo = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Morris', 'lastname' => 'Moo']);
        $userthree = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Aaron', 'lastname' => 'Ant']);

        $groupconversation = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$userone->id, $usertwo->id, $userthree->id],
        );
        helper::send_fake_message_to_conversation($userone, $groupconversation->id, 'Are you somewhere feeling lonely?');

        $this->clock->bump(HOURSECS);

        $privateconversation = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$userone->id, $usertwo->id],
        );
        helper::send_fake_message_to_conversation($usertwo, $privateconversation->id, 'Or is someone loving you?');

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Messages', 'source' => messages::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are author, recipient, message, time created. Sorted by author, recipient, time created.
        $this->assertEquals([
            [
                fullname($usertwo),
                fullname($userone),
                '<div class="text_to_html">Or is someone loving you?</div>',
                'Tuesday, 1 June 2021, 8:00 AM',
            ],
            [
                fullname($userone),
                fullname($userthree),
                '<div class="text_to_html">Are you somewhere feeling lonely?</div>',
                'Tuesday, 1 June 2021, 7:00 AM',
            ],
            [
                fullname($userone),
                fullname($usertwo),
                '<div class="text_to_html">Are you somewhere feeling lonely?</div>',
                'Tuesday, 1 June 2021, 7:00 AM',
            ],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        // Test subject.
        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();

        $conversation = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$userone->id, $usertwo->id],
            'My conversation',
        );
        helper::send_fake_message_to_conversation($userone, $conversation->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Messages', 'source' => messages::class, 'default' => 0]);

        // Message.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'message:subject']);

        // Conversation.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'conversation:type']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'conversation:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'conversation:enabled']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'conversation:timecreated']);

        $content = $this->get_custom_report_content($report->get('id'));

        $this->assertEquals([
            [
                'No subject',
                'Private',
                'My conversation',
                'Yes',
                'Tuesday, 1 June 2021, 7:00 AM',
            ],
        ], array_map('array_values', $content));
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            // Message.
            'Message subject' => ['message:subject', [
                'message:subject_operator' => text::IS_EQUAL_TO,
                'message:subject_value' => 'No subject',
            ], true],
            'Message subject (no match)' => ['message:subject', [
                'message:subject_operator' => text::IS_EQUAL_TO,
                'message:subject_value' => 'Another subject',
            ], false],
            'Message content' => ['message:message', [
                'message:message_operator' => text::IS_EQUAL_TO,
                'message:message_value' => 'Hi',
            ], true],
            'Message content (no match)' => ['message:message', [
                'message:message_operator' => text::IS_EQUAL_TO,
                'message:message_value' => 'Bye',
            ], false],
            'Message time created' => ['message:timecreated', [
                'message:timecreated_operator' => date::DATE_RANGE,
                'message:timecreated_from' => 1622502000,
            ], true],
            'Message time created (no match)' => ['message:timecreated', [
                'message:timecreated_operator' => date::DATE_RANGE,
                'message:timecreated_to' => 1622502000,
            ], false],

            // Conversation.
            'Conversation type' => ['conversation:type', [
                'conversation:type_operator' => select::EQUAL_TO,
                'conversation:type_value' => api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            ], true],
            'Conversation type (no match)' => ['conversation:type', [
                'conversation:type_operator' => select::EQUAL_TO,
                'conversation:type_value' => api::MESSAGE_CONVERSATION_TYPE_GROUP,
            ], false],
            'Conversation name' => ['conversation:name', [
                'conversation:name_operator' => text::IS_EQUAL_TO,
                'conversation:name_value' => 'My conversation',
            ], true],
            'Conversation name (no match)' => ['conversation:name', [
                'conversation:name_operator' => text::IS_EQUAL_TO,
                'conversation:name_value' => 'Another conversation',
            ], false],
            'Conversation enabled' => ['conversation:enabled', [
                'conversation:enabled_operator' => boolean_select::CHECKED,
            ], true],
            'Conversation enabled (no match)' => ['conversation:enabled', [
                'conversation:enabled_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Conversation time created' => ['conversation:timecreated', [
                'conversation:timecreated_operator' => date::DATE_RANGE,
                'conversation:timecreated_from' => 1622502000,
            ], true],
            'Conversation time created (no match)' => ['conversation:timecreated', [
                'conversation:timecreated_operator' => date::DATE_RANGE,
                'conversation:timecreated_to' => 1622502000,
            ], false],

            // Author.
            'User firstname' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Zoe',
            ], true],
            'User firstname (no match)' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Aaron',
            ], false],

            // Recipient.
            'Recipient firstname' => ['recipient:firstname', [
                'recipient:firstname_operator' => text::IS_EQUAL_TO,
                'recipient:firstname_value' => 'Aaron',
            ], true],
            'Recipient firstname (no match)' => ['recipient:firstname', [
                'recipient:firstname_operator' => text::IS_EQUAL_TO,
                'recipient:firstname_value' => 'Zoe',
            ], false],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(string $filtername, array $filtervalues, bool $expectmatch): void {
        $this->resetAfterTest();

        // Test subject.
        $userone = $this->getDataGenerator()->create_user(['firstname' => 'Zoe']);
        $usertwo = $this->getDataGenerator()->create_user(['firstname' => 'Aaron']);

        $conversation = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$userone->id, $usertwo->id],
            'My conversation',
        );
        helper::send_fake_message_to_conversation($userone, $conversation->id, 'Hi');

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Messages', 'source' => messages::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'message:subject']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertEquals([
                ['No subject'],
            ], array_map('array_values', $content));
        } else {
            $this->assertEmpty($content);
        }
    }

    /**
     * Stress test datasource
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        // Test subject.
        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();

        $conversation = api::create_conversation(api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, [$userone->id, $usertwo->id]);
        helper::send_fake_message_to_conversation($userone, $conversation->id);

        $this->datasource_stress_test_columns(messages::class);
        $this->datasource_stress_test_columns_aggregation(messages::class);
        $this->datasource_stress_test_conditions(messages::class, 'message:subject');
    }
}

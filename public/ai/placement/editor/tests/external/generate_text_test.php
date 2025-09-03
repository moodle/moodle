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

namespace aiplacement_editor\external;

/**
 * Test generate text external webservice calls.
 *
 * @package    aiplacement_editor
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiplacement_editor\external\generate_text
 */
final class generate_text_test extends \advanced_testcase {
    /**
     * Test generate_text webservice.
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        set_config('enabled', 1, 'aiplacement_editor');
        $this->setAdminUser();

        // Get system context.
        $context = \core\context\system::instance();

        // Mock the manager class call.
        $response = new \core_ai\aiactions\responses\response_generate_text(success: true);
        $response->set_response_data(
            [
                'generatedcontent' => 'This was a test',
                'finishreason' => 'max_-tokens', // Alphanumext.
            ]
        );

        $mockmanager = $this->createMock(\core_ai\manager::class);
        $mockmanager->method('process_action')->willReturn($response);
        $mockmanager->method('is_action_available')->willReturn(true);
        $mockmanager->method('is_action_enabled')->willReturn(true);
        $mockmanager->method('is_action_enabled_in_context')->willReturn(true);
        \core\di::set(\core_ai\manager::class, function() use ($mockmanager) {
            return $mockmanager;
        });

        $_POST['sesskey'] = sesskey();
        $params = [
            'contextid' => $context->id,
            'prompttext' => 'This is a test',
        ];

        $result = \core_external\external_api::call_external_function(
            'aiplacement_editor_generate_text',
            $params,
        );

        $this->assertFalse($result['error']);
        $this->assertEquals('max_-tokens', $result['data']['finishreason']);
        $this->assertEquals('This was a test', $result['data']['generatedcontent']);
        $this->assertTrue($result['data']['success']);
    }
}

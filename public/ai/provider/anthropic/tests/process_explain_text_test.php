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

namespace aiprovider_anthropic;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/testcase_helper_trait.php');

/**
 * Tests for the explain_text processor of the Anthropic Claude provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_anthropic\process_explain_text::class)]
#[CoversClass(\aiprovider_anthropic\process_generate_text::class)]
#[CoversClass(\aiprovider_anthropic\abstract_processor::class)]
final class process_explain_text_test extends \advanced_testcase {
    use testcase_helper_trait;

    /** @var string A successful response JSON loaded from the fixture file. */
    protected string $responsebodyjson;

    /** @var \core_ai\provider */
    protected \core_ai\provider $provider;

    /** @var \core_ai\aiactions\base */
    protected \core_ai\aiactions\base $action;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_anthropic', 'text_request_success.json'));
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\explain_text::class,
            actionconfig: [
                'systeminstruction' => get_string('action_explain_text_instruction', 'core_ai'),
            ],
        );
        $this->action = new \core_ai\aiactions\explain_text(
            contextid: 1,
            userid: 1,
            prompttext: 'A passage of text to explain.',
        );
    }

    /**
     * Test query_ai_api for a successful explain_text call.
     */
    public function test_query_ai_api_success(): void {
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(200, ['Content-Type' => 'application/json'], $this->responsebodyjson));

        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Photosynthesis is the process plants use', $result['generatedcontent']);
    }

    /**
     * Test prepare_response success for explain_text.
     */
    public function test_prepare_response_success(): void {
        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => true,
            'id' => 'msg_def456',
            'generatedcontent' => 'An explanation of the passage.',
            'finishreason' => 'end_turn',
            'prompttokens' => 30,
            'completiontokens' => 25,
            'model' => 'claude-3-5-sonnet-20241022',
        ];

        $result = $method->invoke($processor, $response);
        $this->assertTrue($result->get_success());
        $this->assertEquals('explain_text', $result->get_actionname());
    }
}

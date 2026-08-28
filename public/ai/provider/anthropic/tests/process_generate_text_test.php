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

use core_ai\aiactions\base;
use core_ai\provider;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/testcase_helper_trait.php');

/**
 * Tests for the generate_text processor of the Anthropic Claude provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_anthropic\provider::class)]
#[CoversClass(\aiprovider_anthropic\process_generate_text::class)]
#[CoversClass(\aiprovider_anthropic\abstract_processor::class)]
final class process_generate_text_test extends \advanced_testcase {
    use testcase_helper_trait;

    /** @var string A successful response JSON loaded from the fixture file. */
    protected string $responsebodyjson;

    /** @var provider */
    protected provider $provider;

    /** @var base */
    protected base $action;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_anthropic', 'text_request_success.json'));
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'systeminstruction' => get_string('action_generate_text_instruction', 'core_ai'),
            ],
        );
        $this->create_action();
    }

    /**
     * Create the action used across tests.
     *
     * @param int $userid
     */
    private function create_action(int $userid = 1): void {
        $this->action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: $userid,
            prompttext: 'This is a test prompt',
        );
    }

    /**
     * Test create_request_object builds a valid Anthropic Messages API payload.
     */
    public function test_create_request_object(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-sonnet-4-5-20250929', $body->model);
        $this->assertEquals(8096, $body->max_tokens);
        $this->assertEquals('This is a test prompt', $body->messages[0]->content);
        $this->assertEquals('user', $body->messages[0]->role);
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
    }

    /**
     * Test create_request_object passes the hashed user id through as Anthropic request metadata.
     */
    public function test_create_request_object_includes_user_metadata(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, $this->provider->generate_userid('1'));

        $body = json_decode($request->getBody()->getContents());

        $this->assertEquals($this->provider->generate_userid('1'), $body->metadata->user_id);
        // The value must be a hash rather than the user id itself.
        $this->assertNotEquals('1', $body->metadata->user_id);
    }

    /**
     * Test create_request_object falls back to the shared default max_tokens for an
     * unlisted (admin-entered) model name.
     */
    public function test_create_request_object_with_custom_model(): void {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'model' => 'claude-not-bundled-yet',
                'max_tokens' => '',
            ],
        );
        $processor = new process_generate_text($provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-not-bundled-yet', $body->model);
        $this->assertEquals(\aiprovider_anthropic\aimodel\claude_base::DEFAULT_MAX_TOKENS, $body->max_tokens);
    }

    /**
     * Test a stored temperature is still sent for an unlisted model, where this plugin
     * cannot know whether the model rejects it.
     */
    public function test_create_request_object_custom_model_keeps_temperature(): void {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'model' => 'claude-not-bundled-yet',
                'temperature' => '0.4',
            ],
        );
        $processor = new process_generate_text($provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());
        $this->assertEquals(0.4, $body->temperature);
    }

    /**
     * Test a stored temperature is dropped for a model known to reject it, even when the
     * value predates the model being recognised as unsupported.
     */
    public function test_create_request_object_drops_unsupported_temperature(): void {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'model' => 'claude-opus-5',
                'temperature' => '0.4',
            ],
        );
        $processor = new process_generate_text($provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());
        $this->assertObjectNotHasProperty('temperature', $body);
    }

    /**
     * Test create_request_object includes system instruction when set.
     */
    public function test_create_request_object_with_system_instruction(): void {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'systeminstruction' => 'You are a helpful writing assistant.',
            ],
        );
        $processor = new process_generate_text($provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());
        $this->assertEquals('You are a helpful writing assistant.', $body->system);
    }

    /**
     * Test create_request_object includes temperature when set.
     */
    public function test_create_request_object_with_temperature(): void {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'temperature' => '0.7',
                'max_tokens' => '4096',
            ],
        );
        $processor = new process_generate_text($provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());
        $this->assertEquals(0.7, $body->temperature);
        $this->assertEquals(4096, $body->max_tokens);
    }

    /**
     * Test create_request_object preserves an explicit max_tokens of 0 rather than falling back to the default.
     */
    public function test_create_request_object_with_zero_max_tokens(): void {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'max_tokens' => '0',
            ],
        );
        $processor = new process_generate_text($provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = json_decode($request->getBody()->getContents());
        $this->assertEquals(0, $body->max_tokens);
    }

    /**
     * Test handle_api_success correctly parses an Anthropic response.
     */
    public function test_handle_api_success(): void {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        );

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');
        $result = $method->invoke($processor, $response);

        $this->assertTrue($result['success']);
        $this->assertEquals('msg_01XFDUDYJgAACzvnptvVoYEL', $result['id']);
        $this->assertStringContainsString('Photosynthesis is the process plants use', $result['generatedcontent']);
        $this->assertEquals('end_turn', $result['finishreason']);
        $this->assertEquals(18, $result['prompttokens']);
        $this->assertEquals(120, $result['completiontokens']);
        $this->assertEquals('claude-sonnet-4-5-20250929', $result['model']);
    }

    /**
     * Test handle_api_success returns an error when the response has no usable text content.
     */
    public function test_handle_api_success_no_content(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => 'msg_01XFDUDYJgAACzvnptvVoYEL',
                'content' => [],
                'stop_reason' => 'refusal',
                'usage' => ['input_tokens' => 5, 'output_tokens' => 0],
            ]),
        );

        $result = $method->invoke($processor, $response);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('refusal', $result['errormessage']);
    }

    /**
     * Test handle_api_success returns an error when the first content block is not text.
     */
    public function test_handle_api_success_non_text_content(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => 'msg_01XFDUDYJgAACzvnptvVoYEL',
                'content' => [['type' => 'tool_use', 'id' => 'toolu_1', 'name' => 'lookup', 'input' => []]],
                'stop_reason' => 'tool_use',
                'usage' => ['input_tokens' => 5, 'output_tokens' => 12],
            ]),
        );

        $result = $method->invoke($processor, $response);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('tool_use', $result['errormessage']);
    }

    /**
     * Test handle_api_success extracts the text block when adaptive thinking places a
     * thinking block ahead of it in the content array (e.g. Claude Sonnet 5, where adaptive
     * thinking is on by default and Claude decides per-request whether to think first).
     */
    public function test_handle_api_success_with_leading_thinking_block(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => 'msg_01XFDUDYJgAACzvnptvVoYEL',
                'content' => [
                    ['type' => 'thinking', 'thinking' => 'Let me consider how best to explain this.', 'signature' => 'abc123'],
                    ['type' => 'text', 'text' => 'Photosynthesis is the process plants use to convert light into energy.'],
                ],
                'stop_reason' => 'end_turn',
                'model' => 'claude-sonnet-5',
                'usage' => ['input_tokens' => 20, 'output_tokens' => 40],
            ]),
        );

        $result = $method->invoke($processor, $response);
        $this->assertTrue($result['success']);
        $this->assertEquals(
            'Photosynthesis is the process plants use to convert light into energy.',
            $result['generatedcontent'],
        );
    }

    /**
     * Test handle_api_error handles 5xx (server) errors.
     */
    public function test_handle_api_error_server(): void {
        $responses = $this->get_error_responses();
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        foreach ([500, 503] as $status) {
            $result = $method->invoke($processor, $responses[$status]);
            $this->assertEquals($status, $result['errorcode']);
        }
    }

    /**
     * Test handle_api_error handles 4xx (client) errors with Anthropic error body.
     */
    public function test_handle_api_error_client(): void {
        $responses = $this->get_error_responses();
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        $result = $method->invoke($processor, $responses[401]);
        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['errorcode']);
        $this->assertEquals('invalid x-api-key', $result['errormessage']);
        $this->assertEquals('401: ' . get_string('error:401', 'core_ai'), $result['error']);

        $result = $method->invoke($processor, $responses[429]);
        $this->assertFalse($result['success']);
        $this->assertEquals(429, $result['errorcode']);
        $this->assertEquals('Rate limit exceeded for requests.', $result['errormessage']);
        $this->assertEquals('429: ' . get_string('error:429', 'core_ai'), $result['error']);
    }

    /**
     * Test handle_api_error parses the Anthropic error detail for 5xx responses too.
     */
    public function test_handle_api_error_server_with_body(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        $response = new Response(
            529,
            ['Content-Type' => 'application/json'],
            json_encode([
                'type' => 'error',
                'error' => [
                    'type' => 'overloaded_error',
                    'message' => 'Overloaded, please retry.',
                ],
            ]),
        );

        $result = $method->invoke($processor, $response);
        $this->assertFalse($result['success']);
        $this->assertEquals(529, $result['errorcode']);
        $this->assertEquals('Overloaded, please retry.', $result['errormessage']);
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        ['mock' => $mock] = $this->get_mocked_http_client();

        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertEquals('msg_01XFDUDYJgAACzvnptvVoYEL', $result['id']);
        $this->assertStringContainsString('Photosynthesis is the process plants use', $result['generatedcontent']);
    }

    /**
     * Test query_ai_api returns an error for a non-200 response.
     */
    public function test_query_ai_api_error(): void {
        ['mock' => $mock] = $this->get_mocked_http_client();

        $mock->append(new Response(
            401,
            ['Content-Type' => 'application/json'],
            json_encode([
                'type' => 'error',
                'error' => [
                    'type' => 'authentication_error',
                    'message' => 'invalid x-api-key',
                ],
            ]),
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['errorcode']);
    }

    /**
     * Test prepare_response success.
     */
    public function test_prepare_response_success(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => true,
            'id' => 'msg_01XFDUDYJgAACzvnptvVoYEL',
            'generatedcontent' => 'Here is the generated text.',
            'finishreason' => 'end_turn',
            'prompttokens' => 18,
            'completiontokens' => 120,
            'model' => 'claude-sonnet-4-5-20250929',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
        $this->assertEquals($response['generatedcontent'], $result->get_response_data()['generatedcontent']);
        $this->assertEquals($response['model'], $result->get_response_data()['model']);
    }

    /**
     * Test prepare_response error.
     */
    public function test_prepare_response_error(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => false,
            'errorcode' => 401,
            'error' => 'authentication_error',
            'errormessage' => 'invalid x-api-key',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals($response['errorcode'], $result->get_errorcode());
        $this->assertEquals($response['error'], $result->get_error());
        $this->assertEquals($response['errormessage'], $result->get_errormessage());
    }

    /**
     * Test the full process method with a mocked HTTP client.
     */
    public function test_process(): void {
        $this->setAdminUser();
        ['mock' => $mock] = $this->get_mocked_http_client();

        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
    }
}

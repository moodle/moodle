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

namespace aitool_gemini;

use local_ai_manager\local\aitool_option_vertexai_authhandler;
use local_ai_manager\local\prompt_response;
use local_ai_manager\local\request_response;
use local_ai_manager\local\unit;
use local_ai_manager\local\usage;
use Psr\Http\Message\StreamInterface;

/**
 * Connector for Gemini.
 *
 * @package    aitool_gemini
 * @copyright  ISB Bayern, 2024
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connector extends \local_ai_manager\base_connector {

    /** @var string The access token to use for authentication against the Google API endpoint. */
    private string $accesstoken = '';

    #[\Override]
    public function get_models_by_purpose(): array {
        $textmodels = ['gemini-1.0-pro', 'gemini-1.0-pro-vision', 'gemini-1.5-flash', 'gemini-1.5-pro'];
        return [
                'chat' => $textmodels,
                'feedback' => $textmodels,
                'singleprompt' => $textmodels,
                'translate' => $textmodels,
                'itt' => ['gemini-1.5-pro', 'gemini-1.5-flash'],
        ];
    }

    #[\Override]
    public function get_unit(): unit {
        return unit::TOKEN;
    }

    #[\Override]
    public function execute_prompt_completion(StreamInterface $result, array $options = []): prompt_response {
        // phpcs:disable moodle.Commenting.TodoComment.MissingInfoInline
        /* TODO error handling: check if answer contains "stop", then the LLM will have successfully done something.
            If not, we need to do some error handling and return prompt_response::create_from_error(...
        */
        // phpcs:enable moodle.Commenting.TodoComment.MissingInfoInline
        $content = json_decode($result->getContents(), true);

        $textanswer = '';
        foreach ($content['candidates'][0]['content']['parts'] as $part) {
            $textanswer .= $part['text'];
        }
        return prompt_response::create_from_result(
                $this->instance->get_model(),
                new usage(
                        (float) $content['usageMetadata']['totalTokenCount'],
                        (float) $content['usageMetadata']['promptTokenCount'],
                        (float) $content['usageMetadata']['candidatesTokenCount']),
                $textanswer,
        );
    }

    #[\Override]
    public function get_prompt_data(string $prompttext, array $requestoptions): array {
        $messages = [];
        if (array_key_exists('conversationcontext', $requestoptions)) {
            foreach ($requestoptions['conversationcontext'] as $message) {
                switch ($message['sender']) {
                    case 'user':
                        $role = 'user';
                        break;
                    case 'ai':
                        $role = 'model';
                        break;
                    case 'system':
                        // Gemini does not have a system role. It's just a simple preprompt as user telling the AI how to behave.
                        $role = 'user';
                        break;
                    default:
                        throw new \moodle_exception('exception_badmessageformat', 'local_ai_manager');
                }
                $messages[] = [
                        'role' => $role,
                        'parts' => [
                                ['text' => $message['message']],
                        ],
                ];
            }
            $messages[] = [
                    'role' => 'user',
                    'parts' => [
                            ['text' => $prompttext],
                    ],
            ];
        } else if (array_key_exists('image', $requestoptions)) {
            $messages[] = [
                    'role' => 'user',
                    'parts' => [
                            ['text' => $prompttext],
                            [
                                    'inline_data' => [
                                            'mime_type' => mime_content_type($requestoptions['image']),
                                        // Gemini API expects the plain base64 encoded string,
                                        // without the leading data url metadata.
                                            'data' => explode(',', $requestoptions['image'])[1],
                                    ],
                            ],
                    ],
            ];
        } else {
            $messages[] = [
                    'role' => 'user',
                    'parts' => [
                            ['text' => $prompttext],
                    ],
            ];
        }
        return [
                'contents' => $messages,
                'generationConfig' => [
                        'temperature' => $this->instance->get_temperature(),
                ],
        ];
    }

    #[\Override]
    public function has_customvalue1(): bool {
        return true;
    }

    #[\Override]
    public function has_customvalue2(): bool {
        return true;
    }

    #[\Override]
    protected function get_headers(): array {
        $headers = parent::get_headers();
        if (in_array('Authorization', array_keys($headers))) {
            if ($this->instance->get_customfield2() === \aitool_gemini\instance::GOOGLE_BACKEND_GOOGLEAI) {
                unset($headers['Authorization']);
                $headers['x-goog-api-key'] = $this->get_api_key();
            } else {
                $headers['Authorization'] = 'Bearer ' . $this->accesstoken;
            }
        }
        return $headers;
    }

    #[\Override]
    public function make_request(array $data): request_response {
        if ($this->instance->get_customfield2() === instance::GOOGLE_BACKEND_VERTEXAI) {
            $vertexaiauthhandler =
                    new aitool_option_vertexai_authhandler($this->instance->get_id(), $this->instance->get_customfield3());
            try {
                $this->accesstoken = $vertexaiauthhandler->get_access_token();
            } catch (\moodle_exception $exception) {
                return request_response::create_from_error(0, $exception->getMessage(), $exception->getTraceAsString());
            }
            $requestresponse = parent::make_request($data);
            // We keep track of the time the cached access token expires. However, due latency, different clocks
            // on different servers etc. we could end up sending a request with an actually expired access token.
            // In this case we refresh our access token and re-submit the request ONE TIME.
            if ($vertexaiauthhandler->is_expired_accesstoken_reason_for_failing($requestresponse)) {
                try {
                    $vertexaiauthhandler->refresh_access_token();
                } catch (\moodle_exception $exception) {
                    return request_response::create_from_error(0, $exception->getMessage(), $exception->getTraceAsString());
                }
            }
        }
        return parent::make_request($data);
    }

    #[\Override]
    public function allowed_mimetypes(): array {
        // We use the inline_data for sending data to the API, so we basically support every format.
        // However, we restrict it to some basics.
        return ['image/png', 'image/jpeg', 'image/webp', 'image/heic', 'image/heiff', 'application/pdf'];
    }
}

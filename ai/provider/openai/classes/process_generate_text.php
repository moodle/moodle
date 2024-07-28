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

namespace aiprovider_openai;

use core\http_client;
use core_ai\aiactions\responses\response_base;
use core_ai\aiactions\responses\response_generate_text;
use core_ai\process_base;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class process text generation.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_text extends process_base {
    /** @var string The API endpoint to make requests against. */
    private string $aiendpoint = 'https://api.openai.com/v1/chat/completions';

    /** @var string The API model to use. */
    private string $model = 'gpt-4o';

    /**
     * Process the AI request.
     *
     * @return response_base The result of the action.
     */
    public function process(): response_base {
        // Check the rate limiter.
        $ratelimitcheck = $this->provider->is_request_allowed($this->action);
        if ($ratelimitcheck !== true) {
            return new response_generate_text(
                success: false,
                actionname: 'generate_text',
                errorcode: $ratelimitcheck['errorcode'],
                errormessage: $ratelimitcheck['errormessage'],
            );
        }

        $userid = $this->provider->generate_userid($this->action->get_configuration('userid'));
        $client = $this->provider->create_http_client($this->aiendpoint);

        // Create the request object.
        $requestobj = $this->create_request_object($this->action, $userid);

        // Make the request to the OpenAI API.
        $response = $this->query_ai_api($client, $requestobj);

        // Format the action response object.
        return $this->prepare_response($response);
    }

    /**
     * Query the AI service.
     *
     * @param http_client $client The http client.
     * @param \stdClass $requestobj The request object.
     * @return array The response from the AI service.
     */
    protected function query_ai_api(http_client $client, \stdClass $requestobj): array {
        $requestjson = json_encode($requestobj);

        try {
            // Call the external AI service.
            $response = $client->request('POST', '', [
                'body' => $requestjson,
            ]);

            // Double-check the response codes, in case of a non 200 that didn't throw an error.
            $status = $response->getStatusCode();
            if ($status == 200) {
                return $this->handle_api_success($response);
            } else {
                return $this->handle_api_error($status, $response);
            }
        } catch (RequestException $e) {
            // Handle any exceptions.
            return [
                'success' => false,
                'errorcode' => $e->getCode(),
                'errormessage' => $e->getMessage(),
            ];
        }

    }

    /**
     * Create the request object to send to the OpenAI API.
     * This object contains all the required parameters for the request.
     *
     * @param \core_ai\aiactions\base $action The action to process.
     * @param string $userid The user id.
     * @return \stdClass The request object to send to the OpenAI API.
     * @throws \coding_exception
     */
    private function create_request_object(\core_ai\aiactions\base $action, string $userid): \stdClass {
        // Create the user object.
        $userobj = new \stdClass();
        $userobj->role = 'user';
        $userobj->content = $action->get_configuration('prompttext');

        // Create the request object.
        $requestobj = new \stdClass();
        $requestobj->model = $this->model;
        $requestobj->user = $userid;

        // If there is a system string available, use it.
        $systeminstruction = $action->get_system_instruction();
        if (!empty($systeminstruction)) {
            $systemobj = new \stdClass();
            $systemobj->role = 'system';
            $systemobj->content = $systeminstruction;
            $requestobj->messages = [$systemobj, $userobj];
        } else {
            $requestobj->messages = [$userobj];
        }

        return $requestobj;
    }

    /**
     * Handle an error from the external AI api.
     *
     * @param int $status The status code.
     * @param ResponseInterface $response The response object.
     * @return array The error response.
     */
    protected function handle_api_error(int $status, ResponseInterface $response): array {
        $responsearr = [
            'success' => false,
            'errorcode' => $status,
        ];

        if ($status == 500) {
            $responsearr['errormessage'] = 'Internal server error.';
        } else if ($status == 503) {
            $responsearr['errormessage'] = 'Service unavailable.';
        } else {
            $responsebody = $response->getBody();
            $bodyobj = json_decode($responsebody->getContents());
            $responsearr['errormessage'] = $bodyobj->error->message;
        }

        return $responsearr;
    }

    /**
     * Handle a successful response from the external AI api.
     *
     * @param ResponseInterface $response The response object.
     * @return array The response.
     */
    protected function handle_api_success(ResponseInterface $response): array {
        $responsebody = $response->getBody();
        $bodyobj = json_decode($responsebody->getContents());

        return [
            'success' => true,
            'id' => $bodyobj->id,
            'fingerprint' => $bodyobj->system_fingerprint,
            'generatedcontent' => $bodyobj->choices[0]->message->content,
            'finishreason' => $bodyobj->choices[0]->finish_reason,
            'prompttokens' => $bodyobj->usage->prompt_tokens,
            'completiontokens' => $bodyobj->usage->completion_tokens,
        ];
    }

    /**
     * Prepare the response object.
     *
     * @param array $response The response object.
     * @return response_generate_text The action response object.
     * @throws \coding_exception
     */
    private function prepare_response(array $response): response_generate_text {
        if ($response['success']) {
            $generatedtext = new response_generate_text(
                success: true,
                actionname: 'generate_text',
            );
            $generatedtext->set_response($response);
            return $generatedtext;
        } else {
            return new response_generate_text(
                success: false,
                actionname: 'generate_text',
                errorcode: $response['errorcode'],
                errormessage: $response['errormessage'],
            );
        }
    }
}

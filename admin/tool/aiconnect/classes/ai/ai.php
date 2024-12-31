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
 * AI class
 *
 * @package    tool_aiconnect
 * @copyright  2024 Marcus Green
 * @author     Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_aiconnect\ai;

use curl;
use tool_aiconnect\event\make_request;
use moodle_exception;
/**
 * Contains most functionality
 *
 */
class ai {

    /**
     * API Key for chatgpt, ignored by ollama
     *
     * @var string
     */
    private string $apikey;

    /**
     * LLM Model e.g. llama2 or gpt4
     *
     * @var string
     */
    private $model;

    /**
     * The model used to generate the completion.
     * @var float
     */
    private float $temperature;

    /**
     * Endpoint URL
     * @var string
     */
    private string $endpoint;

    /**
     * Initialise default settings
     *
     * @param string $model
     */
    public function __construct(?string $model = null) {
        $this->model = $model ?? trim(explode(',', get_config('tool_aiconnect', 'model'))[0]);
        $this->apikey = get_config('tool_aiconnect', 'apikey');
        $this->temperature = get_config('tool_aiconnect', 'temperature');
        $this->endpoint = trim(get_config('tool_aiconnect', 'endpoint'));
    }

    /**
     * Makes a request with the given data and API key.
     *
     * @param array $data The data to send with the request.
     * @param string $apikey The API key to authenticate the request.
     * @param string $multipart TODO document this parameter
     * @return array The response from the request.
     * @throws moodle_exception If the API key is empty.
     */
    private function make_request(array $data, string $apikey, $multipart = null): array {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        if (empty($apikey)) {
            throw new moodle_exception('emptyapikey', 'tool_aiconnect', '', null,
                'Empty API Key.');
        }
        $headers = $multipart ? [
            "Content-Type: multipart/form-data",
        ] : [
            "Content-Type: application/json;charset=utf-8",
        ];

        $headers[] = "Authorization: Bearer $apikey";
        $curl = new curl();
        $options = [
            "CURLOPT_RETURNTRANSFER" => true,
            "CURLOPT_HTTPHEADER" => $headers,
        ];
        $start = microtime(true);
        if (debugging('', DEBUG_DEVELOPER) && get_config('tool_aiconnect', 'log_requests')) {
            $params = [
                'context' => \context_system::instance(),
                'other' => $data['messages'][0]['content'],
            ];
            $event = make_request::create($params);
            $event->trigger();
        }
        $jsonresponse = $curl->post($this->endpoint, json_encode($data), $options);
        $response = json_decode($jsonresponse, true);
        if ($response == null) {
            return ['curl_error' => $curl->get_info()->http_code, 'execution_time' => $executiontime];
        }

        if (isset($response['error'])) {
             throw new moodle_exception('endpointerror', 'tool_aiconnect', '', null,
                $response['error']['message']);
        }

        $end = microtime(true);
        $executiontime = round($end - $start, 2);

        return ['response' => $response, 'execution_time' => $executiontime];
    }

    /**
     * Generates a completion for the given prompt text.
     *
     * @param string $prompttext The prompt text.
     * @return string|array The generated completion or null if the model is empty.
     * @throws moodle_exception If the model is empty.
     */
    public function prompt_completion($prompttext)  {
        $data = $this->get_prompt_data($prompttext);
        $result = $this->make_request($data, $this->apikey);
        if (isset($result['choices'][0]['text'])) {
            return $result['choices'][0]['text'];
        } else if (isset($result['choices'][0]['message'])) {
            return $result['choices'][0]['message'];
        } else {
            return $result;
        }
    }

    /**
     * Retrieves the data for the prompt based on the URL and prompt text.
     *
     * @param string $prompttext The prompt text.
     * @return array The prompt data.
     */
    private function get_prompt_data(string $prompttext): array {
            $data = [
                'model' => $this->model,
                'temperature' => $this->temperature,
                'messages' => [
                    ['role' => 'system', 'content' => 'You ' . $prompttext],
                ],
            ];
            if (get_config('tool_aiconntect', 'json_format')) {
                $data['response_format'] = ['type' => 'json_object'];
            }
            return $data;
    }
    /**
     * Find what models the remote system has available
     *
     * @return \stdClass
     */
    public function get_models(): \stdClass {
        $url = new \moodle_url($this->endpoint);
        $ollama = true;
        // Ollama url.
        $modelquery = '/api/tags';
        if ($url->get_host() == 'api.openai.com') {
            $modelquery = '/v1/models';
            $ollama = false;
        }

        $modelsurl = $url->get_scheme().'://'.$url->get_host().':'.$url->get_port().$modelquery;
        $curl = new curl();
        $options['CURLOPT_HTTPHEADER'] = ["Authorization: Bearer $this->apikey"];

        $modeldata = json_decode($curl->get($modelsurl, null, $options));
        if (!$ollama) {
            $modeldata->models = $modeldata->data;
            foreach ($modeldata->models as $model) {
                $model->name = $model->id;
            }
        }

        return $modeldata;
    }

}


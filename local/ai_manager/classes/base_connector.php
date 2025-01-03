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

namespace local_ai_manager;

use core\http_client;
use core_plugin_manager;
use local_ai_manager\local\prompt_response;
use local_ai_manager\local\request_response;
use local_ai_manager\local\unit;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Base class for connector subplugins.
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_connector {

    /** @var base_instance the connector instance the connector is using */
    protected base_instance $instance;

    /**
     * Create the connector object with a given instance object.
     *
     * @param base_instance $instance the connector instance to use
     */
    public function __construct(base_instance $instance) {
        $this->instance = $instance;
    }

    /**
     * Define available models.
     *
     * @return array names of the available models
     */
    abstract public function get_models_by_purpose(): array;

    /**
     * Get the available models as plain array.
     *
     * @return array array of strings of model identifiers
     */
    final public function get_models(): array {
        $models = [];
        foreach ($this->get_models_by_purpose() as $modelarray) {
            $models = array_merge($models, $modelarray);
        }
        return array_unique($models);
    }

    /**
     * Returns the unit which this connector is using.
     *
     * @return unit the unit enum
     */
    abstract public function get_unit(): unit;

    /**
     * Getter for the endpoint url.
     *
     * @return string the endpoint url
     */
    protected function get_endpoint_url(): string {
        return $this->instance->get_endpoint();
    }

    /**
     * Getter for the api key.
     *
     * @return string the api key
     */
    protected function get_api_key(): string {
        return $this->instance->get_apikey();
    }

    /**
     * Retrieves the data for the prompt based on the prompt text.
     *
     * @param string $prompttext the prompt text
     * @param array $requestoptions the options of the request
     * @return array the prompt data
     */
    abstract public function get_prompt_data(string $prompttext, array $requestoptions): array;

    /**
     * Function to handle the result after the request has been made.
     *
     * This function extracts the data from the request result and puts it into a prompt_response object.
     *
     * @param StreamInterface $result the result of the request
     * @param array $options the request options
     * @return prompt_response the prompt response object containing the extracted data
     */
    abstract public function execute_prompt_completion(StreamInterface $result, array $options = []): prompt_response;

    /**
     * Defines if the connector uses the first customvalue attribute.
     *
     * @return bool if customvalue1 attribute is being used
     */
    public function has_customvalue1(): bool {
        return false;
    }

    /**
     * Defines if the connector uses the second customvalue attribute.
     *
     * @return bool if customvalue2 attribute is being used
     */
    public function has_customvalue2(): bool {
        return false;
    }

    /**
     * Getter for the connector instance being used by this connector.
     *
     * @return base_instance the connector instance
     */
    public function get_instance(): base_instance {
        return $this->instance;
    }

    /**
     * Function for declaring options this connector is supporting.
     *
     * @return array array of options
     */
    public function get_available_options(): array {
        return [];
    }

    /**
     * Makes a request to the specified URL with the given data and API key.
     *
     * Can be used for most tools without any changes. In case changes are needed, it's possible to overwrite, but please only do
     * if really necessary.
     *
     * @param array $data The data to send with the request.
     * @return array The response from the request.
     * @throws \moodle_exception If the API key is empty.
     */
    public function make_request(array $data): request_response {
        $client = new http_client([
                'timeout' => get_config('local_ai_manager', 'requesttimeout'),
                'verify' => !empty(get_config('local_ai_manager', 'verifyssl')),
        ]);

        $options['headers'] = $this->get_headers();
        $options['body'] = json_encode($data);

        try {
            $response = $client->post($this->get_endpoint_url(), $options);
        } catch (ClientExceptionInterface $exception) {
            return $this->create_error_response_from_exception($exception);
        }
        if ($response->getStatusCode() === 200) {
            $return = request_response::create_from_result($response->getBody());
        } else {
            $return = request_response::create_from_error(
                    $response->getStatusCode(),
                    get_string('error_sendingrequestfailed', 'local_ai_manager'),
                    $response->getBody()->getContents(),
                    $response->getBody()
            );
        }
        return $return;
    }

    /**
     * Helper function to retrieve all enabled connector plugins.
     *
     * @return array array of strings of enabled connector plugin names
     */
    final public static function get_all_connectors(): array {
        return core_plugin_manager::instance()->get_enabled_plugins('aitool');
    }

    /**
     * Helper function to create a request_response object if the request throws an exception.
     *
     * @param ClientExceptionInterface $exception the exception which has been thrown
     * @return request_response a request_response object containing the necessary information in a standardized way
     */
    final protected function create_error_response_from_exception(ClientExceptionInterface $exception): request_response {
        $message = '';
        // This is actually pretty bad, but it does not seem possible to get to these kind of errors through some kind of
        // Guzzle API functions, so we have to hope the cURL error messages are kinda stable.
        if (str_contains($exception->getMessage(), 'cURL error')) {
            if (str_contains($exception->getMessage(), 'cURL error 28')) {
                $message = get_string('exception_curl28', 'local_ai_manager');
            } else if (str_contains($exception->getMessage(), 'cURL error')) {
                $message = get_string('exception_curl', 'local_ai_manager');
            }
        } else {
            $message = $this->get_custom_error_message($exception->getCode(), $exception);
            if (empty($message)) {
                // If the tool specific connector does not provide a customized error message, we use our defaults.
                switch ($exception->getCode()) {
                    case 401:
                        $message = get_string('exception_http401', 'local_ai_manager');
                        break;
                    case 429:
                        $message = get_string('exception_http429', 'local_ai_manager');
                        break;
                    case 500:
                        $message = get_string('exception_http500', 'local_ai_manager');
                        break;
                    default:
                        $message = get_string('exception_default', 'local_ai_manager');
                }
            }
        }
        $debuginfo = $exception->getMessage() . '\n' . $exception->getTraceAsString() . '\n';
        if (method_exists($exception, 'getResponse') && !empty($exception->getResponse())) {
            $debuginfo .= $exception->getResponse()->getBody()->getContents();
        }
        return request_response::create_from_error($exception->getCode(), $message, $debuginfo,
                $exception->getResponse()->getBody());
    }

    /**
     * Function to determine the headers for the request.
     *
     * @return array associative array defining header key and value for the request
     */
    protected function get_headers(): array {
        return [
                'Authorization' => 'Bearer ' . $this->get_api_key(),
                'Content-Type' => 'application/json;charset=utf-8',
        ];
    }

    /**
     * Returns the allowed mimetypes.
     *
     * This can be overwritten in connector classes that are capable of files being submitted.
     *
     * @return array an array of allowed mimetypes
     */
    public function allowed_mimetypes(): array {
        return [];
    }

    /**
     * Provides a custom error message for a given error code.
     *
     * This method is intended to be overwritten by subclasses to provide customized error information.
     *
     * @param int $code the error code from the request of the external AI tool
     * @param ?ClientExceptionInterface $exception the exception (if there is any) to extract additional information from,
     *  can be null if no exception had been thrown
     * @return string the localized error message string
     */
    protected function get_custom_error_message(int $code, ?ClientExceptionInterface $exception = null): string {
        return '';
    }
}

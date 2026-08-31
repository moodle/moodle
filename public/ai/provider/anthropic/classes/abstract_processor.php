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

use core\http_client;
use GuzzleHttp\Psr7\Uri;
use core_ai\process_base;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Abstract processor for the Anthropic Claude provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class abstract_processor extends process_base {
    /** @var string The Anthropic Messages API endpoint. */
    public const ANTHROPIC_API_ENDPOINT = 'https://api.anthropic.com/v1/messages';

    /**
     * Get the endpoint URI.
     *
     * @return UriInterface
     */
    protected function get_endpoint(): UriInterface {
        $endpoint = $this->provider->actionconfig[$this->action::class]['settings']['endpoint']
            ?? self::ANTHROPIC_API_ENDPOINT;
        return new Uri($endpoint);
    }

    /**
     * Get the name of the model to use.
     *
     * @return string
     */
    protected function get_model(): string {
        return $this->provider->actionconfig[$this->action::class]['settings']['model']
            ?? helper::get_default_model();
    }

    /**
     * Get the model generation settings (max_tokens, temperature).
     *
     * @return array
     */
    protected function get_model_settings(): array {
        $settings = $this->provider->actionconfig[$this->action::class]['settings'];
        $modelsettings = [];
        if (isset($settings['max_tokens']) && $settings['max_tokens'] !== '') {
            $modelsettings['max_tokens'] = (int) $settings['max_tokens'];
        }

        $supportstemperature = helper::resolve_model($this->get_model())->supports_temperature();

        // Guard against a temperature value stored under an earlier version of this plugin,
        // before a given model was recognised as unsupported: a normal form save already
        // clears an unsupported value going forward, but pre-existing stored config is not
        // retroactively migrated. Some Claude models (Opus 4.7+, Sonnet 5+) reject temperature
        // outright with a 400 error, so this must never be sent for those models.
        if ($supportstemperature && isset($settings['temperature']) && $settings['temperature'] !== '') {
            $modelsettings['temperature'] = (float) $settings['temperature'];
        }
        return $modelsettings;
    }

    /**
     * Get the system instruction.
     *
     * @return string
     */
    protected function get_system_instruction(): string {
        return $this->provider->actionconfig[$this->action::class]['settings']['systeminstruction']
            ?? $this->action::get_system_instruction();
    }

    /**
     * Create the request object to send to the Anthropic API.
     *
     * @param string $userid The user id.
     * @return RequestInterface
     */
    abstract protected function create_request_object(string $userid): RequestInterface;

    /**
     * Handle a successful response from the Anthropic API.
     *
     * @param ResponseInterface $response The response object.
     * @return array The response.
     */
    abstract protected function handle_api_success(ResponseInterface $response): array;

    #[\Override]
    protected function query_ai_api(): array {
        $request = $this->create_request_object(
            userid: $this->provider->generate_userid($this->action->get_configuration('userid')),
        );
        $request = $this->provider->add_authentication_headers($request);
        $client = \core\di::get(http_client::class);
        try {
            $response = $client->send($request, [
                'base_uri' => $this->get_endpoint(),
                RequestOptions::HTTP_ERRORS => false,
            ]);
        } catch (RequestException $e) {
            return \core_ai\error\factory::create($e->getCode(), $e->getMessage())->get_error_details();
        }

        $status = $response->getStatusCode();
        if ($status === 200) {
            return $this->handle_api_success($response);
        } else {
            return $this->handle_api_error($response);
        }
    }

    /**
     * Handle an error response from the Anthropic API.
     *
     * @param ResponseInterface $response The response object.
     * @return array The error response.
     */
    protected function handle_api_error(ResponseInterface $response): array {
        $status = $response->getStatusCode();
        $bodyobj = json_decode($response->getBody()->getContents());
        $errormessage = $bodyobj->error->message ?? $response->getReasonPhrase();

        return \core_ai\error\factory::create($status, $errormessage)->get_error_details();
    }
}

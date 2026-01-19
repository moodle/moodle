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

namespace aiprovider_awsbedrock;

use Aws\Exception\AwsException;
use Aws\Result;
use core_ai\process_base;

/**
 * Class process text generation.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class abstract_processor extends process_base {
    /**
     * Get the name of the model to use.
     *
     * @return string
     */
    protected function get_model(): string {
        return $this->provider->actionconfig[$this->action::class]['settings']['model'];
    }

    /**
     * Get the AWS region this model uses.
     *
     * @return string
     */
    protected function get_region(): string {
        return $this->provider->actionconfig[$this->action::class]['settings']['awsregion'];
    }

    /**
     * Get the cross region inference if applicable.
     *
     * @return string|null
     */
    protected function get_cross_region_inference(): string|null {
        return $this->provider->actionconfig[$this->action::class]['settings']['cross_region_inference'] ?? null;
    }

    /**
     * Get the model settings.
     *
     * @return array
     */
    protected function get_model_settings(): array {
        $settings = $this->provider->actionconfig[$this->action::class]['settings'] ?? [];
        if (!empty($settings['modelextraparams'])) {
            // Custom model settings.
            $params = json_decode($settings['modelextraparams'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($params)) {
                foreach ($params as $key => $param) {
                    $settings[$key] = $param;
                }
            }
        }

        // Unset unnecessary settings.
        unset(
            $settings['model'],
            $settings['awsregion'],
            $settings['cross_region_inference'],
            $settings['systeminstruction'],
            $settings['providerid'],
            $settings['modelextraparams'],
        );
        return $settings;
    }

    /**
     * Get the system instructions.
     *
     * @return string
     */
    protected function get_system_instruction(): string {
        return $this->action::get_system_instruction();
    }

    /**
     * Create the request  to send to the external AI API.
     * This object contains all the required parameters for the request.
     *
     * @return array The request to send to the external AI API.
     */
    abstract protected function create_request(): array;

    /**
     * Handle a successful result from the external AI api.
     *
     * @param Result $result The result object.
     * @return array The response.
     */
    abstract protected function handle_api_success(Result $result): array;

    #[\Override]
    protected function query_ai_api(): array {
        $request = $this->create_request();
        $client = $this->provider->create_bedrock_client(
            region: $this->get_region(),
        );
        try {
            // Call the external AI service.
            $response = $client->invokeModel($request);
        } catch (AwsException $exception) {
            // Handle any exceptions.
            return $this->handle_api_error($exception);
        }
        return $this->handle_api_success($response);
    }

    /**
     * Handle an error from the external AI api.
     *
     * @param AwsException $exception The response object.
     * @return array The error response.
     */
    protected function handle_api_error(AwsException $exception): array {
        return [
            'success' => false,
            'errorcode' => $exception->getStatusCode(),
            'error' => $exception->getAwsErrorCode(),
            'errormessage' => $exception->getAwsErrorMessage(),
        ];
    }
}

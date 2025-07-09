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

namespace core_ai;

use core_ai\aiactions\base;
use core_ai\aiactions\responses\response_base;

/**
 * Base class for provider processors.
 *
 * Each provider processor should extend this class.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class process_base {
    /**
     * Class constructor.
     *
     * @param provider $provider The provider that will process the action.
     * @param base $action The action to process.
     */
    public function __construct(
        /** @var provider The provider that will process the action. */
        protected readonly provider $provider,
        /** @var base The action to process. */
        protected readonly base $action,
    ) {
    }

    /**
     * Process the AI request.
     *
     * @return response_base The result of the action.
     */
    public function process(): response_base {
        // Check the rate limiter.
        $ratelimitcheck = $this->provider->is_request_allowed($this->action);
        if ($ratelimitcheck !== true) {
            return $this->get_response(
                success: false,
                errorcode: $ratelimitcheck['errorcode'],
                error: $ratelimitcheck['error'],
                errormessage: $ratelimitcheck['errormessage'],
            );
        }

        // Format the action response object.
        return $this->prepare_response($this->query_ai_api());
    }

    /**
     * Query the AI service.
     *
     * @return array The response from the AI service.
     */
    abstract protected function query_ai_api(): array;

    /**
     * Prepare the response object.
     *
     * @param array $responsedata The response object.
     * @return response_base The action response object.
     */
    private function prepare_response(array $responsedata): response_base {
        if ($responsedata['success']) {
            $response = $this->get_response(
                success: true,
            );
            $response->set_response_data($responsedata);

            return $response;
        } else {
            return $this->get_response(
                success: false,
                errorcode: $responsedata['errorcode'],
                error: $responsedata['error'],
                errormessage: $responsedata['errormessage'],
            );
        }
    }

    /**
     * Get the instantiated Response Class for the action described by this processor.
     *
     * @param mixed ...$args The arguments to pass to the response class constructor.
     * @return response_base
     */
    protected function get_response(...$args): response_base {
        $responseclassname = $this->action::get_response_classname();
        return new $responseclassname(
            ...$args,
        );
    }
}

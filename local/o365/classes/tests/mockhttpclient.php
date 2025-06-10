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
 * A mock HTTP client allowing set responses.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\tests;

defined('MOODLE_INTERNAL') || die();

/**
 * A mock HTTP client allowing set responses.
 *
 * @codeCoverageIgnore
 */
class mockhttpclient extends \local_o365\httpclient {
    /** @var string The stored set response. */
    protected $mockresponse = '';

    /** @var int The index of the current response. */
    protected $curresponse = 0;

    /** @var array Array of executed requests. */
    protected $requests = [];

    /**
     * Set a response to return.
     *
     * @param string $response The response to return.
     */
    public function set_response($response) {
        $this->set_responses([$response]);
    }

    /**
     * Get executed requests.
     *
     * @return array Array of executed requests.
     */
    public function get_requests() {
        return $this->requests;
    }

    /**
     * Set multiple responses.
     *
     * Responses will be returned in sequence every time $this->request is called. I.e. The first
     * time request() is called, the first item in the response array will be returned, the second time it's
     * called the second item will be returned, etc.
     *
     * @param array $responses Array of responses.
     */
    public function set_responses(array $responses) {
        $this->curresponse = 0;
        $this->mockresponse = $responses;
    }

    /**
     * Return the set response instead of making the actual HTTP request.
     *
     * @param string $url The request URL
     * @param array $options Additional curl options.
     * @return string The set response.
     */
    protected function request($url, $options = array()) {
        $this->requests[] = [
            'url' => $url,
            'options' => $options,
        ];
        if (isset($this->mockresponse[$this->curresponse])) {
            $response = $this->mockresponse[$this->curresponse];
            $this->curresponse++;
            return $response;
        } else {
            $this->curresponse = 0;
            if (!isset($this->mockresponse[$this->curresponse])) {
                throw new \moodle_exception('No responses available.');
            }
            return $this->mockresponse[$this->curresponse];
        }
    }
}

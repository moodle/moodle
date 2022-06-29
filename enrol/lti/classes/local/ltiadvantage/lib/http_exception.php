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

namespace enrol_lti\local\ltiadvantage\lib;

use Packback\Lti1p3\Interfaces\IHttpException;
use Packback\Lti1p3\Interfaces\IHttpResponse;

/**
 * An implementation of IHTTPException, for use with the lib/lti1p3 library code.
 *
 * @package    enrol_lti
 * @copyright  2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class http_exception extends \Exception implements IHttpException {

    /** @var IHttpResponse the response to which this exception relates.*/
    protected $response;

    /**
     * Constructor.
     *
     * @param IHttpResponse $response a response instance.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(IHttpResponse $response, $message = "", $code = 0, \Throwable $previous = null) {

        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Get the http response.
     *
     * @return IHttpResponse the response.
     */
    public function getResponse(): IHttpResponse {
        return $this->response;
    }
}

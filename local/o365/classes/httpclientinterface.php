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
 * HTTP Client Interface.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365;

defined('MOODLE_INTERNAL') || die();

/**
 * HTTP Client Interface.
 */
interface httpclientinterface {
     /**
      * HTTP POST method
      *
      * @param string $url
      * @param array|string $params
      * @param array $options
      * @return bool
      */
    public function post($url, $params = '', $options = array());

     /**
      * HTTP GET method
      *
      * @param string $url
      * @param array $params
      * @param array $options
      * @return bool
      */
    public function get($url, $params = array(), $options = array());

     /**
      * HTTP PATCH method
      *
      * @param string $url
      * @param array|string $params
      * @param array $options
      * @return bool
      */
    public function patch($url, $params = '', $options = array());

    /**
     * HTTP DELETE method
     *
     * @param string $url
     * @param array $param
     * @param array $options
     * @return bool
     */
    public function delete($url, $param = array(), $options = array());

    /**
     * Set HTTP Request Header
     *
     * @param array $header
     */
    public function setheader($header);

     /**
      * Resets the HTTP Request headers (to prepare for the new request)
      */
    public function resetheader();
}

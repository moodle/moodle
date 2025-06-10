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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\CurlHelper;
use local_intellidata\repositories\statistics_repository;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class intelliboard_service {

    /** @var bool */
    protected $debug = false;
    /** @var array */
    protected $params = [];
    /** @var string */
    protected $apiurl = 'https://next.intelliboard.net/api/prospects/moodle/';

    /**
     * Service to send request to IntelliBoard.
     */
    public function __construct() {
        $this->debug = DebugHelper::debugenabled();
    }

    /**
     * Setup params for install action.
     */
    public function set_params_for_install() {
        $this->params = statistics_repository::get_site_info();
        $this->params['action'] = 'install';
    }

    /**
     * Send request to IB.
     *
     * @return object
     */
    public function send_request() {
        return CurlHelper::send_post($this->apiurl, $this->params, [], $this->debug);
    }
}

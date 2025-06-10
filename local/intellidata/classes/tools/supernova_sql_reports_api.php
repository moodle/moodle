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
 * Supernova sql reports API.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\tools;

/**
 * Supernova sql reports API.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class supernova_sql_reports_api {
    /** @var \curl $client */
    private $client;
    /** @var object $report */
    private $report;
    /** @var false */
    private $debug;

    /**
     * Supernova sql reports API construct.
     *
     * @param $report
     * @param false $debug
     */
    public function __construct($report, $debug = false) {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        $this->client = new \curl();
        $this->report = $report;
        $this->debug = $debug;
    }

    /**
     * Save report.
     *
     * @param array $data
     * @return bool|void
     */
    public function save(array $data) {
        $url = rtrim($this->report->service, '/') . "/api/moodle/custom-sql-reports/{$this->report->external_identifier}/save";
        $client = clone $this->client;
        $response = json_decode($client->post($url, $data, [
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_SSL_VERIFYHOST' => false,
            'CURLOPT_HTTPHEADER' => ['Accept: application/json'],
        ]), true);

        if (empty($response['message']) || $response['message'] !== 'ok') {
            if ($this->debug) {
                echo '<pre>';
                var_dump($response);exit;
            }

            return false;
        } else {
            return true;
        }
    }

    /**
     * Delete report.
     *
     * @return bool|void
     */
    public function delete() {
        $url = rtrim($this->report->service, '/') . "/api/moodle/custom-sql-reports/{$this->report->external_identifier}/delete";
        $client = clone $this->client;
        $response = json_decode($client->delete($url, [], [
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_SSL_VERIFYHOST' => false,
            'CURLOPT_HTTPHEADER' => ['Accept: application/json'],
        ]), true);

        if (empty($response['message']) || $response['message'] !== 'ok') {
            if ($this->debug) {
                echo '<pre>';
                var_dump($response);exit;
            }

            return false;
        } else {
            return true;
        }
    }
}

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

namespace core;

use Google_Service_Exception;
use Google_Service_YouTube;

/**
 * Tests for google library
 *
 * @package    core
 * @copyright  2021 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class googlelib_test extends \advanced_testcase {

    public function test_invalid_google_api_key(): void {
        global $CFG;
        require_once($CFG->libdir . '/google/lib.php');
        $client = get_google_client();
        $client->setDeveloperKey('invalid');
        $client->setScopes(array(Google_Service_YouTube::YOUTUBE_READONLY));
        $service = new Google_Service_YouTube($client);
        try {
            $service->videoCategories->listVideoCategories('snippet', ['regionCode' => 'us']);
            $this->fail('Exception expected');
        } catch (Google_Service_Exception $e) {
            $this->assertMatchesRegularExpression('/API key not valid/', $e->getMessage());
        }
    }
}

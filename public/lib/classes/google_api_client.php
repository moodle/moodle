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

use Google\Client;

/**
 * Google API Client integration for Moodle.
 *
 * @package    core
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_api_client extends Client{

    /**
     * Constructor.
     *
     * @param array $config Configuration array.
     */
    public function __construct(array $config = []) {
        $client = di::get(http_client::class);
        $this->setHttpClient($client);
        $config = $this->get_options($config);

        parent::__construct($config);
    }

    /**
     * Get the custom options for Google API Client integration in Moodle.
     *
     * @param array $settings The settings or options from client.
     * @return array
     */
    protected function get_options(array $settings): array {
        global $CFG;

        if (empty($settings['application_name'])) {
            // Configure the application name.
            $settings['application_name'] = 'Moodle ' . $CFG->release;
        }
        if (empty($settings['access_type'])) {
            // Configure the access type.
            $settings['access_type'] = 'online';
        }
        if (empty($settings['approval_prompt'])) {
            // Configure the approval prompt.
            $settings['approval_prompt'] = 'auto';
        }

        return $settings;
    }
}

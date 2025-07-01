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
 * Mailpit mail handling implementation.
 *
 * @package    core
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Simey Lameze <simey@moodle.com>
 */
namespace core\test;

use core\http_client;
use stdClass;

/**
 * Mailpit email handling class.
 *
 * @package    core
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mailpit_email_catcher implements email_catcher {

    /** @var http_client The http client object. */
    protected http_client $httpclient;

    /**
     * Constructor.
     *
     * @param string $baseuri The base uri for the mailpit server.
     */
    public function __construct(string $baseuri) {
        $this->httpclient = new http_client(['base_uri' => $baseuri]);
    }

    /**
     * Reset the mailpit server after a test.
     */
    public function reset_after_test(): void {
        $this->httpclient->delete_all();
    }

    /**
     * Delete all messages from the mailpit server.
     */
    public function delete_all() {
        $this->httpclient->delete('api/v1/messages');
    }

    /**
     * Get a list of messages from the mailpit server.
     *
     * @param bool $showdetails Optional. Whether to include detailed information in the messages. Default is false.
     * @return iterable
     */
    public function get_messages(bool $showdetails = false): iterable {
        $uri = 'api/v1/messages';
        $options = [
            'query' => [
                'start' => 0,
            ],
        ];

        do {
            $response = $this->httpclient->get(
                uri: $uri,
                options: $options,
            );

            $data = json_decode($response->getBody());
            foreach ($data->messages as $messagedata) {
                yield mailpit_message::create_from_api_response($this, $messagedata, $showdetails);
            }

            $options['query']['start'] = $data->start + $data->count;
        } while ($data->total > ($options['query']['start']));
    }

    /**
     * Get the message summary for a specific message.
     *
     * @param string $id The message id.
     * @return stdClass
     */
    public function get_message_data(string $id): stdClass {
        $response = $this->httpclient->get("api/v1/message/{$id}");

        return json_decode($response->getBody());
    }

    /**
     * Search for a message in the mailpit server.
     *
     * @param string $query The search query.
     * @return mixed
     */
    public function search(string $query): iterable {
        $uri = "api/v1/search?query={$query}";

        $response = $this->httpclient->get($uri);
        $data = json_decode($response->getBody());

        foreach ($data->messages as $messagedata) {
            yield mailpit_message::create_from_api_response($this, $messagedata);
        }
    }
}

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

namespace communication_matrix\tests\fixtures;

use core\http_client;

/**
 * Tests for the api_base class.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mocked_matrix_client extends \communication_matrix\matrix_client {
    /**
     * Public variant of the constructor.
     */
    public function __construct() {
        parent::__construct(...func_get_args());
    }

    /**
     * Reset the test client.
     */
    public static function reset_client(): void {
        self::$client = null;
    }

    /**
     * Set the http_client to the client specified.
     *
     * @param http_client $client
     */
    public static function set_client(http_client $client): void {
        self::$client = $client;
    }
}

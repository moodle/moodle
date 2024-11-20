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

use PhpXmlRpc\Client;
use PhpXmlRpc\Request;
use PhpXmlRpc\Response;
use PhpXmlRpc\Server;
use PhpXmlRpc\Value;

/**
 * phpxmlrpc library unit tests.
 *
 * @package   core
 * @category  test
 * @copyright 2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpxmlrpc_test extends \basic_testcase {

    /**
     * Ensure PhpXmlRpc availability.
     *
     * This may seem silly, sure it is. But it's a good way to verify
     * that the Moodle PSR-4 autoloader is working ok.
     *
     * @coversNothing
     */
    public function test_phpxmlrpc_availability(): void {
        // All these classes need to be at hand.
        $this->assertInstanceOf(\PhpXmlRpc\Client::class, new Client('https://example.com'));
        $this->assertInstanceOf(\PhpXmlRpc\Request::class, new Request(''));
        $this->assertInstanceOf(\PhpXmlRpc\Response::class, new Response(''));
        $this->assertInstanceOf(\PhpXmlRpc\Server::class, new Server());
        $this->assertInstanceOf(\PhpXmlRpc\Value::class, new Value());

        // Worth checking that we have removed this.
        $this->assertFileDoesNotExist(__DIR__ . '/../phpxmlrpc/Autoloader.php');

        // We cannnot live without our beloved readme.
        $this->assertFileExists(__DIR__ . '/../phpxmlrpc/readme_moodle.txt');
    }
}

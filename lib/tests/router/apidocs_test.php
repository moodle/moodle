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

namespace core\router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for user preference API handler.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\apidocs
 */
final class apidocs_test extends \advanced_testcase {
    public function test_openapi_docs(): void {
        $apidocs = new apidocs();

        $result = $apidocs->openapi_docs(new Response());
        $this->assertInstanceOf(ResponseInterface::class, $result);

        $this->assertTrue($result->hasHeader('Content-Type'));
        $this->assertEquals(['application/json'], $result->getHeader('Content-Type'));
    }
}

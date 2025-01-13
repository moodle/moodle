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

namespace core\router\schema\response;

use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the view_response response type.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\response\view_response
 */
final class view_response_test extends route_testcase {
    public function test_defaults(): void {
        $response = new response();

        // The default status code is 200.
        $this->assertSame(200, $response->get_status_code());
    }

    public function test_get_response(): void {
        global $OUTPUT;

        $request = new ServerRequest('GET', 'http://example.com');

        $response = new view_response(
            template: 'core/welcome',
            parameters: [
                'welcomemessage' => 'Hello, everybody!',
            ],
            request: $request,
        );

        $this->assertEquals('core/welcome', $response->get_template_name());
        $this->assertEquals(
            ['welcomemessage' => 'Hello, everybody!'],
            $response->get_parameters(),
        );

        $this->assertEquals($request, $response->get_request());
        $this->assertEquals(
            $OUTPUT->render_from_template('core/welcome', ['welcomemessage' => 'Hello, everybody!']),
            $response->get_response($this->get_router()->get_response_factory())->getBody(),
        );
    }
}

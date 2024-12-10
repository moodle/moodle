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

namespace core\router\schema\parameters;

use core\param;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use invalid_parameter_exception;
use Psr\Http\Message\ServerRequestInterface;
use ValueError;

/**
 * Tests for header objects.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\parameters\header_object
 */
final class header_object_test extends route_testcase {
    public function test_validate(): void {
        $param = new header_object(
            name: 'example',
            type: param::TEXT,
        );

        /** @var ServerRequestInterface $request */ // phpcs:ignore moodle.Commenting.InlineComment.DocBlock
        $request = (new ServerRequest('GET', 'http://example.com'))
            // A known header.
            ->withHeader('Accept', 'application/json')
            // An unknown header is kept.
            ->withHeader('X-Example', 'example')
            // A known header with multiple values.
            ->withAddedHeader('X-Multiple', 'value1')
            ->withAddedHeader('X-Multiple', 'value2')
            // An unknown header with multiple values.
            ->withAddedHeader('X-Unknown', 'value1')
            ->withAddedHeader('X-Unknown', 'value2');

        $result = $param->validate($request);
        $this->assertInstanceOf(ServerRequestInterface::class, $result);

        $this->assertEquals('application/json', $result->getHeaderLine('Accept'));
        $this->assertEquals('example', $result->getHeaderLine('X-Example'));
        $this->assertEquals(['value1', 'value2'], $result->getHeader('X-Multiple'));
        $this->assertEquals(['value1', 'value2'], $result->getHeader('X-Unknown'));
    }

    public function test_required_missing(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $param = new header_object(
            name: 'example',
            type: param::TEXT,
            required: true,
        );

        $this->expectException(invalid_parameter_exception::class);
        $param->validate($request);
    }

    public function test_optional_default(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $param = new header_object(
            name: 'example',
            type: param::TEXT,
            required: false,
            default: 'default',
        );

        $request = $param->validate($request);
        $this->assertEquals('default', $request->getHeaderLine('example'));
    }

    public function test_optional_without_default(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $param = new header_object(
            name: 'example',
            type: param::TEXT,
            required: false,
        );

        $request = $param->validate($request);
        $this->assertEquals(null, $request->getHeaderLine('example'));
    }

    public function test_multiple_allowed(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $request = $request
            ->withAddedHeader('example', 'value1')
            ->withAddedHeader('example', 'value2');
        $param = new header_object(
            name: 'example',
            type: param::TEXT,
            multiple: true,
        );

        $request = $param->validate($request);
        $this->assertEquals(['value1', 'value2'], $request->getHeader('example'));
    }

    public function test_multiple_not_allowed(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $request = $request
            ->withAddedHeader('example', 'value1')
            ->withAddedHeader('example', 'value2');
        $param = new header_object(
            name: 'example',
            type: param::TEXT,
            multiple: false,
        );

        $this->expectException(invalid_parameter_exception::class);
        $param->validate($request);
    }

    public function test_boolean_param(): void {
        $request = (new ServerRequest('GET', 'http://example.com'))
            ->withHeader('example', 'true');
        $param = new header_object(
            name: 'example',
            type: param::BOOL,
        );

        $request = $param->validate($request);
        $this->assertEquals([true], $request->getHeader('example'));
    }

    public function test_multiple_boolean_param(): void {
        $request = (new ServerRequest('GET', 'http://example.com'))
            ->withAddedHeader('example', 'true')
            ->withAddedHeader('example', 'false')
            ->withAddedHeader('example', 'true');
        $param = new header_object(
            name: 'example',
            type: param::BOOL,
            multiple: true,
        );

        $request = $param->validate($request);
        $this->assertEquals([true, false, true], $request->getHeader('example'));
    }

    public function test_multiple_boolean_param_invalid(): void {
        $request = (new ServerRequest('GET', 'http://example.com'))
            ->withAddedHeader('example', 'true')
            ->withAddedHeader('example', '0')
            ->withAddedHeader('example', 'true');
        $param = new header_object(
            name: 'example',
            type: param::BOOL,
            multiple: true,
        );

        $this->expectException(ValueError::class);
        $param->validate($request);
    }
}

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

namespace core\router\parameters;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Tests for the language header.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\router\parameters\header_language
 */
final class header_language_test extends route_testcase {
    /**
     * Test that the parameter is valid when the component is not specified.
     */
    public function test_component_not_specified(): void {
        $param = new header_language();

        $request = new ServerRequest('GET', '/example');

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $param->validate($request),
        );
    }

    /**
     * Test that the parameter name is respected
     */
    public function test_name(): void {
        $param = new header_language(name: 'not_the_default_name');
        $this->assertEquals('not_the_default_name', $param->get_name());
    }

    /**
     * Test valid components.
     *
     * @param string $component
     * @dataProvider valid_values
     */
    public function test_valid_value(string $component): void {
        $param = new header_language();

        /** @var ServerRequestInterface $request */ // phpcs:ignore moodle.Commenting.InlineComment.DocBlock
        $request = (new ServerRequest('GET', '/example'))
            ->withAddedHeader('Language', $component);

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $param->validate($request),
        );
    }

    /**
     * Test invalid components.
     *
     * @param string $component
     * @dataProvider invalid_values
     */
    public function test_invalid_value(string $component): void {
        $param = new header_language();

        /** @var ServerRequestInterface */ // phpcs:ignore moodle.Commenting.InlineComment.DocBlock
        $request = (new ServerRequest('GET', '/example'))
            ->withAddedHeader('Language', $component);

        $this->expectException(\core\exception\invalid_parameter_exception::class);
        $param->validate($request);
    }

    /**
     * Data provider containing seemingly-valid components.
     *
     * @return array
     */
    public static function valid_values(): array {
        return [
            [''],
            ['en'],
        ];
    }

    /**
     * Data provider containing invalid components.
     *
     * @return array
     */
    public static function invalid_values(): array {
        return [
            ['de'],
            ['Something wrong!!!'],
        ];
    }
}

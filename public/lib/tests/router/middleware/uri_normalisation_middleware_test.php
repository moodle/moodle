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

namespace core\router\middleware;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Tests for uri_normalisation_middleware.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\middleware\uri_normalisation_middleware
 */
final class uri_normalisation_middleware_test extends \advanced_testcase {
    /**
     * Test the normalisation of URIs.
     *
     * @dataProvider data_provider
     * @param string $input The input URI.
     * @param string $expected The expected output URI.
     */
    public function test_normalisation(
        string $input,
        string $expected,
    ): void {
        $request = new ServerRequest('GET', $input);
        $handler = new class () implements \Psr\Http\Server\RequestHandlerInterface {
            #[\Override]
            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface {
                return new \GuzzleHttp\Psr7\Response();
            }
        };

        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function ($request) use ($expected) {
                    return $request->getUri()->getPath() === $expected;
                }),
            );

        $middleware = \core\di::get(uri_normalisation_middleware::class);
        $middleware->process($request, $handler);
    }

    /**
     * Data provider for test_normalisation.
     */
    public static function data_provider(): array {
        return [
            'Empty URI' => [
                '',
                '/',
            ],
            'Duplicate slashes' => [
                '/test//path',
                '/test/path',
            ],
            'Trailing slash' => [
                '/test/path/',
                '/test/path',
            ],
            'Multiple duplicate slashes' => [
                '/test///path//with//more//than//one',
                '/test/path/with/more/than/one',
            ],
        ];
    }
}

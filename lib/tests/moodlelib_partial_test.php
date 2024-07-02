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

/**
 * Unit tests for partial() in moodlelib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::partial
 */
final class moodlelib_partial_test extends \advanced_testcase {
    /**
     * Test that arguments to partial can be passed as anticipated.
     *
     * @param callable $callable The callable to partially apply.
     * @param array $initialargs The initial arguments to pass to the callable.
     * @param array $calledargs The arguments to pass to the partially applied callable.
     * @param mixed $expected The expected return value.
     * @dataProvider partial_args_provider
     */
    public function test_partial_args(
        callable $callable,
        array $initialargs,
        array $calledargs,
        mixed $expected,
    ): void {
        $this->assertEquals($expected, partial($callable, ...$initialargs)(...$calledargs));
    }

    /**
     * An example static method as part of the testcase.
     *
     * @param string $foo The first argument.
     * @param string $bar The second argument.
     * @param string $baz The third argument.
     * @param string $bum The fourth argument.
     */
    public static function example_static_method(
        string $foo,
        string $bar,
        string $baz,
        string $bum
    ): string {
        return implode('/', [$foo, $bar, $baz, $bum]);
    }

    /**
     * An example method as part of the testcase.
     *
     * @param string $foo The first argument.
     * @param string $bar The second argument.
     * @param string $baz The third argument.
     * @param string $bum The fourth argument.
     */
    public function example_instance_method(
        string $foo,
        string $bar,
        string $baz,
        string $bum
    ): string {
        return implode('/', [$foo, $bar, $baz, $bum]);
    }

    /**
     * Data provider for test_partial_args.
     *
     * @return array
     */
    public static function partial_args_provider(): array {
        return [
            'Using positional args' => [
                'str_contains',
                ['foobar'],
                ['foo'],
                true,
            ],
            'Using positional args swapped' => [
                'str_contains',
                ['foo'],
                ['foobar'],
                false,
            ],
            'Using named args' => [
                'str_contains',
                ['needle' => 'foo'],
                ['haystack' => 'foobar'],
                true,
            ],
            'Using named args on callable args only' => [
                'str_contains',
                ['foobar'],
                ['needle' => 'foo'],
                true,
            ],
            'Using named args on initial args only - instance method' => [
                [new self(), 'example_instance_method'],
                ['foo' => 'foo'],
                ['bar', 'baz', 'bum'],
                'foo/bar/baz/bum',
            ],
            'Using named args on called args only - instance method' => [
                [new self(), 'example_instance_method'],
                ['foo'],
                ['bar' => 'bar', 'baz' => 'baz', 'bum' => 'bum'],
                'foo/bar/baz/bum',
            ],
            'Using named args on initial args only - static method' => [
                [self::class, 'example_static_method'],
                ['foo' => 'foo'],
                ['bar', 'baz', 'bum'],
                'foo/bar/baz/bum',
            ],
            'Using named args on called args only - static method' => [
                [self::class, 'example_static_method'],
                ['foo'],
                ['bar' => 'bar', 'baz' => 'baz', 'bum' => 'bum'],
                'foo/bar/baz/bum',
            ],
        ];
    }
}

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core;

/**
 * Unit tests for parameter management.
 *
 * @package   core
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(param::class)]
final class param_test extends \advanced_testcase {
    /**
     * Test that the Moodle `from_type` method provides canonicalised parameter values.
     *
     * @param string $type
     * @param param $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('valid_param_provider')]
    public function test_from_type(string $type, param $expected): void {
        $this->assertEquals($expected, param::from_type($type));
    }

    /**
     * Data provider containing valid paramter types and their name mapping.
     */
    public static function valid_param_provider(): array {
        return [
            // Standard values.
            [PARAM_RAW, param::RAW],
            [PARAM_RAW_TRIMMED, param::RAW_TRIMMED],
            [PARAM_FLOAT, param::FLOAT],
            [PARAM_INT, param::INT],

            // Using enum values (why would you, but...).
            [param::RAW->value, param::RAW],
            [param::RAW_TRIMMED->value, param::RAW_TRIMMED],
            [param::FLOAT->value, param::FLOAT],
            [param::INT->value, param::INT],
            [param::COMPONENT->value, param::COMPONENT],

            // Some aliases (canonicalised) parameters.
            [PARAM_INTEGER, param::INT],
            [PARAM_NUMBER, param::FLOAT],
        ];
    }

    /**
     * Ensure that we throw an exception if an invalid parameter type is used.
     */
    public function test_from_type_invalid(): void {
        $this->expectException(\coding_exception::class);
        param::from_type('not_a_param');
    }

    /**
     * Test that deprecated parameters are marked as such.
     *
     * @param param $param
     * @param bool $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('is_deprecated_provider')]
    public function test_is_deprecated(param $param, bool $expected): void {
        $this->assertEquals(
            $expected,
            $param->is_deprecated(),
        );
    }

    /**
     * Provider for deprecated parameter types.
     *
     * @return array
     */
    public static function is_deprecated_provider(): array {
        return [
            // Some undeprecated parameters.
            [param::RAW, false],
            [param::RAW_TRIMMED, false],
            [param::INT, false],

            // Some deprecated parameters.
            [param::INTEGER, true],
            [param::NUMBER, true],
            [param::CLEAN, true],
        ];
    }


    /**
     * Test that finally deprecated params throw an exception when cleaning.
     *
     * @param param $param
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('deprecated_param_provider')]
    public function test_deprecated_params_except(param $param): void {
        $this->expectException(\coding_exception::class);
        $param->clean('foo');
    }

    /**
     * Provider for deprecated parameters.
     *
     * @return array
     */
    public static function deprecated_param_provider(): array {
        return array_map(
            fn (param $param): array => [$param],
            array_filter(
                param::cases(),
                function (param $param): bool {
                    if ($attribute = deprecation::from($param)) {
                        return $attribute->emit && $attribute->final;
                    }
                    return false;
                },
            ),
        );
    }

    /**
     * Test that valid parameters clean correctly (i.e. return the input value).
     *
     * @param string $input
     * @param \core\param $param
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('valid_param_clean_provider')]
    public function test_param_clean(
        string $input,
        \core\param $param,
    ): void {
        $cleaned = $param->clean($input);
        $this->assertEquals($input, $cleaned);
    }

    /**
     * Data provider for the valid provider for param::clean.
     *
     * @return array<param|string>[]
     */
    public static function valid_param_clean_provider(): array {
        return [
            ['1', param::INT],
            ['1.5', param::FLOAT],
            ['foo', param::ESM_PATH],
            ['foo/bar', param::ESM_PATH],
            ['foo/bar/baz', param::ESM_PATH],
            ['@moodle/lms/example', param::ESM_PATH],
            ['react/jsx-runtime', param::ESM_PATH],
            ['button.small', param::ESM_PATH],
            ['@moodlehq/design-system/button.small', param::ESM_PATH],
        ];
    }

    /**
     * Test that valid parameters clean correctly (i.e. return the input value).
     *
     * @param string $input
     * @param \core\param $param
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('valid_param_clean_invalid_provider')]
    public function test_param_clean_invalid(
        string $input,
        \core\param $param,
    ): void {
        $cleaned = $param->clean($input);
        $this->assertEquals('', $cleaned);
    }

    /**
     * Data provider for the invalid provider for param::clean.
     *
     * @return array<param|string>[]
     */
    public static function valid_param_clean_invalid_provider(): array {
        return [
            ['foo@', param::ESM_PATH],
            ['@moodle/lms/@example', param::ESM_PATH],
        ];
    }
}

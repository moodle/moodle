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
 * Tests for \core\deprecated and \core\deprecation.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\deprecated
 * @covers \core\deprecated_with_reference
 * @covers \core\deprecation
 */
class deprecation_test extends \advanced_testcase {
    /**
     * @dataProvider emit_provider
     */
    public function test_emit(
        array $args,
        bool $expectdebugging,
        bool $expectexception,
    ): void {
        if ($expectexception) {
            $this->expectException(\coding_exception::class);
        }

        $attribute = new deprecated(
            ...$args,
            replacement: 'Test replacement',
        );

        $rc = new \ReflectionClass(deprecation::class);
        $method = $rc->getMethod('emit_deprecation_notice');
        $method->invoke(null, $attribute);

        if ($expectdebugging) {
            $this->assertdebuggingcalledcount(1);
        }
    }

    public static function emit_provider(): array {
        return [
            [
                [
                    'final' => false,
                    'emit' => false,
                ],
                false,
                false,
            ],
            [
                [
                    'final' => false,
                    'emit' => true,
                ],
                true,
                false,
            ],
            [
                [
                    'final' => true,
                    'emit' => false,
                ],
                false,
                false,
            ],
            [
                [
                    'final' => true,
                    'emit' => true,
                ],
                false,
                true,
            ],
        ];
    }

    /**
     * @dataProvider get_deprecation_string_provider
     */
    public function test_get_deprecation_string(
        ?string $replacement,
        ?string $since,
        ?string $reason,
        ?string $mdl,
        string $expected,
    ): void {
        $attribute = new deprecated_with_reference(
            owner: 'Test description',
            replacement: $replacement,
            since: $since,
            reason: $reason,
            mdl: $mdl,
            final: false,
            emit: true,
        );

        $this->assertEquals(
            $expected,
            deprecation::get_deprecation_string($attribute),
        );

        $rc = new \ReflectionClass(deprecation::class);
        $method = $rc->getMethod('emit_deprecation_notice');
        $method->invoke(null, $attribute);

        $this->assertDebuggingCalled($expected);
    }

    public static function get_deprecation_string_provider(): array {
        return [
            [
                'Test replacement',
                null,
                null,
                null,
                'Deprecation: Test description has been deprecated. Use Test replacement instead.',
            ],
            [
                'Test replacement',
                '4.1',
                null,
                null,
                'Deprecation: Test description has been deprecated since 4.1. Use Test replacement instead.',
            ],
            [
                'Test replacement',
                null,
                'Test reason',
                null,
                'Deprecation: Test description has been deprecated. Test reason. Use Test replacement instead.',
            ],
            [
                'Test replacement',
                null,
                null,
                null,
                'Deprecation: Test description has been deprecated. Use Test replacement instead.',
            ],
            [
                'Test replacement',
                null,
                null,
                'https://docs.moodle.org/311/en/Deprecated',
                'Deprecation: Test description has been deprecated. Use Test replacement instead. See https://docs.moodle.org/311/en/Deprecated for more information.',
            ],
            [
                'Test replacement',
                '4.1',
                'Test reason',
                'https://docs.moodle.org/311/en/Deprecated',
                'Deprecation: Test description has been deprecated since 4.1. Test reason. Use Test replacement instead. See https://docs.moodle.org/311/en/Deprecated for more information.',
            ],
            [
                null,
                null,
                'Test reason',
                null,
                'Deprecation: Test description has been deprecated. Test reason.',
            ],
            [
                null,
                null,
                null,
                'MDL-80677',
                'Deprecation: Test description has been deprecated. See MDL-80677 for more information.',
            ],
        ];
    }

    public function test_deprecated_without_replacement(): void {
        $this->expectException(\coding_exception::class);
        new deprecated(
            replacement: null,
        );
    }

    /**
     * @dataProvider from_provider
     */
    public function test_from($reference, bool $isdeprecated): void {
        $attribute = deprecation::from($reference);
        if ($isdeprecated) {
            $this->assertInstanceOf(deprecated::class, $attribute);
            $this->assertTrue(deprecation::is_deprecated($reference));
            $this->assertDebuggingNotCalled();

            deprecation::emit_deprecation_if_present($reference);
            $this->assertDebuggingCalled(deprecation::get_deprecation_string($attribute));
        } else {
            $this->assertNull($attribute);
            $this->assertFalse(deprecation::is_deprecated($reference));
            deprecation::emit_deprecation_if_present($reference);
            $this->assertDebuggingNotCalled();
        }
    }

    public function test_from_object(): void {
        require_once(dirname(__FILE__) . '/fixtures/deprecated_fixtures.php');

        $this->assertNull(deprecation::from(new \core\fixtures\not_deprecated_class()));
        $this->assertInstanceOf(deprecated::class, deprecation::from([new \core\fixtures\deprecated_class()]));
    }

    public static function from_provider(): array {
        require_once(dirname(__FILE__) . '/fixtures/deprecated_fixtures.php');
        return [
            // Classes.
            [\core\fixtures\deprecated_class::class, true],
            [[\core\fixtures\deprecated_class::class], true],
            [\core\fixtures\not_deprecated_class::class, false],
            [[\core\fixtures\not_deprecated_class::class], false],

            // Class properties.
            [\core\fixtures\deprecated_class::class . '::deprecatedproperty', true],
            [[\core\fixtures\deprecated_class::class, 'deprecatedproperty'], true],

            [\core\fixtures\deprecated_class::class . '::notdeprecatedproperty', false],
            [[\core\fixtures\deprecated_class::class, 'notdeprecatedproperty'], false],

            // Class constants.
            [\core\fixtures\deprecated_class::class . '::DEPRECATED_CONST', true],
            [[\core\fixtures\deprecated_class::class, 'DEPRECATED_CONST'], true],

            [\core\fixtures\deprecated_class::class . '::NOT_DEPRECATED_CONST', false],
            [[\core\fixtures\deprecated_class::class, 'NOT_DEPRECATED_CONST'], false],

            // Class methods.
            [\core\fixtures\deprecated_class::class . '::deprecated_method', true],
            [[\core\fixtures\deprecated_class::class, 'deprecated_method'], true],

            [\core\fixtures\deprecated_class::class . '::not_deprecated_method', false],
            [[\core\fixtures\deprecated_class::class, 'not_deprecated_method'], false],

            // Non-existent class.
            ['non_existent_class', false],
            [['non_existent_class'], false],

            // Non-existent feature in an existent class.
            [[\core\fixtures\not_deprecated_class::class, 'no_such_method'], false],

            // Not-deprecated class.
            [\core\fixtures\not_deprecated_class::class, false],

            // Deprecated global function.
            ['core\fixtures\deprecated_function', true],
            ['core\fixtures\not_deprecated_function', false],
        ];
    }
}

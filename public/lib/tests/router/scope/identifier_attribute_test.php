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

namespace core\router\scope;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see identifier_attribute}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(identifier_attribute::class)]
final class identifier_attribute_test extends \advanced_testcase {
    /**
     * Test identifier_attribute construction and get_identifier() with valid scope identifiers.
     *
     * @param string $inputidentifier The raw input scope identifier.
     * @param string|null $expectedidentifier The expected scope identifier.
     * @param string|null $expectedexception The expected exception message, if any.
     */
    #[DataProvider('scope_identifiers_provider')]
    public function test_scope_identifier_construction(
        string $inputidentifier,
        ?string $expectedidentifier,
        ?string $expectedexception,
    ): void {
        if ($expectedexception !== null) {
            $this->expectException(\coding_exception::class);
            $this->expectExceptionMessage($expectedexception);
        }

        $attribute = new identifier_attribute($inputidentifier);

        $this->assertSame($expectedidentifier, $attribute->get_identifier());
    }

    /**
     * Data provider for valid scope identifiers.
     *
     * @return array
     */
    public static function scope_identifiers_provider(): array {
        return [
            'valid scope identifier (simple lowercase)' => [
                'profile',
                'profile',
                null,
            ],
            'valid scope identifier (lowercase with underscore)' => [
                'user_read',
                'user_read',
                null,
            ],
            'valid scope identifier (lowercase with numbers)' => [
                'write_123',
                'write_123',
                null,
            ],
            'valid scope identifier (starts with letter, followed by number)' => [
                'a1_b2',
                'a1_b2',
                null,
            ],
            'invalid scope identifier (empty string)' => [
                '',
                null,
                'OAuth2 scope identifier cannot be empty.',
            ],
            'invalid scope identifier (only spaces)' => [
                '   ',
                null,
                'OAuth2 scope identifier cannot be empty.',
            ],
            'invalid scope identifier (starts with underscore)' => [
                '_profile',
                null,
                "Invalid OAuth2 scope identifier '_profile'. Scope identifiers must start with a letter and consist of " .
                "lowercase letters, numbers, and underscores.",
            ],
            'invalid scope identifier (starts with number)' => [
                '1profile',
                null,
                "Invalid OAuth2 scope identifier '1profile'. Scope identifiers must start with a letter and consist of " .
                "lowercase letters, numbers, and underscores.",
            ],
            'invalid scope identifier (contains uppercase)' => [
                'Profile',
                null,
                "Invalid OAuth2 scope identifier 'Profile'. Scope identifiers must start with a letter and consist of " .
                "lowercase letters, numbers, and underscores.",
            ],
            'invalid scope identifier (contains hyphen)' => [
                'profile-scope',
                null,
                "Invalid OAuth2 scope identifier 'profile-scope'. Scope identifiers must start with a letter and consist " .
                "of lowercase letters, numbers, and underscores.",
            ],
            'invalid scope identifier (contains spaces inside)' => [
                'profile scope',
                null,
                "Invalid OAuth2 scope identifier 'profile scope'. Scope identifiers must start with a letter and consist " .
                "of lowercase letters, numbers, and underscores.",
            ],
            'invalid scope identifier (contains special characters)' => [
                'profile$',
                null,
                "Invalid OAuth2 scope identifier 'profile$'. Scope identifiers must start with a letter and consist of " .
                "lowercase letters, numbers, and underscores.",
            ],
        ];
    }
}

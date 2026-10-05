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

declare(strict_types=1);

namespace tiny_media;

use advanced_testcase;

/**
 * Unit tests for the \tiny_media\plugininfo class.
 *
 * @package     tiny_media
 * @copyright   2025 Moodle Pty Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversMethod(\tiny_media\plugininfo::class, 'is_enabled_for_external')]
final class plugininfo_test extends advanced_testcase {

    /**
     * Basic setup for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test the is_enabled_for_external method.
     *
     * @param bool $guest True to use guest user.
     * @param bool $expectedenabled Expected result.
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('is_enabled_for_external_provider')]
    public function test_is_enabled_for_external(bool $guest, bool $expectedenabled): void {
        global $CFG;

        $generator = $this->getDataGenerator();
        if ($guest) {
            $this->setGuestUser();
        } else {
            $user = $generator->create_user();
            $this->setUser($user);
        }
        $context = \context_system::instance();

        $this->assertEquals($expectedenabled, plugininfo::is_enabled_for_external($context, ['pluginname' => 'media']));
    }

    /**
     * Data provider for test_is_enabled_for_external.
     *
     * @return array
     */
    public static function is_enabled_for_external_provider(): array {
        return [
            ['guest' => false, 'expectedenabled' => true],
            ['guest' => true, 'expectedenabled' => false],
        ];
    }
}

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

namespace core_badges;

use core_badges\png_metadata_handler;

/**
 * Unit tests for PNG metadata handler
 *
 * @package    core_badges
 * @covers     \core_badges\png_metadata_handler
 * @copyright  2025 Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Dai Nguyen Trong <ngtrdai@hotmail.com>
 * @author     Sara Arjona <sara@moodle.com>
 */
final class png_metadata_handler_test extends \advanced_testcase {

    /**
     * Create a valid PNG file content for testing
     *
     * @return string The PNG file content
     */
    protected function create_test_png(): string {
        global $CFG;

        $badgepath = $CFG->dirroot . '/badges/tests/behat/badge.png';
        return file_get_contents($badgepath);
    }

    /**
     * Create a valid JPG file content for testing
     *
     * @return string The PNG file content
     */
    protected function create_test_jpg(): string {
        global $CFG;

        $badgepath = $CFG->dirroot . '/badges/tests/fixtures/badge.jpg';
        return file_get_contents($badgepath);
    }

    /**
     * Test PNG metadata handler constructor with valid PNG.
     */
    public function test_constructor_valid_png(): void {
        $this->resetAfterTest();

        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);
        $this->assertInstanceOf(png_metadata_handler::class, $handler);
    }

    /**
     * Test constructor with invalid PNG.
     */
    public function test_constructor_invalid_png(): void {
        $this->resetAfterTest();

        $content = $this->create_test_jpg();
        $handler = new png_metadata_handler($content);
        $this->assertDebuggingCalled('This is not a valid PNG image');
        $this->assertInstanceOf(png_metadata_handler::class, $handler);
    }

    /**
     * Test add_chunks method with valid chunks.
     *
     * @dataProvider add_chunks_provider
     * @param string $type The chunk type
     * @param string $key The key to add
     * @param string|null $value The value to add
     */
    public function test_add_chunks(string $type, string $key, ?string $value = null): void {
        $this->resetAfterTest();

        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);
        $this->assertTrue($handler->check_chunks($type, 'openbadge'));

        $newcontent = $handler->add_chunks($type, $key, $value);

        // Create new handler with modified content to verify.
        $newhandler = new png_metadata_handler($newcontent);
        $this->assertFalse($newhandler->check_chunks($type, $key));
        $this->assertDebuggingCalled('Key "' . $key . '" already exists in "' . $type . '" chunk.');
    }

    /**
     * Data provider for add_chunks test.
     *
     * @return array The data provider array
     */
    public static function add_chunks_provider(): array {
        return [
            'tEXt' => [
                'type' => 'tEXt',
                'key' => 'openbadge',
                'value' => 'http://example.com/badge',
            ],
            'iTXt' => [
                'type' => 'iTXt',
                'key' => 'openbadge',
                'value' => 'http://example.com/badge',
            ],
        ];
    }

    /**
     * Test add_chunks method with invalid chunk type.
     */
    public function test_add_chunks_invalid_type(): void {
        $this->resetAfterTest();

        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('Unsupported chunk type: zTXt');

        $handler->add_chunks('zTXt', 'openbadge', 'http://example.com/badge');
    }

    /**
     * Test add_chunks method with too long key.
     */
    public function test_add_chunks_long_key(): void {
        $this->resetAfterTest();

        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $longkey = str_repeat('a', 80);
        $this->assertTrue($handler->check_chunks('tEXt', $longkey));
        $newcontent = $handler->add_chunks('tEXt', $longkey, 'http://example.com/badge');
        $this->assertDebuggingCalled('Key is too big');

        $newhandler = new png_metadata_handler($newcontent);
        $this->assertFalse($newhandler->check_chunks('tEXt', $longkey));
        $this->assertDebuggingCalled('Key "' . $longkey . '" already exists in "tEXt" chunk.');
    }
}

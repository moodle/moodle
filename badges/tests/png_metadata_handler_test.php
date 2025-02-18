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

defined('MOODLE_INTERNAL') || die();

use core_badges\png_metadata_handler;

/**
 * Unit tests for PNG metadata handler
 *
 * @package    core_badges
 * @covers     \core_badges\png_metadata_handler
 * @copyright  2025 Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Dai Nguyen Trong <ngtrdai@hotmail.com>
 */
final class png_metadata_handler_test extends \advanced_testcase
{
    /**
     * Set up function for tests
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Create a valid PNG file content for testing
     *
     * @return string The PNG file content
     */
    protected function create_test_png(): string {
        // PNG signature
        $content = pack("C8", 137, 80, 78, 71, 13, 10, 26, 10);

        // IHDR chunk
        $ihdr = pack("N", 13); // length
        $ihdr .= "IHDR";
        $ihdr .= pack("N*", 100, 100); // width, height
        $ihdr .= pack("C*", 8, 6, 0, 0, 0); // bit depth, color type, compression, filter, interlace
        $ihdr .= pack("N", crc32("IHDR" . pack("N*", 100, 100) . pack("C*", 8, 6, 0, 0, 0)));

        // IEND chunk
        $iend = pack("N", 0);
        $iend .= "IEND";
        $iend .= pack("N", crc32("IEND"));

        return $content . $ihdr . $iend;
    }

    /**
     * Test PNG metadata handler constructor with valid PNG
     */
    public function test_constructor_valid_png(): void {
        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);
        $this->assertInstanceOf(png_metadata_handler::class, $handler);
    }

    /**
     * Test check_chunks method with non-existent chunk
     */
    public function test_check_chunks_non_existent(): void {
        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $this->assertTrue($handler->check_chunks('tEXt', 'openbadge'));
    }

    /**
     * Test add_chunks method with tEXt chunk
     */
    public function test_add_chunks_text(): void {
        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $newcontent = $handler->add_chunks('tEXt', 'openbadge', 'http://example.com/badge');

        // Create new handler with modified content to verify
        $newhandler = new png_metadata_handler($newcontent);
        $this->assertFalse($newhandler->check_chunks('tEXt', 'openbadge'));
    }

    /**
     * Test add_chunks method with iTXt chunk
     */
    public function test_add_chunks_itext(): void {
        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $newcontent = $handler->add_chunks('iTXt', 'openbadge', 'http://example.com/badge');

        // Create new handler with modified content to verify
        $newhandler = new png_metadata_handler($newcontent);
        $this->assertFalse($newhandler->check_chunks('iTXt', 'openbadge'));
    }

    /**
     * Test add_chunks method with invalid chunk type
     */
    public function test_add_chunks_invalid_type(): void {
        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('Unsupported chunk type: zTXt');

        $handler->add_chunks('zTXt', 'openbadge', 'http://example.com/badge');
    }

    /**
     * Test add_chunks method with too long key
     */
    public function test_add_chunks_long_key(): void {
        $this->resetDebugging();
        $content = $this->create_test_png();
        $handler = new png_metadata_handler($content);

        $longkey = str_repeat('a', 80);
        $handler->add_chunks('tEXt', $longkey, 'http://example.com/badge');

        $this->assertDebuggingCalled('Key is too big');
    }
}

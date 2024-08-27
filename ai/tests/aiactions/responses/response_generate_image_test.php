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

namespace core_ai\aiactions\responses;

use core\exception\coding_exception;
use core_ai\aiactions\responses\response_generate_image;

/**
 * Test response_generate_image_test action methods.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\aiactions\responses\response_generate_image
 */
final class response_generate_image_test extends \advanced_testcase {
    /**
     * Test get_basename.
     */
    public function test_get_success(): void {
        $actionresponse = new response_generate_image(
            success: true,
        );

        $this->assertTrue($actionresponse->get_success());
        $this->assertEquals('generate_image', $actionresponse->get_actionname());
    }

    /**
     * Test constructor with error.
     */
    public function test_construct_error(): void {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Error code and message must exist in an error response.');
        new response_generate_image(
            success: false,
        );
    }

    /**
     * Test set_response_data.
     */
    public function test_set_response_data(): void {
        $this->resetAfterTest();

        // Create a file to store.
        $fs = get_file_storage();
        $filerecord = new \stdClass();
        $filerecord->contextid = 1;
        $filerecord->component = 'core_ai';
        $filerecord->filearea = 'draft';
        $filerecord->itemid = 0;
        $filerecord->filepath = '/';
        $filerecord->filename = 'test.txt';
        $file = $fs->create_file_from_string($filerecord, 'This is a test file');
        $body = [
            'draftfile' => $file,
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image.png',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $this->assertEquals($file, $actionresponse->get_response_data()['draftfile']);
        $this->assertEquals('This is a revised prompt', $actionresponse->get_response_data()['revisedprompt']);
        $this->assertEquals('https://example.com/image.png', $actionresponse->get_response_data()['sourceurl']);
    }
}

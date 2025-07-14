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

use core_ai\aiactions\responses\response_generate_text;

/**
 * Test response_generate_image_test action methods.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\aiactions\responses\response_generate_text
 */
final class response_generate_text_test extends \advanced_testcase {
    /**
     * Test get_basename.
     */
    public function test_get_success(): void {
        $actionresponse = new response_generate_text(
            success: true,
        );

        $this->assertTrue($actionresponse->get_success());
        $this->assertEquals('generate_text', $actionresponse->get_actionname());
    }

    /**
     * Test constructor with error.
     */
    public function test_construct_error(): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Error code and name must exist in an error response.');
        new response_generate_text(
            success: false,
        );
    }

    /**
     * Test set_response_data.
     */
    public function test_set_response_data(): void {
        $this->resetAfterTest();
        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $this->assertEquals($body['id'], $actionresponse->get_response_data()['id']);
        $this->assertEquals($body['fingerprint'], $actionresponse->get_response_data()['fingerprint']);
        $this->assertEquals($body['generatedcontent'], $actionresponse->get_response_data()['generatedcontent']);
        $this->assertEquals($body['finishreason'], $actionresponse->get_response_data()['finishreason']);
        $this->assertEquals($body['prompttokens'], $actionresponse->get_response_data()['prompttokens']);
        $this->assertEquals($body['completiontokens'], $actionresponse->get_response_data()['completiontokens']);
    }
}

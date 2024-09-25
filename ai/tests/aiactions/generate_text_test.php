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

namespace core_ai\aiactions;

use core_ai\aiactions\responses\response_generate_text;
use core_ai\aiactions\generate_text;

/**
 * Test generate_text action methods.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\aiactions\base
 */
final class generate_text_test extends \advanced_testcase {

    /**
     * Test configure method.
     */
    public function test_configure(): void {
        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';

        $action = new generate_text(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext
        );
        $this->assertEquals($userid, $action->get_configuration('userid'));
        $this->assertEquals($prompttext, $action->get_configuration('prompttext'));
    }

    /**
     * Test store method.
     */
    public function test_store(): void {
        $this->resetAfterTest();
        global $DB;

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';

        $action = new generate_text(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $storeid = $action->store($actionresponse);

        // Check the stored record.
        $record = $DB->get_record('ai_action_generate_text', ['id' => $storeid]);
        $this->assertEquals($prompttext, $record->prompt);
        $this->assertEquals($body['id'], $record->responseid);
        $this->assertEquals($body['fingerprint'], $record->fingerprint);
        $this->assertEquals($body['generatedcontent'], $record->generatedcontent);
        $this->assertEquals($body['finishreason'], $record->finishreason);
        $this->assertEquals($body['prompttokens'], $record->prompttokens);
        $this->assertEquals($body['completiontokens'], $record->completiontoken);
    }
}

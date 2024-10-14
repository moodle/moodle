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

namespace mod_lesson;

use lesson_page_type_essay;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lesson/locallib.php');
require_once($CFG->dirroot . '/mod/lesson/pagetypes/essay.php');


/**
 * This class contains the test cases for some of the functions in the lesson essay page type class.
 *
 * @package   mod_lesson
 * @category  test
 * @copyright 2015 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
final class pagetypes_test extends \advanced_testcase {
    public function test_lesson_essay_extract_useranswer(): void {
        // Test that reponseformat is added when not present.
        $answer = 'O:8:"stdClass":6:{s:4:"sent";i:1;s:6:"graded";i:1;s:5:"score";s:1:"1";'
                . 's:6:"answer";s:64:"<p>This is my answer <b>with bold</b> and <i>italics</i><br></p>";'
                . 's:12:"answerformat";s:1:"1";s:8:"response";s:10:"Well done!";}';
        $userresponse = new \stdClass;
        $userresponse->sent = 1;
        $userresponse->graded = 1;
        $userresponse->score = 1;
        $userresponse->answer = "<p>This is my answer <b>with bold</b> and <i>italics</i><br></p>";
        $userresponse->answerformat = FORMAT_HTML;
        $userresponse->response = "Well done!";
        $userresponse->responseformat = FORMAT_HTML;
        $this->assertEquals($userresponse, lesson_page_type_essay::extract_useranswer($answer));

        // Test that reponseformat is not modified when present.
        $answer = 'O:8:"stdClass":7:{s:4:"sent";i:0;s:6:"graded";i:1;s:5:"score";s:1:"0";'
                . 's:6:"answer";s:64:"<p>This is my answer <b>with bold</b> and <i>italics</i><br></p>";'
                . 's:12:"answerformat";s:1:"1";s:8:"response";s:10:"Well done!";s:14:"responseformat";s:1:"2";}';
        $userresponse = new \stdClass;
        $userresponse->sent = 0;
        $userresponse->graded = 1;
        $userresponse->score = 0;
        $userresponse->answer = "<p>This is my answer <b>with bold</b> and <i>italics</i><br></p>";
        $userresponse->answerformat = FORMAT_HTML;
        $userresponse->response = "Well done!";
        $userresponse->responseformat = FORMAT_PLAIN;
        $this->assertEquals($userresponse, lesson_page_type_essay::extract_useranswer($answer));
    }
}

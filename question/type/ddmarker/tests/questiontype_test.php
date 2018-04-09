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

/**
 * Unit tests for the drag-and-drop markers question definition class.
 *
 * @package    qtype_ddmarker
 * @copyright  2012 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddmarker/tests/helper.php');


/**
 * Unit tests for the drag-and-drop markers question definition class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_test extends advanced_testcase {
    /** @var qtype_ddmarker instance of the question type class to test. */
    protected $qtype;

    protected function setUp() {
        $this->qtype = question_bank::get_qtype('ddmarker');;
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'ddmarker');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_save_question() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(array());

        $dd = $questiongenerator->create_question('ddmarker', 'zerodrag',
                array('category' => $cat->id));
        $actual = question_bank::load_question_data($dd->id);

        $this->assertCount(2, $actual->options->drags);
    }
}

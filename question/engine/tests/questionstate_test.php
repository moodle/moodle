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
 * This file contains tests for the question_state class and subclasses.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once($CFG->libdir . '/questionlib.php');


/**
 * Unit tests for the {@link question_state} class and subclasses.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_state_test extends advanced_testcase {
    public function test_is_active() {
        $this->assertFalse(question_state::$notstarted->is_active());
        $this->assertFalse(question_state::$unprocessed->is_active());
        $this->assertTrue(question_state::$todo->is_active());
        $this->assertTrue(question_state::$invalid->is_active());
        $this->assertTrue(question_state::$complete->is_active());
        $this->assertFalse(question_state::$needsgrading->is_active());
        $this->assertFalse(question_state::$finished->is_active());
        $this->assertFalse(question_state::$gaveup->is_active());
        $this->assertFalse(question_state::$gradedwrong->is_active());
        $this->assertFalse(question_state::$gradedpartial->is_active());
        $this->assertFalse(question_state::$gradedright->is_active());
        $this->assertFalse(question_state::$manfinished->is_active());
        $this->assertFalse(question_state::$mangaveup->is_active());
        $this->assertFalse(question_state::$mangrwrong->is_active());
        $this->assertFalse(question_state::$mangrpartial->is_active());
        $this->assertFalse(question_state::$mangrright->is_active());
    }

    public function test_is_finished() {
        $this->assertFalse(question_state::$notstarted->is_finished());
        $this->assertFalse(question_state::$unprocessed->is_finished());
        $this->assertFalse(question_state::$todo->is_finished());
        $this->assertFalse(question_state::$invalid->is_finished());
        $this->assertFalse(question_state::$complete->is_finished());
        $this->assertTrue(question_state::$needsgrading->is_finished());
        $this->assertTrue(question_state::$finished->is_finished());
        $this->assertTrue(question_state::$gaveup->is_finished());
        $this->assertTrue(question_state::$gradedwrong->is_finished());
        $this->assertTrue(question_state::$gradedpartial->is_finished());
        $this->assertTrue(question_state::$gradedright->is_finished());
        $this->assertTrue(question_state::$manfinished->is_finished());
        $this->assertTrue(question_state::$mangaveup->is_finished());
        $this->assertTrue(question_state::$mangrwrong->is_finished());
        $this->assertTrue(question_state::$mangrpartial->is_finished());
        $this->assertTrue(question_state::$mangrright->is_finished());
    }

    public function test_is_graded() {
        $this->assertFalse(question_state::$notstarted->is_graded());
        $this->assertFalse(question_state::$unprocessed->is_graded());
        $this->assertFalse(question_state::$todo->is_graded());
        $this->assertFalse(question_state::$invalid->is_graded());
        $this->assertFalse(question_state::$complete->is_graded());
        $this->assertFalse(question_state::$needsgrading->is_graded());
        $this->assertFalse(question_state::$finished->is_graded());
        $this->assertFalse(question_state::$gaveup->is_graded());
        $this->assertTrue(question_state::$gradedwrong->is_graded());
        $this->assertTrue(question_state::$gradedpartial->is_graded());
        $this->assertTrue(question_state::$gradedright->is_graded());
        $this->assertFalse(question_state::$manfinished->is_graded());
        $this->assertFalse(question_state::$mangaveup->is_graded());
        $this->assertTrue(question_state::$mangrwrong->is_graded());
        $this->assertTrue(question_state::$mangrpartial->is_graded());
        $this->assertTrue(question_state::$mangrright->is_graded());
    }

    public function test_is_commented() {
        $this->assertFalse(question_state::$notstarted->is_commented());
        $this->assertFalse(question_state::$unprocessed->is_commented());
        $this->assertFalse(question_state::$todo->is_commented());
        $this->assertFalse(question_state::$invalid->is_commented());
        $this->assertFalse(question_state::$complete->is_commented());
        $this->assertFalse(question_state::$needsgrading->is_commented());
        $this->assertFalse(question_state::$finished->is_commented());
        $this->assertFalse(question_state::$gaveup->is_commented());
        $this->assertFalse(question_state::$gradedwrong->is_commented());
        $this->assertFalse(question_state::$gradedpartial->is_commented());
        $this->assertFalse(question_state::$gradedright->is_commented());
        $this->assertTrue(question_state::$manfinished->is_commented());
        $this->assertTrue(question_state::$mangaveup->is_commented());
        $this->assertTrue(question_state::$mangrwrong->is_commented());
        $this->assertTrue(question_state::$mangrpartial->is_commented());
        $this->assertTrue(question_state::$mangrright->is_commented());
    }

    public function test_graded_state_for_fraction() {
        $this->assertEquals(question_state::$gradedwrong, question_state::graded_state_for_fraction(-1));
        $this->assertEquals(question_state::$gradedwrong, question_state::graded_state_for_fraction(0));
        $this->assertEquals(question_state::$gradedpartial, question_state::graded_state_for_fraction(0.000001));
        $this->assertEquals(question_state::$gradedpartial, question_state::graded_state_for_fraction(0.999999));
        $this->assertEquals(question_state::$gradedright, question_state::graded_state_for_fraction(1));
    }

    public function test_manually_graded_state_for_other_state() {
        $this->assertEquals(question_state::$manfinished,
                question_state::$finished->corresponding_commented_state(null));
        $this->assertEquals(question_state::$mangaveup,
                question_state::$gaveup->corresponding_commented_state(null));
        $this->assertEquals(question_state::$manfinished,
                question_state::$manfinished->corresponding_commented_state(null));
        $this->assertEquals(question_state::$mangaveup,
                question_state::$mangaveup->corresponding_commented_state(null));
        $this->assertEquals(question_state::$needsgrading,
                question_state::$mangrright->corresponding_commented_state(null));
        $this->assertEquals(question_state::$needsgrading,
                question_state::$mangrright->corresponding_commented_state(null));

        $this->assertEquals(question_state::$mangrwrong,
                question_state::$gaveup->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$needsgrading->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$gradedwrong->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$gradedpartial->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$gradedright->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$mangrright->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$mangrpartial->corresponding_commented_state(0));
        $this->assertEquals(question_state::$mangrwrong,
                question_state::$mangrright->corresponding_commented_state(0));

        $this->assertEquals(question_state::$mangrpartial,
                question_state::$gradedpartial->corresponding_commented_state(0.5));

        $this->assertEquals(question_state::$mangrright,
                question_state::$gradedpartial->corresponding_commented_state(1));
    }
}
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

namespace qbehaviour_deferredcbm;

use question_cbm;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../engine/lib.php');

/**
 * Unit tests for the deferred feedback with certainty base marking behaviour.
 *
 * @package    qbehaviour_deferredcbm
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class question_cbm_test extends \basic_testcase {

    public function test_adjust_fraction(): void {
        $this->assertEqualsWithDelta( 1,   question_cbm::adjust_fraction( 1,    question_cbm::LOW), 0.0000001);
        $this->assertEqualsWithDelta( 2,   question_cbm::adjust_fraction( 1,    question_cbm::MED), 0.0000001);
        $this->assertEqualsWithDelta( 3,   question_cbm::adjust_fraction( 1,    question_cbm::HIGH), 0.0000001);
        $this->assertEqualsWithDelta( 0,   question_cbm::adjust_fraction( 0,    question_cbm::LOW), 0.0000001);
        $this->assertEqualsWithDelta(-2,   question_cbm::adjust_fraction( 0,    question_cbm::MED), 0.0000001);
        $this->assertEqualsWithDelta(-6,   question_cbm::adjust_fraction( 0,    question_cbm::HIGH), 0.0000001);
        $this->assertEqualsWithDelta( 0.5, question_cbm::adjust_fraction( 0.5,  question_cbm::LOW), 0.0000001);
        $this->assertEqualsWithDelta( 1,   question_cbm::adjust_fraction( 0.5,  question_cbm::MED), 0.0000001);
        $this->assertEqualsWithDelta( 1.5, question_cbm::adjust_fraction( 0.5,  question_cbm::HIGH), 0.0000001);
        $this->assertEqualsWithDelta( 0,   question_cbm::adjust_fraction(-0.25, question_cbm::LOW), 0.0000001);
        $this->assertEqualsWithDelta(-2,   question_cbm::adjust_fraction(-0.25, question_cbm::MED), 0.0000001);
        $this->assertEqualsWithDelta(-6,   question_cbm::adjust_fraction(-0.25, question_cbm::HIGH), 0.0000001);
    }
}

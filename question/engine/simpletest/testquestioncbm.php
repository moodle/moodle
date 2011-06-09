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
 * This file contains tests for the question_cbm class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../lib.php');


/**
 * Unit tests for the question_cbm class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_cbm_test extends UnitTestCase {
    public function test_adjust_fraction() {
        $this->assertWithinMargin(0, question_cbm::adjust_fraction(0, question_cbm::LOW), 0.0000001);
        $this->assertWithinMargin(-2/3, question_cbm::adjust_fraction(0, question_cbm::MED), 0.0000001);
        $this->assertWithinMargin(-2, question_cbm::adjust_fraction(0, question_cbm::HIGH), 0.0000001);
        $this->assertWithinMargin(1/3, question_cbm::adjust_fraction(1, question_cbm::LOW), 0.0000001);
        $this->assertWithinMargin(2/3, question_cbm::adjust_fraction(1, question_cbm::MED), 0.0000001);
        $this->assertWithinMargin(1, question_cbm::adjust_fraction(1, question_cbm::HIGH), 0.0000001);
    }
}
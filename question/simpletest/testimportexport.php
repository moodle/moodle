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
 * Unit tests for the question import and export system.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');


/**
 * Subclass to make it easier to test qformat_default.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_qformat extends qformat_default {
    public function assemble_category_path($names) {
        return parent::assemble_category_path($names);
    }

    public function split_category_path($names) {
        return parent::split_category_path($names);
    }
}


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_default_test extends UnitTestCase {
    public function test_assemble_category_path() {
        $format = new testable_qformat();
        $pathsections = array(
            '$course$',
            "Tim's questions",
            "Tricky things like / // and so on",
            'Category name ending in /',
            '/ and one that starts with one',
            '<span lang="en" class="multilang">Matematically</span> <span lang="sv" class="multilang">Matematiskt (svenska)</span>'
        );
        $this->assertEqual('$course$/Tim\'s questions/Tricky things like // //// and so on/Category name ending in // / // and one that starts with one/<span lang="en" class="multilang">Matematically<//span> <span lang="sv" class="multilang">Matematiskt (svenska)<//span>',
                $format->assemble_category_path($pathsections));
    }

    public function test_split_category_path() {
        $format = new testable_qformat();
        $path = '$course$/Tim\'s questions/Tricky things like // //// and so on/Category name ending in // / // and one that starts with one/<span lang="en" class="multilang">Matematically<//span> <span lang="sv" class="multilang">Matematiskt (svenska)<//span>';
        $this->assertEqual(array(
                    '$course$',
                    "Tim's questions",
                    "Tricky things like / // and so on",
                    'Category name ending in /',
                    '/ and one that starts with one',
                    '<span lang="en" class="multilang">Matematically</span> <span lang="sv" class="multilang">Matematiskt (svenska)</span>'
                ), $format->split_category_path($path));
    }

    public function test_split_category_path_cleans() {
        $format = new testable_qformat();
        $path = '<evil>Nasty <virus //> thing<//evil>';
        $this->assertEqual(array('Nasty  thing'), $format->split_category_path($path));
    }
}

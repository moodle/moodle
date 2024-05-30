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
 * @package    core_question
 * @category   test
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question;

use qformat_default;

defined('MOODLE_INTERNAL') || die();

global $CFG;
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
class importexport_test extends \advanced_testcase {
    public function test_assemble_category_path(): void {
        $format = new testable_qformat();
        $pathsections = [
            '$course$',
            "Tim's questions",
            "Tricky things like / // and so on",
            'Category name ending in /',
            '/ and one that starts with one',
            '<span lang="en" class="multilang">Matematically</span> <span lang="sv" class="multilang">Matematiskt (svenska)</span>'
        ];
        $this->assertEquals('$course$/Tim\'s questions/Tricky things like // //// and so on/Category name ending in // / // and one that starts with one/<span lang="en" class="multilang">Matematically<//span> <span lang="sv" class="multilang">Matematiskt (svenska)<//span>',
                $format->assemble_category_path($pathsections));
    }

    public function test_split_category_path(): void {
        $format = new testable_qformat();
        $path = '$course$/Tim\'s questions/Tricky things like // //// and so on/Category name ending in // / // and one that starts with one/<span lang="en" class="multilang">Matematically<//span> <span lang="sv" class="multilang">Matematiskt (svenska)<//span>';
        $this->assertEquals([
                    '$course$',
                    "Tim's questions",
                    "Tricky things like / // and so on",
                    'Category name ending in /',
                    '/ and one that starts with one',
                    '<span lang="en" class="multilang">Matematically</span> <span lang="sv" class="multilang">Matematiskt (svenska)</span>'
                ], $format->split_category_path($path));
    }

    public function test_split_category_path_cleans(): void {
        $format = new testable_qformat();
        $path = '<evil>Nasty <virus //> thing<//evil>';
        $this->assertEquals(['Nasty  thing'], $format->split_category_path($path));
    }

    public function test_clean_question_name(): void {
        $format = new testable_qformat();

        $name = 'Nice simple name';
        $this->assertEquals($name, $format->clean_question_name($name));

        $name = 'Question in <span lang="en" class="multilang">English</span><span lang="fr" class="multilang">French</span>';
        $this->assertEquals($name, $format->clean_question_name($name));

        $name = 'Evil <script type="text/javascrip">alert("You have been hacked!");</script>';
        $this->assertEquals('Evil alert("You have been hacked!");', $format->clean_question_name($name));

        $name = 'This is a very long question name. It goes on and on and on. ' .
                'I wonder if it will ever stop. The quetsion name field in the database is only ' .
                'two hundred and fifty five characters wide, so if the import file contains a ' .
                'name longer than that, the code had better truncate it!';
        $this->assertEquals(shorten_text($name, 251), $format->clean_question_name($name));

        // The worst case scenario is a whole lot of single charaters in separate multilang tags.
        $name = '<span lang="en" class="multilang">A</span>' .
                '<span lang="fr" class="multilang">B</span>' .
                '<span lang="fr_ca" class="multilang">C</span>' .
                '<span lang="en_us" class="multilang">D</span>' .
                '<span lang="de" class="multilang">E</span>' .
                '<span lang="cz" class="multilang">F</span>' .
                '<span lang="it" class="multilang">G</span>' .
                '<span lang="es" class="multilang">H</span>' .
                '<span lang="pt" class="multilang">I</span>' .
                '<span lang="ch" class="multilang">J</span>';
        $this->assertEquals(shorten_text($name, 1), $format->clean_question_name($name));
    }

    public function test_create_default_question_name(): void {
        $format = new testable_qformat();

        $text = 'Nice simple name';
        $this->assertEquals($text, $format->create_default_question_name($text, 'Default'));

        $this->assertEquals('Default', $format->create_default_question_name('', 'Default'));

        $text = 'Question in <span lang="en" class="multilang">English</span><span lang="fr" class="multilang">French</span>';
        $this->assertEquals($text, $format->create_default_question_name($text, 'Default'));

        $text = 'Evil <script type="text/javascrip">alert("You have been hacked!");</script>';
        $this->assertEquals('Evil alert("You have been hacked!");',
                $format->create_default_question_name($text, 'Default'));

        $text = 'This is a very long question text. It goes on and on and on. ' .
                'I wonder if it will ever stop. The question name field in the database is only ' .
                'two hundred and fifty five characters wide, so if the import file contains a ' .
                'name longer than that, the code had better truncate it!';
        $this->assertEquals(shorten_text($text, 80), $format->create_default_question_name($text, 'Default'));

        // The worst case scenario is a whole lot of single charaters in separate multilang tags.
        $text = '<span lang="en" class="multilang">A</span>' .
                '<span lang="fr" class="multilang">B</span>' .
                '<span lang="fr_ca" class="multilang">C</span>' .
                '<span lang="en_us" class="multilang">D</span>' .
                '<span lang="de" class="multilang">E</span>' .
                '<span lang="cz" class="multilang">F</span>' .
                '<span lang="it" class="multilang">G</span>' .
                '<span lang="es" class="multilang">H</span>' .
                '<span lang="pt" class="multilang">I</span>' .
                '<span lang="ch" class="multilang">J</span>';
        $this->assertEquals(shorten_text($text, 1), $format->create_default_question_name($text, 'Default'));
    }
}

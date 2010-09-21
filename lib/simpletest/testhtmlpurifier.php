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
 * Tests our HTMLPurifier hacks
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class htmlpurifier_test extends UnitTestCase {

    /**
     * Tests the installation of event handlers from file
     */
    function test_our_tags() {
        $text = '<nolink>xxx<em>xx</em><div>xxx</div></nolink>';
        $this->assertIdentical($text, purify_html($text));

        $text = '<tex>xxxxxx</tex>';
        $this->assertIdentical($text, purify_html($text));

        $text = '<algebra>xxxxxx</algebra>';
        $this->assertIdentical($text, purify_html($text));

        $text = '<span lang="de_DU" class="multilang">asas</span>';
        $this->assertIdentical($text, purify_html($text));

        $text = '<lang lang="de_DU">xxxxxx</lang>';
        $this->assertIdentical($text, purify_html($text));

        $text = "\n\raa\rsss\nsss\r";
        $this->assertIdentical($text, purify_html($text));
    }

}



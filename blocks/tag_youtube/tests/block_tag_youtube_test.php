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

namespace block_tag_youtube;

/**
 * Block Tag Youtube test class.
 *
 * @package   block_tag_youtube
 * @category  test
 * @copyright 2015 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_tag_youtube_test extends \advanced_testcase {

    /**
     * Testing the tag youtube block's initial state after a new installation.
     *
     * @return void
     */
    public function test_after_install(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Assert that tag_youtube entry exists and that its visible attribute is set to 0 (disabled).
        $this->assertTrue($DB->record_exists('block', array('name' => 'tag_youtube', 'visible' => 0)));
    }
}

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

namespace profilefield_text;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/profile/field/text/field.class.php');

use profile_field_text;

/**
 * Unit tests for the profilefield_text.
 *
 * @package    profilefield_text
 * @copyright  2022 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \profilefield_text\profile_field_text
 */
class field_class_test extends \advanced_testcase {
    /**
     * Test that the profile text data is formatted and required filters applied
     *
     * @covers \profile_field_text::display_data
     * @dataProvider filter_profile_field_text_provider
     * @param string $input
     * @param string $expected
     */
    public function test_filter_display_data(string $input, string $expected): void {
        $this->resetAfterTest();
        $field = new profile_field_text();
        $field->data = $input;

        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);

        $actual = $field->display_data();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for {@see test_filter_display_data}
     *
     * @return string[]
     */
    public function filter_profile_field_text_provider(): array {
        return [
                'simple_string' => ['Simple string', 'Simple string'],
                'format_string' => ['HTML & is escaped', 'HTML &amp; is escaped'],
                'multilang_filter' =>
                    ['<span class="multilang" lang="en">English</span><span class="multilang" lang="fr">French</span>', 'English'],
                'emoticons_filter' => ['No emoticons filter :-(', 'No emoticons filter :-(']
        ];
    }
}

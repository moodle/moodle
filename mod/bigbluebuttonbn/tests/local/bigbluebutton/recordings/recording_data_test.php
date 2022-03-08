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

namespace mod_bigbluebuttonbn\local\bigbluebutton\recordings;

/**
 * Recording data tests.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @coversDefaultClass \mod_bigbluebuttonbn\local\bigbluebutton\recordings\recording_data
 */
class recording_data_test extends \advanced_testcase {

    /**
     * Test for the type_text provider.
     *
     * @covers ::type_text
     * @dataProvider type_text_provider
     * @param string $name
     * @param string $type
     */
    public function test_get_recording_type_text(string $name, string $type) {
        $this->assertEquals($name, recording_data::type_text($type));
    }

    /**
     * Type of recordings (dataprovider)
     *
     * @return \string[][]
     */
    public function type_text_provider(): array {
        return [
            ['Presentation', 'presentation'],
            ['Video', 'video'],
            ['Videos', 'videos'],
            ['Whatever', 'whatever'],
            ['Whatever It Can Be', 'whatever it can be'],
        ];
    }
}

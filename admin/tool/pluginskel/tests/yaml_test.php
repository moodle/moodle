<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace tool_pluginskel\local\util;

/**
 * The YAML utility testcasd.
 *
 * @package    tool_pluginskel
 * @copyright  2021 David Mudrák <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_yaml_testcase extends \basic_testcase {

    public function test_yaml_processing() {

        $input = "---
privacy:
  haspersonaldata: true
  meta:
    subsystems:
      - comment
      - files
      - portfolio:
        - firstname
        - lastname";

        $decoded = yaml::decode_string($input);

        $this->assertTrue($decoded['privacy']['haspersonaldata']);
        $this->assertEquals('comment', $decoded['privacy']['meta']['subsystems'][0]);
        $this->assertEquals('files', $decoded['privacy']['meta']['subsystems'][1]);
        $this->assertEquals('firstname', $decoded['privacy']['meta']['subsystems'][2]['portfolio'][0]);
        $this->assertEquals('lastname', $decoded['privacy']['meta']['subsystems'][2]['portfolio'][1]);

        $encoded = yaml::encode($decoded);

        $this->assertStringContainsString('- files', $encoded);
        $this->assertStringContainsString('portfolio:', $encoded);
        $this->assertStringContainsString('- firstname', $encoded);
    }
}


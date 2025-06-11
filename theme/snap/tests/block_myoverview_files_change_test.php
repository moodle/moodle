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
 * Test block_myoverview files changes that were overwritten or duplicated in Snap.
 *
 * @package   theme_snap
 * @author    Daniel Cifuentes <daniel.cifuentes@openlms.net>
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_myoverview_files_change_test extends advanced_testcase  {

    /**
     * @dataProvider getblockmyoverviewfiles
     */
    public function test_block_myoverview_files_change_correct($path, $expectedchecksum) {
        $this->markTestSkipped('To be reviewed in INT-20323');
        $this->resetAfterTest();

        $message = "The {$path} file has been modified, please check the changes for the block_myoverview in Snap.";
        $currentchecksum = sha1(file_get_contents($path));
        $messagemod = "The file with path $path has been modified.";
        $messageexist = "The file with path $path does not exist.";
        // Assertion file exists and current SHA1 file is equal to expected SHA1.
        $this->assertFileExists($path, $messageexist);
        $this->assertEquals($expectedchecksum, $currentchecksum, $messagemod);
    }

    // If test_block_myoverview_files_change_correct() fails after merging code, verify all the listed files and compare
    // them with the duplicated ones in Snap. Also check the general workflow of the Course Overview block in the My
    // Courses page to see if it works as expected. If the changes are not needed in our code, just replace the SHA and
    // move on. If not, please update the code with the required changes.
    public function getblockmyoverviewfiles() {
        return [
            // Follow the pattern [path, expected checksum].
            ['blocks/myoverview/amd/src/main.js', 'd7b5e4308c1a8721e3e27fd3dcf13da66776e9e6'],
            ['blocks/myoverview/amd/src/repository.js', '0daa7a880c2e330e8436fb10f9c479ef89a40c37'],
            ['blocks/myoverview/amd/src/view.js', '94059351094f9e0b08d4e2c62cefe932a9d09893'],
            ['blocks/myoverview/amd/src/view_nav.js', 'e090c421394862da1d245056915e7cb80bd0ce10'],
            ['blocks/myoverview/templates/main.mustache', 'e06e44eca0b3afa04fe16cb848b6bb92da577f69'],
            ['blocks/myoverview/classes/output/renderer.php', '1dce1c37f0ea65958735510ce902a906b1e321d9']
        ];
    }
}


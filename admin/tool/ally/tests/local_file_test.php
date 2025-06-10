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
 * Tests for local_file library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local_file;
use tool_ally\auto_config;

/**
 * Tests for local_file library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_file_test extends \advanced_testcase {


    public function test_generate_wspluginfile_signature_invalid_config() {
        // Test failure without ally_webuser / valid configuration.
        $expectedmsg = 'Access control exception (Ally web user (ally_webuser) does not exist.';
        $expectedmsg .= ' Has auto configure been run?)';
        $this->expectExceptionMessage($expectedmsg);
        local_file::generate_wspluginfile_signature('fakehash');
    }

    public function test_generate_wspluginfile_signature() {
        $this->resetAfterTest();
        // Test method successful when configured.
        $ac = new auto_config();
        $ac->configure();
        $fakehash = 'fakehash'; // Not a hash - just for testing.
        $iat = time();
        $signature = local_file::generate_wspluginfile_signature($fakehash, $iat);
        $this->assertEquals($fakehash, $signature->pathnamehash);
        // Check iat is fresh. 5 second buffer for checking iat.
        $this->assertEquals($iat, $signature->iat);
        $this->assertNotEmpty($signature->signature);
    }

    public function test_get_fileurlproperties() {
        global $CFG;
        $this->resetAfterTest();

        $samplefilearea = 'assets';
        $samplecomponent = 'tool_themeassets';
        $samplefilename = 'icon.png';
        $samplefilepath = '/Folder 1/';
        $sampleurl = "{$CFG->wwwroot}/pluginfile.php/1/{$samplecomponent}/{$samplefilearea}/0{$samplefilepath}{$samplefilename}";
        $props = local_file::get_fileurlproperties($sampleurl);
        $this->assertInstanceOf('tool_ally\models\pluginfileurlprops', $props);
        $this->assertEquals($samplefilearea, $props->filearea);
        $this->assertEquals($samplecomponent, $props->component);
        $this->assertEquals($samplefilename, basename($props->filename));
        $this->assertEquals($samplefilepath, $props->filepath);
    }
}

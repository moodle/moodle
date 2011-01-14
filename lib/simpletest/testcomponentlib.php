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
 * Unit tests for /lib/componentlib.class.php.
 *
 * @package   moodlecore
 * @copyright 2011 Tomasz Muras
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir.'/componentlib.class.php');

class componentlib_test extends UnitTestCase {

    public function test_component_installer() {
        global $CFG;

        $ci = new component_installer('http://download.moodle.org', 'unittest', 'downloadtests.zip');
        $this->assertTrue($ci->check_requisites());

        $destpath = $CFG->dataroot.'/downloadtests';

        //carefully remove component files to enforce fresh installation
        @unlink($destpath.'/'.'downloadtests.md5');
        @unlink($destpath.'/'.'test.html');
        @unlink($destpath.'/'.'test.jpg');
        @rmdir($destpath);

        $this->assertEqual(COMPONENT_NEEDUPDATE, $ci->need_upgrade());

        $status = $ci->install();
        $this->assertEqual(COMPONENT_INSTALLED, $status);
        $this->assertEqual('9e94f74b3efb1ff6cf075dc6b2abf15c', $ci->get_component_md5());

        //it's already installed, so Moodle should detect it's up to date
        $this->assertEqual(COMPONENT_UPTODATE, $ci->need_upgrade());
        $status = $ci->install();
        $this->assertEqual(COMPONENT_UPTODATE, $status);

        //check if correct files were downloaded
        $this->assertEqual('2af180e813dc3f446a9bb7b6af87ce24', md5_file($destpath.'/'.'test.jpg'));
        $this->assertEqual('47250a973d1b88d9445f94db4ef2c97a', md5_file($destpath.'/'.'test.html'));

    }
}

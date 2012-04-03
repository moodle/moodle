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
 * Unit tests for  ../repositorylib.php.
 *
 * @package    core_repository
 * @category   phpunit
 * @author     nicolasconnault@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/repository/lib.php");


class repositorylib_testcase extends advanced_testcase {

    public function test_initialise_filepicker() {
        global $PAGE, $SITE;

        $this->resetAfterTest(true);

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $this->setUser(2);

        $args = new stdClass();
        $args->accepted_types = '*';
        $args->return_types = array(FILE_EXTERNAL);
        $info = initialise_filepicker($args);

        $this->assertInstanceOf('stdClass', $info);
        $this->assertObjectHasAttribute('defaultlicense', $info);
        $this->assertObjectHasAttribute('licenses', $info);
        $this->assertObjectHasAttribute('author', $info);
        $this->assertObjectHasAttribute('externallink', $info);
        $this->assertObjectHasAttribute('accepted_types', $info);
        $this->assertObjectHasAttribute('return_types', $info);
    }
}

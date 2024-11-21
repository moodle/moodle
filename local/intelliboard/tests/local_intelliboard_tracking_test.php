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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class within phpunit tests
 * @group local_intelliboard
 */
class local_intelliboard_tracking_testcase extends advanced_testcase
{

    public function test_tracking()
    {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        require_once($CFG->dirroot .'/local/intelliboard/lib.php');

        //$result = local_intelliboard_insert_tracking(false);
        //$this->assertEquals(true, $result);

        $this->setGuestUser();
        //$result = local_intelliboard_insert_tracking(false);
        //$this->assertEquals(false, $result);
    }
}

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
 * Block XP course world config test.
 *
 * @package    block_xp
 * @copyright  2020 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/events.php');

use block_xp\di;
use block_xp\tests\base_testcase;

/**
 * Course world config testcase.
 *
 * @package    block_xp
 * @copyright  2020 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_xp\local\config\default_course_world_config
 * @covers     \block_xp\local\config\default_admin_config
 */
final class course_world_config_test extends base_testcase {

    public function test_default_config(): void {
        global $DB;

        $defaultcourse = new local\config\default_course_world_config();
        $defaultadmin = new local\config\default_admin_config();
        $inheritable = array_keys(array_intersect_key($defaultcourse->get_all(), $defaultadmin->get_all()));

        $config = di::get('config');
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        // Validate that all keys match the admin value.
        $cfg1 = $this->get_world($c1->id)->get_config();
        foreach ($inheritable as $key) {
            $this->assertEquals($cfg1->get($key), $config->get($key));;
        }

        // Validate that changing an admin value is populated in the course.
        $this->assertNotEquals(9, $config->get('neighbours'));
        $config->set('neighbours', 9);
        $this->assertContains('neighbours', $inheritable);
        $cfg2 = $this->get_world($c2->id)->get_config();
        foreach ($inheritable as $key) {
            $this->assertEquals($cfg2->get($key), $config->get($key));;
        }

        // After saving the configuration, any more changes to the admin won't have an impact.
        $this->assertEquals($config->get('neighbours'), $cfg2->get('neighbours'));
        $cfg2->set('neighbours', 7);
        $config->set('neighbours', 6);
        $this->assertNotEquals($config->get('neighbours'), $cfg2->get('neighbours'));
    }

}

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
 * Moodle environment test.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Do standard environment.xml tests.
 */
class environment_testcase extends advanced_testcase {

    public function test_environment() {
        global $CFG;

        require_once($CFG->libdir.'/environmentlib.php');
        list($envstatus, $environment_results) = check_moodle_environment(normalize_version($CFG->release), ENV_SELECT_RELEASE);

        $this->assertNotEmpty($envstatus);
        foreach ($environment_results as $environment_result) {
            $this->assertTrue($environment_result->getStatus(), "Problem detected in environment ($environment_result->part:$environment_result->info), fix all warnings and errors!");
        }
    }
}

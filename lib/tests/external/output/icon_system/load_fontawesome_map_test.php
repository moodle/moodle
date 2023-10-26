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
 * External functions test for record_feedback_action.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\external\output\icon_system;

use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Class record_userfeedback_action_testcase
 *
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\external\output\icon_system\load_fontawesome_map
 */
class load_fontawesome_map_test extends externallib_advanced_testcase {

    /**
     * Perform setup before these tests are run.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        // In normal operation the external_api classes will have been loaded by the caller.
        // The load_fontawesome_map class should not need to supplement our lack of autoloading of these classes.
        require_once($CFG->libdir . '/externallib.php');
    }

    /**
     * Ensure that a valid theme which uses fontawesome returns a map.
     *
     * @covers ::execute_parameters
     * @covers ::execute
     * @covers ::execute_returns
     * @dataProvider valid_fontawesome_theme_provider
     * @param   string $themename
     */
    public function test_execute(string $themename): void {
        $result = load_fontawesome_map::execute($themename);
        $this->assertIsArray($result);

        foreach ($result as $value) {
            $this->assertArrayHasKey('component', $value);
            $this->assertArrayHasKey('pix', $value);
            $this->assertArrayHasKey('to', $value);
        }
    }

    /**
     * Ensure that an invalid theme cannot be loaded.
     */
    public function test_execute_invalid_themename(): void {
        $result = load_fontawesome_map::execute('invalidtheme');
        $this->assertDebuggingCalled(
            'This page should be using theme invalidtheme which cannot be initialised. Falling back to the site theme boost'
        );
        $this->assertIsArray($result);
    }

    /**
     * Data provider for valid themes to use with the execute function.
     *
     * @return  array
     */
    public function valid_fontawesome_theme_provider(): array {
        return [
            'Boost theme' => ['boost'],
            'Classic theme (extends boost)' => ['classic'],
        ];
    }
}

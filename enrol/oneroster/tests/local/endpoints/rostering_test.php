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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\endpoints;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

use advanced_testcase;
use enrol_oneroster\local\interfaces\coursecat_representation;
use stdClass;
use InvalidArgumentException;
use ReflectionClass;

/**
 * One Roster tests for filters.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\endpoints\rostering
 */
class rostering_testcase extends oneroster_testcase {

    /**
     * Ensure that instantiation works correctly.
     */
    public function test_instantiation(): void {
        $container = $this->get_mocked_container();

        $endpoint = new rostering($container);

        $this->assertInstanceOf(rostering::class, $endpoint);
    }
}

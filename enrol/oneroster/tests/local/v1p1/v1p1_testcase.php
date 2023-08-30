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

namespace enrol_oneroster\local\v1p1;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

use enrol_oneroster\local\v1p1\container;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\collection_factory as collection_factory_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;
use enrol_oneroster\local\v1p1\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\v1p1\factories\collection_factory;
use enrol_oneroster\local\v1p1\factories\entity_factory;

/**
 * One Roster Entity tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class v1p1_testcase extends oneroster_testcase {

    /**
     * Get a mocked container.
     */
    protected function get_mocked_container(): container_interface {
        $client = $this->mock_client();
        $mock = $this->getMockBuilder(container::class)
            ->setConstructorArgs([$client])
            ->setMethods([
                '__construct',
                'get_client',
                'get_rostering_endpoint',
                'get_entity_factory',
                'get_collection_factory',
                'get_cache_factory',
            ])
            ->getMock();

        $mock->method('get_client')->willReturn($client);

        return $mock;
    }
}

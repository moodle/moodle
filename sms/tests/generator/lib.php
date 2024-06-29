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

use core_sms\gateway;

/**
 * SMS data generator for tests.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_sms_generator extends component_generator_base {

    /**
     * Create sms gateway record.
     *
     * @param array $record The gateway record to be created.
     * @return gateway
     */
    public function create_sms_gateways(array $record): gateway {
        $config = json_decode($record['config'], false, 512, JSON_THROW_ON_ERROR);
        $manager = \core\di::get(\core_sms\manager::class);
        $gateway = $manager->create_gateway_instance(
            classname: $record['classname'],
            name: $record['name'],
            enabled: $record['enabled'],
            config: $config,
        );
        return $gateway;
    }
}

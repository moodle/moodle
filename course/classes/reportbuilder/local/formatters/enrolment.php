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

declare(strict_types=1);

namespace core_course\reportbuilder\local\formatters;

use core_user\output\status_field;
use lang_string;
use stdClass;

/**
 * Formatters for the course enrolment entity
 *
 * @package     core_course
 * @copyright   2022 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolment {

    /**
     * Return enrolment plugin instance name
     *
     * @param string|null $value
     * @param stdClass $row
     * @return string
     */
    public static function enrolment_name(?string $value, stdClass $row): string {
        global $DB;

        if (empty($value)) {
            return '';
        }

        $instance = $DB->get_record('enrol', ['id' => $row->id, 'enrol' => $row->enrol], '*', MUST_EXIST);
        $plugin = enrol_get_plugin($row->enrol);

        return $plugin ? $plugin->get_instance_name($instance) : '-';
    }

    /**
     * Returns list of enrolment statuses
     *
     * @return lang_string[]
     */
    public static function enrolment_values(): array {
        return [
            status_field::STATUS_ACTIVE => new lang_string('participationactive', 'enrol'),
            status_field::STATUS_SUSPENDED => new lang_string('participationsuspended', 'enrol'),
            status_field::STATUS_NOT_CURRENT => new lang_string('participationnotcurrent', 'enrol'),
        ];
    }

    /**
     * Return enrolment status for user
     *
     * @param string|null $value
     * @return string|null
     */
    public static function enrolment_status(?string $value): ?string {
        if ($value === null) {
            return null;
        }

        $statusvalues = self::enrolment_values();

        $value = (int) $value;
        if (!array_key_exists($value, $statusvalues)) {
            return null;
        }

        return (string) $statusvalues[$value];
    }
}

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

namespace core_cohort\customfield;

use core_customfield\handler;
use core_customfield\field_controller;

/**
 * Cohort handler for custom fields.
 *
 * @package   core_cohort
 * @copyright 2023 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_handler extends handler {

    /**
     * @var cohort_handler
     */
    static protected $singleton;

    /**
     * Returns a singleton.
     *
     * @param int $itemid
     * @return \core_customfield\handler
     */
    public static function create(int $itemid = 0): handler {
        if (static::$singleton === null) {
            self::$singleton = new static(0);
        }
        return self::$singleton;
    }

    /**
     * Run reset code after unit tests to reset the singleton usage.
     */
    public static function reset_caches(): void {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('This feature is only intended for use in unit tests');
        }

        static::$singleton = null;
    }

    /**
     * The current user can configure custom fields on this component.
     *
     * @return bool true if the current can configure custom fields, false otherwise
     */
    public function can_configure(): bool {
        return has_capability('moodle/cohort:configurecustomfields', $this->get_configuration_context());
    }

    /**
     * The current user can edit custom fields on the given cohort.
     *
     * @param field_controller $field
     * @param int $instanceid id of the cohort to test edit permission
     * @return bool true if the current can edit custom field, false otherwise
     */
    public function can_edit(field_controller $field, int $instanceid = 0): bool {
        return has_capability('moodle/cohort:manage', $this->get_instance_context($instanceid));
    }

    /**
     * The current user can view custom fields on the given cohort.
     *
     * @param field_controller $field
     * @param int $instanceid id of the cohort to test edit permission
     * @return bool true if the current can view custom field, false otherwise
     */
    public function can_view(field_controller $field, int $instanceid): bool {
        return has_any_capability(['moodle/cohort:manage', 'moodle/cohort:view'], $this->get_instance_context($instanceid));
    }

    /**
     * Context that should be used for new categories created by this handler.
     *
     * @return \context the context for configuration
     */
    public function get_configuration_context(): \context {
        return \context_system::instance();
    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return \moodle_url The URL to configure custom fields for this component
     */
    public function get_configuration_url(): \moodle_url {
        return new \moodle_url('/cohort/customfield.php');
    }

    /**
     * Returns the context for the data associated with the given instanceid.
     *
     * @param int $instanceid id of the record to get the context for
     * @return \context the context for the given record
     */
    public function get_instance_context(int $instanceid = 0): \context {
        global $DB;
        if ($instanceid > 0) {
            $cohort = $DB->get_record('cohort', ['id' => $instanceid], '*', MUST_EXIST);
            return \context::instance_by_id($cohort->contextid, MUST_EXIST);
        } else {
            return \context_system::instance();
        }
    }
}

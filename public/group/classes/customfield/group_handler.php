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

namespace core_group\customfield;

use context;
use context_course;
use context_system;
use core_customfield\api;
use core_customfield\handler;
use core_customfield\field_controller;
use moodle_url;
use restore_task;

/**
 * Group handler for custom fields.
 *
 * @package   core_group
 * @author    Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright 2023 Catalyst IT Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_handler extends handler {

    /**
     * @var group_handler
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
        return has_capability('moodle/group:configurecustomfields', $this->get_configuration_context());
    }

    /**
     * The current user can edit custom fields on the given group.
     *
     * @param field_controller $field
     * @param int $instanceid id of the group to test edit permission
     * @return bool true if the current can edit custom field, false otherwise
     */
    public function can_edit(field_controller $field, int $instanceid = 0): bool {
        return has_capability('moodle/course:managegroups', $this->get_instance_context($instanceid));
    }

    /**
     * The current user can view custom fields on the given group.
     *
     * @param field_controller $field
     * @param int $instanceid id of the group to test edit permission
     * @return bool true if the current can view custom field, false otherwise
     */
    public function can_view(field_controller $field, int $instanceid): bool {
        return has_any_capability(['moodle/course:managegroups', 'moodle/course:view'], $this->get_instance_context($instanceid));
    }

    /**
     * Context that should be used for new categories created by this handler.
     *
     * @return context the context for configuration
     */
    public function get_configuration_context(): context {
        return context_system::instance();
    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return moodle_url The URL to configure custom fields for this component
     */
    public function get_configuration_url(): moodle_url {
        return new moodle_url('/group/customfield.php');
    }

    /**
     * Returns the context for the data associated with the given instanceid.
     *
     * @param int $instanceid id of the record to get the context for
     * @return context the context for the given record
     */
    public function get_instance_context(int $instanceid = 0): \context {
        global $COURSE, $DB;

        if ($instanceid > 0) {
            $group = $DB->get_record('groups', ['id' => $instanceid], '*', MUST_EXIST);
            return context_course::instance($group->courseid);
        } else if (!empty($COURSE->id)) {
            return context_course::instance($COURSE->id);
        } else {
            return context_system::instance();
        }
    }

    /**
     * Get raw data associated with all fields current user can view or edit
     *
     * @param int $instanceid
     * @return array
     */
    public function get_instance_data_for_backup(int $instanceid): array {
        $finalfields = [];
        $instancedata = $this->get_instance_data($instanceid, true);
        foreach ($instancedata as $data) {
            if ($data->get('id') && $this->can_backup($data->get_field(), $instanceid)) {
                $finalfields[] = [
                    'id' => $data->get('id'),
                    'shortname' => $data->get_field()->get('shortname'),
                    'type' => $data->get_field()->get('type'),
                    'value' => $data->get_value(),
                    'valueformat' => $data->get('valueformat'),
                    'valuetrust' => $data->get('valuetrust'),
                    'groupid' => $data->get('instanceid'),
                ];
            }
        }
        return $finalfields;
    }

    /**
     * Creates or updates custom field data.
     *
     * @param restore_task $task
     * @param array $data
     *
     * @return int|void Conditionally returns the ID of the created or updated record.
     */
    public function restore_instance_data_from_backup(restore_task $task, array $data) {
        $instanceid = $data['groupid'];
        $context = $this->get_instance_context($instanceid);
        $editablefields = $this->get_editable_fields($instanceid);
        $records = $this->get_instance_fields_data($editablefields, $instanceid);

        foreach ($records as $d) {
            $field = $d->get_field();
            if ($field->get('shortname') === $data['shortname'] && $field->get('type') === $data['type']) {
                if (!$d->get('id')) {
                    $d->set($d->datafield(), $data['value']);
                    $d->set('value', $data['value']);
                    $d->set('valueformat', $data['valueformat']);
                    $d->set('valuetrust', !empty($data['valuetrust']));
                    $d->set('contextid', $context->id);
                    $d->save();
                }
                return $d->get('id');
            }
        }
    }
}

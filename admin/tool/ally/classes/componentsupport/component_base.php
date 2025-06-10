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
 * Base class for processing module html.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

use context;
use stored_file;
use tool_ally\componentsupport\interfaces\html_content;
use tool_ally\local;
use tool_ally\role_assignments;
use tool_ally\exceptions\component_validation_exception;

/**
 * Base class for processing module html.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class component_base {

    const TYPE_CORE = 'core';

    const TYPE_MOD = 'mod';

    const TYPE_BLOCK = 'block';

    protected $tablefields = [];

    /**
     * Return component type for this component - a class constant beginning with TYPE_
     *
     * @return int
     */
    abstract public static function component_type();

    /**
     * @return bool
     */
    public function module_installed() {
        return \core_component::get_component_directory($this->get_component_name()) !== null;
    }

    /**
     * Get fields for a specific table.
     *
     * @param string $table
     * @return array|mixed
     */
    public function get_table_fields($table) {
        if (isset($this->tablefields[$table])) {
            return $this->tablefields[$table];
        }
        return [];
    }

    /**
     * @param string $table
     * @param string $field
     * @throws \coding_exception
     */
    protected function validate_component_table_field($table, $field) {
        if (empty($this->tablefields[$table]) || !is_array($this->tablefields)) {
            throw new component_validation_exception(
                'Table '.$table.' is not allowed for the requested component content'
            );
        }
        if (!in_array($field, $this->tablefields[$table])) {
            throw new component_validation_exception(
                'Field '.$field.' is not allowed for the table '.$table
            );
        }
    }

    /**
     * Extract component name from class.
     * @return mixed
     */
    protected function get_component_name() {
        $reflect = new \ReflectionClass($this);
        $class = $reflect->getShortName();
        $matches = [];
        if (!preg_match('/(.*)_component/', $class, $matches) || count($matches) < 2) {
            throw new \coding_exception('Invalid component class '.$class);
        }

        return $matches[1];
    }

    /**
     * Get ids of approved content authors - teachers, managers, admin, etc.
     * @param context $context
     * @return array
     */
    public function get_approved_author_ids_for_context(context $context) {
        $admins = local::get_adminids();
        $ra = new role_assignments(local::get_roleids());
        $userids = $ra->user_ids_for_context($context);
        $userids = array_filter($userids, function($item) {
            return !!$item;
        });
        $userids = array_keys($userids);
        $result = array_unique(array_merge($admins, $userids));
        return $result;
    }

    /**
     * Is the user an approved content author? teachers, managers, admin, etc.
     * @param int $userid
     * @param context $context
     * @return bool
     */
    public function user_is_approved_author_type($userid, context $context) {
        return in_array($userid, $this->get_approved_author_ids_for_context($context));
    }

    /**
     * Get a file area for a specific table / field.
     *
     * Override this in your component if you need something more complicated.
     *
     * @param $table
     * @param $field
     * @return mixed
     */
    public function get_file_area($table, $field) {
        // The default is simply to return the field, if it's part of the tablefields array.
        if (isset($this->tablefields[$table]) && in_array($field, $this->tablefields[$table])) {
            return $field;
        }
    }

    /**
     * Get a file item id for a specific table / field / id.
     *
     * Override this in your component if you need something more complicated.
     *
     * @param string $table
     * @param string $field
     * @param int $id
     * @return int
     */
    public function get_file_item($table, $field, $id) {
        return 0;
    }

    /**
     * Get a file item path for a specific table / field / id.
     *
     * Override this in your component if you need something more complicated.
     *
     * @param string $table
     * @param string $field
     * @param int $id
     * @return int
     */
    public function get_file_path($table, $field, $id) {
        return '/';
    }

    /**
     * Take a list of content items and return an array mapping the entity IDs with course module id.
     *
     * The resulting array is keyed on the content item ID, with the value being the course module id.
     *
     * @param string $table
     * @param array $contentitems
     * @return array
     */
    protected function map_content_items_to_cmids($table, $contentitems) {
        global $DB;
        $colitems = array_column($contentitems, 'id');
        if (empty($colitems)) {
            return [];
        }
        list($insql, $params) = $DB->get_in_or_equal(array_column($contentitems, 'id'), SQL_PARAMS_NAMED);
        $params['modulename'] = $table;
        $sql = "SELECT instance.id, cm.id AS cmid
                FROM {{$table}} instance
                JOIN {course_modules} cm ON cm.instance = instance.id
                JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                WHERE instance.id $insql";
        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Attempt to resolve a module instance id from a specific table + id.
     * You may need to override this method in a component for tables that do not easily link back to the module's
     * main table (e.g. table 2 levels down from main module table).
     *
     * @param $table
     * @param $id
     * @return mixed
     * @throws \dml_exception
     */
    public function resolve_module_instance_id($table, $id) {
        global $DB;

        $component = $this->get_component_name();

        if ($this->component_type() !== self::TYPE_MOD) {
            $msg = <<<MSG
Attempt to get a module instance for a component that is not a module ($component)
MSG;

            throw new \coding_exception($msg);
        }

        if ($table === $component) {
            return $id;
        } else {
            $record = $DB->get_record($table, ['id' => $id]);
            if (!empty($record->{$component.'id'})) {
                $instanceid = $record->{$component.'id'};
            } else if (!empty($record->$component)) {
                $instanceid = $record->$component;
            } else {
                $method = __METHOD__;
                $msg = <<<MSG
Unable to resolve component from subtable "$table" with id $id. A developer needs to override the method "$method" in the
component $component so that it can cope with the table "$table".
MSG;
                throw new \coding_exception($msg);
            }
            $componentrecord = $DB->get_record($component, ['id' => $instanceid]);
            return $componentrecord->id;
        }
    }

    /**
     * Check if the provided file is in use in the provided context.
     * Intended to filter out files that are attached to text areas, but are aren't actually in use.
     * Can make an assumption that only teacher generated content needs to be searched.
     *
     * @param stored_file $file The file to check
     * @param context $context The context to check in
     * @return bool
     */
    public function check_file_in_use(stored_file $file, ?context $context = null): bool {
        if (is_a($this, html_content::class) && method_exists($this, 'check_embedded_file_in_use')) {
            return $this->check_embedded_file_in_use($file, $context);
        }

        // If not implemented, we should just default to true.
        debugging('No check_file_in_use implementation for ' . $this->get_component_name(), DEBUG_DEVELOPER);

        return true;
    }

    /**
     * Return an array of text content created by teachers, managers, admin, etc, for use in
     * checking if files are in use.
     *
     * @param int $id
     * @return array|null
     */
    public function get_all_files_search_html(int $id): ?array {
        // Null return means not implemented.
        return null;
    }
}

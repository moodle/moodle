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

namespace core_ai\aiactions;

use core_ai\aiactions\responses\response_base;

/**
 * Base Action class.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {
    /** @var int Timestamp the action object was created. */
    protected readonly int $timecreated;

    /**
     * Constructor for the class.
     *
     * @param int $contextid The context ID where the action was created.
     */
    public function __construct(
        /** @var int The context ID the action was created in */
        protected int $contextid,
    ) {
        $this->timecreated = \core\di::get(\core\clock::class)->time();
    }

    /**
     * Responsible for storing any action specific data in the database.
     *
     * @param response_base $response The response object to store.
     * @return int The id of the stored action.
     */
    abstract public function store(response_base $response): int;

    /**
     * Get the basename of the class.
     *
     * This is used to generate the action name and description.
     *
     * @return string The basename of the class.
     */
    public static function get_basename(): string {
        return basename(str_replace('\\', '/', static::class));
    }

    /**
     * Get the action name.
     *
     * Defaults to the action name string.
     *
     * @return string
     */
    public static function get_name(): string {
        $stringid = 'action_' . self::get_basename();
        return get_string($stringid, 'core_ai');
    }

    /**
     * Get the action description.
     *
     * Defaults to the action description string.
     *
     * @return string
     */
    public static function get_description(): string {
        $stringid = 'action_' . self::get_basename() . '_desc';
        return get_string($stringid, 'core_ai');
    }

    /**
     * Get the system instruction for the action.
     *
     * @return string The system instruction for the action.
     */
    public static function get_system_instruction(): string {
        $stringid = 'action_' . self::get_basename() . '_instruction';

        // If the string doesn't exist, return an empty string.
        if (!get_string_manager()->string_exists($stringid, 'core_ai')) {
            return '';
        }

        return get_string($stringid, 'core_ai');
    }

    /**
     * Get a configuration option.
     *
     * @param string $name The name of the configuration option to get.
     * @return mixed The value of the configuration option.
     */
    public function get_configuration(string $name): mixed {
        return $this->$name;
    }

    /**
     * Return the correct table name for the action.
     *
     * @return string The correct table name for the action.
     */
    protected function get_tablename(): string {
        // Table name should always be in this format.
        return 'ai_action_' . $this->get_basename();
    }

    /**
     * Get the class name of the response object.
     *
     * @return string The class name of the response object.
     */
    public static function get_response_classname(): string {
        return responses::class . '\\response_' . self::get_basename();
    }
}

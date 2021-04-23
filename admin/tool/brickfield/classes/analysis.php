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

namespace tool_brickfield;

use tool_brickfield\task\process_analysis_requests;

/**
 * Analysis and deployment class.
 *
 * @package tool_brickfield
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analysis {

    /**
     * Indicates that analysis of content has been enabled.
     */
    const ANALYSISDISABLED = '0';

    /**
     * Indicates that analysis is enabled and using request method.
     */
    const ANALYSISBYREQUEST = '1';

    /**
     * Return the type of analysis being used (currently only request).
     *
     * @return false|mixed|object|string|null
     * @throws \dml_exception
     */
    public static function get_type() {
        // Moodle caches these calls, so it's not expensive.
        return get_config(manager::PLUGINNAME, 'analysistype');
    }

    /**
     * Return true is analysis has been enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_enabled(): bool {
        $analysistype = static::get_type();
        return ($analysistype !== false) && ($analysistype !== self::ANALYSISDISABLED);
    }

    /**
     * Return true if analysis is by request method.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function type_is_byrequest(): bool {
        return static::get_type() === self::ANALYSISBYREQUEST;
    }

    /**
     * Return true if the course has been analyzed.
     *
     * @param int $courseid
     * @return bool
     * @throws \dml_exception
     */
    public static function is_course_analyzed(int $courseid): bool {
        return scheduler::is_course_analyzed($courseid);
    }

    /**
     * Return a redirect message with the earliest time expected for analysis to complete.
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public static function redirect_message() {
        $epoch = new process_analysis_requests;
        $time = userdate($epoch->get_next_scheduled_time(), get_string('strftimetime', 'core_langconfig'));
        $message = get_string('confirmationmessage', manager::PLUGINNAME, $time);

        return $message;
    }
}

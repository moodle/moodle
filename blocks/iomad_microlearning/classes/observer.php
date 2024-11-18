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
 * Event observer for local iomad plugin.
 *
 * @package    block_iomad_microlearning
 * @copyright  2019 E-Learn Design Ltd. (http://www.e-learndesign.co.uk)
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/iomad_microlearning/lib.php');

class block_iomad_microlearning_observer {

    /**
     * Triggered via block_iomad_microlearning::thread_created event.
     *
     * @param \block_iomad_microlearning\event\thread_created $event
     * @return bool true on success.
     */
    public static function thread_created($event) {
        microlearning::event_thread_created($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::thread_deleted event.
     *
     * @param \block_iomad_microlearning\event\thread_deleted $event
     * @return bool true on success.
     */
    public static function thread_deleted($event) {
        microlearning::event_thread_deleted($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::thread_updated event.
     *
     * @param \block_iomad_microlearning\event\thread_updated $event
     * @return bool true on success.
     */
    public static function thread_updated($event) {
        microlearning::event_thread_updated($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::thread_schedule_updated event.
     *
     * @param \block_iomad_microlearning\event\thread_schedule_updated $event
     * @return bool true on success.
     */
    public static function thread_schedule_updated($event) {
        microlearning::event_thread_schedule_updated($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::nugget_created event.
     *
     * @param \block_iomad_microlearning\event\nugget_created $event
     * @return bool true on success.
     */
    public static function nugget_created($event) {
        microlearning::event_nugget_created($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::nugget_deleted event.
     *
     * @param \block_iomad_microlearning\event\nugget_deleted $event
     * @return bool true on success.
     */
    public static function nugget_deleted($event) {
        microlearning::event_nugget_deleted($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::nugget_updated event.
     *
     * @param \block_iomad_microlearning\event\nugget_updated $event
     * @return bool true on success.
     */
    public static function nugget_updated($event) {
        microlearning::event_nugget_updated($event);
        return true;
    }

    /**
     * Triggered via block_iomad_microlearning::nugget_moved event.
     *
     * @param \block_iomad_microlearning\event\nugget_moved $event
     * @return bool true on success.
     */
    public static function nugget_moved($event) {
        microlearning::event_nugget_moved($event);
        return true;
    }

    /**
     * Triggered via course_module_completion_updated event.
     *
     * @param \core\event\course_module_completion_updated $event
     * @return bool true on success.
     */
    public static function course_module_completion_updated($event) {
        microlearning::event_course_module_completion_updated($event);
        return true;
    }

    /**
     * Triggered via user_deleted event.
     *
     * @param \core\event\user_deleted $event
     * @return bool true on success.
     */
    public static function user_deleted($event) {
        microlearning::event_user_deleted($event);
        return true;
    }
}

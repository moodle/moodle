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
 * @package    local_iomad
 * @copyright  2016 E-Learn Design Ltd. (http://www.e-learndesign.co.uk)
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/iomad/lib/company.php');

class observer {

    /**
     * Triggered via competency_framework_created event.
     *
     * @param \core\event\competency_framework_created $event
     * @return bool true on success.
     */
    public static function competency_framework_created(\core\event\competency_framework_created $event) {
        company::competency_framework_created($event);
        return true;
    }

    /**
     * Triggered via competency_framework_deleted event.
     *
     * @param \core\event\competency_framework_deleted $event
     * @return bool true on success.
     */
    public static function competency_framework_deleted(\core\event\competency_framework_deleted $event) {
        company::competency_framework_deleted($event);
        return true;
    }

    /**
     * Triggered via competency_template_created event.
     *
     * @param \core\event\competency_template_created $event
     * @return bool true on success.
     */
    public static function competency_template_created(\core\event\competency_template_created $event) {
        company::competency_template_created($event);
        return true;
    }

    /**
     * Triggered via competency_template_deleted event.
     *
     * @param \core\event\competency_template_deleted $event
     * @return bool true on success.
     */
    public static function competency_template_deleted(\core\event\competency_template_deleted $event) {
        company::competency_template_deleted($event);
        return true;
    }

     * Triggered via course_completed event.
     *
     * @param \core\event\course_completed $event
     * @return bool true on success.
     */
    public static function course_completed($event) {
        \company::course_completed_supervisor($event);
        return true;
    }
}

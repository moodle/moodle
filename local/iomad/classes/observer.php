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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/iomad/lib/company.php');

class local_iomad_observer {

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

    /**
     * Triggered via course_deleted event.
     *
     * @param \core\event\course_deleted $event
     * @return bool true on success.
     */
    public static function course_deleted($event) {
        company::course_deleted($event);
        return true;
    }

    /**
     * Triggered via course_completed event.
     *
     * @param \core\event\course_completed $event
     * @return bool true on success.
     */
    public static function course_completed($event) {
        company::course_completed_supervisor($event);
        return true;
    }

    /**
     * Triggered via user_deleted event.
     *
     * @param \core\event\user_deleted $event
     * @return bool true on success.
     */
    public static function user_deleted($event) {
        company::user_deleted($event);
        return true;
    }

    /**
     * Triggered via user_created event.
     *
     * @param \core\event\user_created $event
     * @return bool true on success.
     */
    public static function user_created($event) {
        company::user_created($event);
        return true;
    }

    /**
     * Triggered via user_updated event.
     *
     * @param \core\event\user_updated $event
     * @return bool true on success.
     */
    public static function user_updated($event) {
        company::user_updated($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_created event.
     *
     * @param \block_iomad_company_admin\event\company_created $event
     * @return bool true on success.
     */
    public static function company_created($event) {
        company::company_created($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_updated event.
     *
     * @param \block_iomad_company_admin\event\company_updated $event
     * @return bool true on success.
     */
    public static function company_updated($event) {
        company::company_updated($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_user_assigned event.
     *
     * @param \block_iomad_company_admin\event\course_completed $event
     * @return bool true on success.
     */
    public static function company_user_assigned($event) {
        company::company_user_assigned($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_user_unassigned event.
     *
     * @param \block_iomad_company_admin\event\course_completed $event
     * @return bool true on success.
     */
    public static function company_user_unassigned($event) {
        company::company_user_unassigned($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::user_license_assigned event.
     *
     * @param \block_iomad_company_admin\event\user_license_assigned $event
     * @return bool true on success.
     */
    public static function user_license_assigned($event) {
        company::user_license_assigned($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::user_license_unassigned event.
     *
     * @param \block_iomad_company_admin\event\user_license_unassigned $event
     * @return bool true on success.
     */
    public static function user_license_unassigned($event) {
        company::user_license_unassigned($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_license_created event.
     *
     * @param \block_iomad_company_admin\event\company_license_created $event
     * @return bool true on success.
     */
    public static function company_license_created($event) {
        company::company_license_created($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_license_deleted event.
     *
     * @param \block_iomad_company_admin\event\company_license_deleted $event
     * @return bool true on success.
     */
    public static function company_license_deleted($event) {
        company::company_license_deleted($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_license_updated event.
     *
     * @param \block_iomad_company_admin\event\company_license_updated $event
     * @return bool true on success.
     */
    public static function company_license_updated($event) {
        company::company_license_updated($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_suspended event.
     *
     * @param \block_iomad_company_admin\event\company_suspended $event
     * @return bool true on success.
     */
    public static function company_suspended($event) {
        company::company_suspended($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_unsuspended event.
     *
     * @param \block_iomad_company_admin\event\company_unsuspended $event
     * @return bool true on success.
     */
    public static function company_unsuspended($event) {
        company::company_unsuspended($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::user_course_expired event.
     *
     * @param \block_iomad_company_admin\event\user_course_expired $event
     * @return bool true on success.
     */
    public static function user_course_expired($event) {
        company::user_course_expired($event);
        return true;
    }
}

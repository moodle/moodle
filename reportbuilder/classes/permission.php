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

namespace core_reportbuilder;

use context;
use context_system;
use core_reportbuilder\exception\report_access_exception;
use core_reportbuilder\local\helpers\audience;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\base;

/**
 * Report permission class
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission {

    /**
     * Require given user can view reports list
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @throws report_access_exception
     */
    public static function require_can_view_reports_list(?int $userid = null, ?context $context = null): void {
        if (!static::can_view_reports_list($userid, $context)) {
            throw new report_access_exception();
        }
    }

    /**
     * Whether given user can view reports list
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @return bool
     */
    public static function can_view_reports_list(?int $userid = null, ?context $context = null): bool {
        global $CFG;

        if ($context === null) {
            $context = context_system::instance();
        }

        return !empty($CFG->enablecustomreports) && has_any_capability([
            'moodle/reportbuilder:edit',
            'moodle/reportbuilder:editall',
            'moodle/reportbuilder:view',
            'moodle/reportbuilder:viewall',
        ], $context, $userid);
    }

    /**
     * Require given user can view report
     *
     * @param report $report
     * @param int|null $userid User ID to check, or the current user if omitted
     * @throws report_access_exception
     */
    public static function require_can_view_report(report $report, ?int $userid = null): void {
        if (!static::can_view_report($report, $userid)) {
            throw new report_access_exception('errorreportview');
        }
    }

    /**
     * Whether given user can view report
     *
     * @param report $report
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return bool
     */
    public static function can_view_report(report $report, ?int $userid = null): bool {
        if (!static::can_view_reports_list($userid, $report->get_context())) {
            return false;
        }

        if (has_capability('moodle/reportbuilder:viewall', $report->get_context(), $userid)) {
            return true;
        }

        if (self::can_edit_report($report, $userid)) {
            return true;
        }

        $reports = audience::user_reports_list($userid);
        return in_array($report->get('id'), $reports);
    }

    /**
     * Require given user can edit report
     *
     * @param report $report
     * @param int|null $userid User ID to check, or the current user if omitted
     * @throws report_access_exception
     */
    public static function require_can_edit_report(report $report, ?int $userid = null): void {
        if (!static::can_edit_report($report, $userid)) {
            throw new report_access_exception('errorreportedit');
        }
    }

    /**
     * Whether given user can edit report
     *
     * @param report $report
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return bool
     */
    public static function can_edit_report(report $report, ?int $userid = null): bool {
        global $CFG, $USER;

        if (empty($CFG->enablecustomreports)) {
            return false;
        }

        // We can only edit custom reports.
        if ($report->get('type') !== base::TYPE_CUSTOM_REPORT) {
            return false;
        }

        // To edit their own reports, users must have either of the 'edit' or 'editall' capabilities. For reports belonging
        // to other users, they must have the specific 'editall' capability.
        $userid = $userid ?: (int) $USER->id;
        if ($report->get('usercreated') === $userid) {
            return has_any_capability([
                'moodle/reportbuilder:edit',
                'moodle/reportbuilder:editall',
            ], $report->get_context(), $userid);
        } else {
            return has_capability('moodle/reportbuilder:editall', $report->get_context(), $userid);
        }
    }

    /**
     * Whether given user can create a new report
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @return bool
     */
    public static function can_create_report(?int $userid = null, ?context $context = null): bool {
        global $CFG;

        if ($context === null) {
            $context = context_system::instance();
        }

        return !empty($CFG->enablecustomreports) && has_any_capability([
            'moodle/reportbuilder:edit',
            'moodle/reportbuilder:editall',
        ], $context, $userid) && !manager::report_limit_reached();
    }

    /**
     * Require given user can create a new report
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @throws report_access_exception
     */
    public static function require_can_create_report(?int $userid = null, ?context $context = null): void {
        if (!static::can_create_report($userid, $context)) {
            throw new report_access_exception('errorreportcreate');
        }
    }
}

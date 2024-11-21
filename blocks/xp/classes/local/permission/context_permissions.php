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
 * Context permissions.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\permission;

use context;

/**
 * Context permissions.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_permissions implements access_permissions, access_logs_permissions, access_report_permissions {

    /** @var context The context. */
    protected $context;

    /**
     * Constructor.
     *
     * @param context $context The context.
     */
    public function __construct(context $context) {
        $this->context = $context;
    }

    /**
     * Whether the user can access the content.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public function can_access($userid = null) {
        return has_capability('block/xp:view', $this->context, $userid) || $this->can_manage($userid);
    }

    /**
     * Whether the user can access the logs.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public function can_access_logs($userid = null) {
        return has_capability('block/xp:viewlogs', $this->context, $userid);
    }

    /**
     * Whether the user can access the report.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public function can_access_report($userid = null) {
        return has_capability('block/xp:viewreport', $this->context, $userid);
    }

    /**
     * Whether the user can manage the content.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public function can_manage($userid = null) {
        return has_capability('block/xp:manage', $this->context, $userid);
    }

    /**
     * Requires for user to be able to access the content.
     *
     * @param int $userid The user ID.
     * @throws required_capability_exception
     */
    public function require_access($userid = null) {
        if (!$this->can_manage($userid)) {
            return require_capability('block/xp:view', $this->context, $userid);
        }
    }

    /**
     * Requires for user to be able to access the logs.
     *
     * @param int $userid The user ID.
     * @throws required_capability_exception
     */
    public function require_access_logs($userid = null) {
        return require_capability('block/xp:viewlogs', $this->context, $userid);
    }

    /**
     * Requires for user to be able to access the report.
     *
     * @param int $userid The user ID.
     * @throws required_capability_exception
     */
    public function require_access_report($userid = null) {
        return require_capability('block/xp:viewreport', $this->context, $userid);
    }

    /**
     * Requires for user to be able to manage the content.
     *
     * @param int $userid The user ID.
     * @throws required_capability_exception
     */
    public function require_manage($userid = null) {
        return require_capability('block/xp:manage', $this->context, $userid);
    }
}

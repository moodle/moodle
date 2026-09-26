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

namespace local_requirelogin;

/**
 * Course lists show a course when the user is enrolled in it, or when they
 * may browse the catalog (moodle/category:viewcourselist).
 *
 * Moodle grants that capability to the authenticated-user role, so every
 * logged-in account passes the browse check and the front page lists every
 * visible course. Enrolment is already the other half of the filter in
 * core_course_category::get_course_records(), so removing the browse grant
 * from learner roles leaves enrolled courses visible and hides the rest.
 *
 * The grant is removed, not prohibited. A prohibit on the authenticated-user
 * role would also hide the catalog from managers, because every manager has
 * that role as well. Site administrators still see every course through the
 * admin bypass in has_capability().
 *
 * @package    local_requirelogin
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courselist {
    /** Capability that allows a user to see courses they are not enrolled in. */
    public const CAPABILITY = 'moodle/category:viewcourselist';

    /**
     * Learner archetypes that must not browse the full catalog.
     */
    private const LEARNER_ARCHETYPES = ['user', 'guest'];

    /**
     * Staff archetypes that still manage courses and need the catalog.
     */
    private const STAFF_ARCHETYPES = ['manager', 'coursecreator'];

    /**
     * Re-apply the catalog rule when a learner role has been given browse access again.
     */
    public static function enforce(): void {
        if (self::skip()) {
            return;
        }
        if (self::learners_can_browse()) {
            self::restrict();
        }
    }

    /**
     * Remove catalog browsing from learner roles and keep it for staff.
     */
    public static function restrict(): void {
        if (self::skip()) {
            return;
        }

        $changed = false;
        foreach (self::roles_for(self::LEARNER_ARCHETYPES) as $roleid) {
            if (self::role_allows($roleid)) {
                unassign_capability(self::CAPABILITY, $roleid);
                $changed = true;
            }
        }

        $systemcontext = \context_system::instance();
        foreach (self::roles_for(self::STAFF_ARCHETYPES) as $roleid) {
            if (!self::role_allows($roleid, $systemcontext->id)) {
                assign_capability(self::CAPABILITY, CAP_ALLOW, $roleid, $systemcontext->id, true);
                $changed = true;
            }
        }

        if (!$changed) {
            return;
        }

        global $USER;
        if (!empty($USER->id)) {
            reload_all_capabilities();
        }
        \cache_helper::purge_by_event('changesincoursecat');
    }

    /**
     * True when a learner role still has an allow for catalog browsing.
     */
    private static function learners_can_browse(): bool {
        foreach (self::hot_role_ids() as $roleid) {
            if (self::role_allows($roleid)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Roles assigned to every visitor. Checked on each request.
     *
     * @return int[]
     */
    private static function hot_role_ids(): array {
        global $CFG;

        $ids = [];
        foreach (['defaultuserroleid', 'guestroleid'] as $key) {
            if (!empty($CFG->$key)) {
                $ids[] = (int) $CFG->$key;
            }
        }
        return array_values(array_unique($ids));
    }

    /**
     * Role ids for the given archetypes, plus the default user and guest roles.
     *
     * @param string[] $archetypes
     * @return int[]
     */
    private static function roles_for(array $archetypes): array {
        $ids = [];
        if (in_array('user', $archetypes, true) || in_array('guest', $archetypes, true)) {
            $ids = self::hot_role_ids();
        }
        foreach ($archetypes as $archetype) {
            foreach (get_archetype_roles($archetype) as $role) {
                $ids[] = (int) $role->id;
            }
        }
        return array_values(array_unique($ids));
    }

    /**
     * True when the role has an allow, optionally at one context.
     *
     * @param int $roleid
     * @param int|null $contextid
     */
    private static function role_allows(int $roleid, ?int $contextid = null): bool {
        global $DB;

        $conditions = [
            'roleid' => $roleid,
            'capability' => self::CAPABILITY,
            'permission' => CAP_ALLOW,
        ];
        if ($contextid !== null) {
            $conditions['contextid'] = $contextid;
        }
        return $DB->record_exists('role_capabilities', $conditions);
    }

    /**
     * Upstream test runs expect the default role definitions.
     */
    private static function skip(): bool {
        if (during_initial_install()) {
            return true;
        }
        return environment::is_automated_test();
    }
}

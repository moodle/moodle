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

namespace local_launchpad;

/**
 * Policy for reconciling existing LaunchPad objects on re-apply.
 *
 * New objects still use hidden defaults in setup. Existing objects must not be
 * force-hidden, renamed, or moved — admins may have deliberately shown a course
 * (MVP §4 / §10). Moodle category hide cascades to child categories and courses.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reconcile_policy {

    /**
     * Whether apply may set an existing category/course to hidden.
     *
     * Must stay false: core_course_category visibility updates cascade and would
     * undo deliberate "show ready course" releases.
     */
    public static function may_force_hide_existing(): bool {
        return false;
    }

    /**
     * Whether apply may rename an existing category/course fullname/name.
     */
    public static function may_rename_existing(): bool {
        return false;
    }

    /**
     * Whether apply may move an existing course/category to another parent.
     */
    public static function may_move_existing(): bool {
        return false;
    }

    /**
     * Documented Moodle behaviour used in reviews/tests (not an API probe).
     *
     * Hiding a category via core APIs also hides child categories and courses.
     */
    public static function category_hide_cascades_to_courses(): bool {
        return true;
    }
}

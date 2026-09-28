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
 * Home lists courses from a per-user cache. Joining a course does not clear
 * that cache, so a learner who just enrolled still sees the old list.
 *
 * @package    local_requirelogin
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolment_observer {
    /**
     * Drop this user's cached course list after their enrolment changes.
     *
     * @param \core\event\base $event
     */
    public static function enrolment_changed(\core\event\base $event): void {
        if (environment::is_automated_test() || during_initial_install()) {
            return;
        }
        \cache::make('core', 'coursecat')->purge();
    }
}

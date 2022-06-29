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

namespace format_theunittest\courseformat;

use core_courseformat\stateupdates;
use core_courseformat\stateactions as core_actions;
use stdClass;

/**
 * Fixture for fake course stateactions testing.
 *
 * @package    core_course
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateactions extends core_actions {

    /**
     * Alternative cm_state state action for testing.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int $targetsectionid optional target section id
     * @param int $targetcmid optional target cm id
     */
    public function cm_state(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $updates->add_cm_create(array_pop($ids));
    }

    /**
     * Course format custom state action.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int $targetsectionid optional target section id
     * @param int $targetcmid optional target cm id
     */
    public function format_do_something(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $updates->add_cm_delete(array_pop($ids));
    }

    /**
     * Course format target section testing action.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int $targetsectionid optional target section id
     * @param int $targetcmid optional target cm id
     */
    public function targetsection_test(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $updates->add_section_put($targetsectionid);
    }

    /**
     * Course format target cm testing action.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int $targetsectionid optional target section id
     * @param int $targetcmid optional target cm id
     */
    public function targetcm_test(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $updates->add_cm_put($targetcmid);
    }

}

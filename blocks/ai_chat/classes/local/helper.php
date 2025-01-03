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

namespace block_ai_chat\local;

use moodle_page;

/**
 * Class helper
 *
 * @package    block_ai_chat
 * @copyright  2024 Tobias Garske, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Check, if a block is existing in course context.
     * @param int $courseid
     * @return object|bool
     * @throws \dml_exception
     */
    public static function has_block_in_course_context(int $courseid): object|bool {
        global $DB;

        // Check if tenant is enabled for the school.
        $sql = "SELECT bi.*
                  FROM {block_instances} bi
                  JOIN {context} ctx ON bi.parentcontextid = ctx.id
                 WHERE bi.blockname = :blockname AND ctx.contextlevel = :contextlevel
                   AND ctx.instanceid = :courseid";

        $params = [
            'blockname' => 'ai_chat',
            'contextlevel' => CONTEXT_COURSE,
            'courseid' => $courseid,
        ];

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Helper function to determine if the global instance floating button should be shown.
     *
     * @param moodle_page $page The page needed to determine if the global instance should be rendered
     * @return bool true if the global instance floating button should be rendered or not
     */
    public static function show_global_block(moodle_page $page): bool {
        if (!isloggedin()) {
            return false;
        }

        if ($page->blocks->is_block_present('ai_chat')) {
            // If current page already has a block instance, we do not add a global one.
            return false;
        }

        $showonpagetypes = get_config('block_ai_chat', 'showonpagetypes');
        if (trim($showonpagetypes) === '*') {
            return true;
        }

        if (empty(trim($showonpagetypes))) {
            return false;
        }

        $pagetypes = [];
        foreach (explode(PHP_EOL, $showonpagetypes) as $pagetype) {
            $pagetype = trim($pagetype);
            if (!empty($pagetype)) {
                $pagetypes[] = $pagetype;
            }
        }
        $pagetypecheck = false;
        foreach ($pagetypes as $value) {
            if (str_starts_with($page->pagetype, $value)) {
                $pagetypecheck = true;
                break;
            }
        }
        return $pagetypecheck;
    }
}

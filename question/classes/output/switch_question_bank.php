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
 * core_question output class.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\output;

use cm_info;
use core_question\local\bank\question_bank_helper;
use renderer_base;

/**
 * Get the switch question bank rendered content. Displays lists of shared banks the viewing user has access to.
 */
class switch_question_bank implements \renderable, \templatable {

    /**
     * Instantiate the output class.
     *
     * @param int $quizcmid quiz course module id.
     * @param int $courseid of the current course.
     * @param int $userid of the user viewing the page.
     */
    public function __construct(
        /** @var int quiz course module id */
        private readonly int $quizcmid,
        /** @var int id of the current course */
        private readonly int $courseid,
        /** @var int id of the user viewing the page */
        private readonly int $userid
    ) {
    }

    /**
     * Create a list of question banks the user has access to for the template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        [, $cm] = get_module_from_cmid($this->quizcmid);
        $cminfo = cm_info::create($cm);

        $capabilities = ['moodle/question:useall', 'moodle/question:usemine'];
        $coursesharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(
            incourseids: [$this->courseid],
            havingcap: $capabilities,
            filtercontext:  $cminfo->context,
        );
        $recentlyviewedbanks = question_bank_helper::get_recently_used_open_banks($this->userid, havingcap: $capabilities);

        return [
            'quizname' => $cminfo->get_formatted_name(),
            'quizcmid' => $this->quizcmid,
            'quizcontextid' => $cminfo->context->id,
            'hascoursesharedbanks' => !empty($coursesharedbanks),
            'coursesharedbanks' => $coursesharedbanks,
            'hasrecentlyviewedbanks' => !empty($recentlyviewedbanks),
            'recentlyviewedbanks' => $recentlyviewedbanks,
        ];
    }
}

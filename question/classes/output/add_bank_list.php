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

namespace core_question\output;

use action_link;
use core_question\local\bank\question_bank_helper;
use renderer_base;
use stdClass;

/**
 * Create a list of 'Add another question bank' links for plugins that support FEATURE_PUBLISHES_QUESTIONS.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_bank_list implements \renderable, \templatable {

    /**
     * Instantiate the output class.
     *
     * @param stdClass $course the course currently being viewed.
     * @param array $bankplugins {@see question_bank_helper::get_activity_types_with_shareable_questions()}
     */
    public function __construct(
        /** @var stdClass $course the viewing course */
        protected readonly stdClass $course,
        /** @var array $bankplugins shareable bank type plugins */
        protected readonly array $bankplugins
    ) {
    }

    /**
     * Dynamically create an 'add' link for each module type that supports FEATURE_PUBLISHES_QUESTIONS.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {

        $addbanks = [];

        foreach ($this->bankplugins as $plugin) {

            if (!plugin_supports('mod', $plugin, FEATURE_PUBLISHES_QUESTIONS)) {
                continue;
            }

            $link = new action_link(
                new \moodle_url('/course/modedit.php', [
                    'add' => $plugin,
                    'course' => $this->course->id,
                    'section' => 0,
                    'return' => 0,
                    'sr' => 0,
                    'beforemod' => 0,
                ]),
                get_string('addanotherbank', $plugin),
                null,
                null,
                new \pix_icon('t/add', get_string('addanotherbank', $plugin))
            );
            $addbanks[] = $link->export_for_template($output);
        }

        return $addbanks;
    }
}

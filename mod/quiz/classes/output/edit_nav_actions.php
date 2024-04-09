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

namespace mod_quiz\output;

use moodle_url;
use renderable;
use renderer_base;
use templatable;
use url_select;

/**
 * Represents the tertiary navigation around the quiz edit pages.
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_nav_actions implements renderable, templatable {
    /** @var string option for $whichpage argument to the constructor. */
    const SUMMARY = 'summary';

    /** @var string option for $whichpage argument to the constructor. */
    const GRADING = 'grading';

    /**
     * overrides_action constructor.
     *
     * @param int $cmid The course module id.
     * @param string $whichpage self::SUMMARY (edit.php) or self::GRADING (editgrading.php).
     */
    public function __construct(

        /** @var int The course module ID. */
        protected readonly int $cmid,

        /** @var string which page this is. Either self::SUMMARY (edit.php) or self::GRADING (editgrading.php). */
        protected readonly string $whichpage,

    ) {
    }

    public function export_for_template(renderer_base $output): array {

        // Build the navigation drop-down.
        $questionsurl = new moodle_url('/mod/quiz/edit.php', ['cmid' => $this->cmid]);
        $gradeitemsetupurl = new moodle_url('/mod/quiz/editgrading.php', ['cmid' => $this->cmid]);

        $menu = [
            $questionsurl->out(false) => get_string('questions', 'quiz'),
            $gradeitemsetupurl->out(false) => get_string('gradeitemsetup', 'quiz'),
        ];

        $overridesnav = new url_select(
            $menu,
            $this->whichpage === self::SUMMARY ? $questionsurl->out(false) : $gradeitemsetupurl->out(false),
            null
        );
        $overridesnav->set_label(get_string('quizsetupnavigation', 'quiz'), ['class' => 'sr-only']);

        return [
            'navmenu' => $overridesnav->export_for_template($output),
        ];
    }
}

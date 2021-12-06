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

use templatable;
use renderable;
use renderer_base;
use moodle_url;
use url_select;

/**
 * Render results action
 *
 * @package mod_quiz
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resultsaction implements templatable, renderable {
    /** @var int */
    private $id;

    /**
     * resultsaction constructor.
     *
     * @param int $id The course module id.
     */
    public function __construct(int $id) {
        $this->id = $id;
    }

    /**
     * Provide data for the template
     *
     * @param renderer_base $output renderer_base object.
     * @return array data for template.
     */
    public function export_for_template(renderer_base $output): array {
        global $PAGE;

        $gradeslink = new moodle_url('/mod/quiz/report.php', ['id' => $this->id, 'mode' => 'overview']);
        $responseslink = new moodle_url('/mod/quiz/report.php', ['id' => $this->id, 'mode' => 'responses']);
        $statisticslink = new moodle_url('/mod/quiz/report.php', ['id' => $this->id, 'mode' => 'statistics']);
        $manualgrading = new moodle_url('/mod/quiz/report.php', ['id' => $this->id, 'mode' => 'grading']);

        $menu = [
            $gradeslink->out(false) => get_string('grades', 'grades'),
            $responseslink->out(false) => get_string('responses', 'quiz_responses'),
            $statisticslink->out(false) => get_string('statistics', 'quiz_statistics'),
            $manualgrading->out(false) => get_string('grading', 'quiz_grading')
        ];

        $urlselect = new url_select($menu, $PAGE->url->out(false), null, 'quizresults');

        $data = [
            'resultaction' => $urlselect->export_for_template($output)
        ];
        return $data;
    }
}

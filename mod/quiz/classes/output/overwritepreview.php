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

use renderable;
use renderer_base;
use templatable;
use moodle_url;

/**
 * Output the preview action area of this activity
 *
 * @package mod_quiz
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overwritepreview implements templatable, renderable {

    /** @var int */
    private $cmid;

    /** @var bool */
    private $canedit;

    /**
     * overwritepreview constructor.
     *
     * @param int $cmid The course module id.
     * @param bool $canedit Can edit the quiz.
     */
    public function __construct(int $cmid, bool $canedit) {
        $this->cmid = $cmid;
        $this->canedit = $canedit;
    }

    /**
     * Provide data for the template
     *
     * @param renderer_base $output
     * @return array data for the template
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'back' => (new moodle_url('/mod/quiz/view.php', ['id' => $this->cmid]))->out(false),
        ];
        if ($this->canedit) {
            $data['editlink'] = (new moodle_url('/mod/quiz/edit.php', ['cmid' => $this->cmid]))->out(false);
        }
        return $data;
    }
}

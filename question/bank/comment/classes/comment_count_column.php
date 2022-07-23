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

namespace qbank_comment;

use core_question\local\bank\column_base;
use question_bank;

/**
 * A column to show the number of comments.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment_count_column extends column_base {

    /**
     * Get the name of the column, used internally.
     *
     * @return string
     */
    public function get_name(): string {
        return 'commentcount';
    }

    /**
     * Get the title of the column that will be displayed.
     *
     * @return string
     */
    public function get_title(): string {
        return get_string('commentplural', 'qbank_comment');
    }

    /**
     * Generate the content to be displayed.
     *
     * @param object $question The question object.
     * @param string $rowclasses Classes that can be added.
     */
    protected function display_content($question, $rowclasses): void {
        global $DB, $PAGE;
        $args = [
            'component' => 'qbank_comment',
            'commentarea' => 'question',
            'itemid' => $question->id,
            'contextid' => 1
        ];
        $commentcount = $DB->count_records('comments', $args);
        $attributes = [];
        if (question_has_capability_on($question, 'comment')) {
            $target = 'questioncommentpreview_' . $question->id;
            $datatarget = '[data-target="' . $target . '"]';
            $PAGE->requires->js_call_amd('qbank_comment/comment', 'init', [$datatarget]);
            $attributes = [
                'href' => '#',
                'data-target' => $target,
                'data-questionid' => $question->id,
                'data-courseid' => $this->qbank->course->id,
            ];
        }
        echo \html_writer::tag('a', $commentcount, $attributes);
    }

    public function get_extra_classes(): array {
        return ['pr-3'];
    }

}

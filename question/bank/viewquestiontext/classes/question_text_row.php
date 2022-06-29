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

namespace qbank_viewquestiontext;

use core_question\local\bank\row_base;

/**
 * A column type for the name of the question name.
 *
 * @package   qbank_viewquestiontext
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_text_row extends row_base {

    /**
     * To initialise subclasses
     * @var $formatoptions
     */
    protected $formatoptions;

    protected function init(): void {
        $this->formatoptions = new \stdClass();
        $this->formatoptions->noclean = true;
        $this->formatoptions->para = false;
    }

    public function get_name(): string {
        return 'questiontext';
    }

    public function get_title(): string {
        return get_string('questiontext', 'question');
    }

    protected function display_content($question, $rowclasses): void {
        $text = question_rewrite_question_preview_urls($question->questiontext, $question->id,
                $question->contextid, 'question', 'questiontext', $question->id,
                $question->contextid, 'core_question');
        $text = format_text($text, $question->questiontextformat,
                $this->formatoptions);
        if ($text == '') {
            $text = '&#160;';
        }
        echo $text;
    }

    public function get_required_fields(): array {
        return ['q.questiontext', 'q.questiontextformat'];
    }

    public function has_preference(): bool {
        return true;
    }

    public function get_preference_key(): string {
        return 'qbshowtext';
    }

    public function get_preference(): bool {
        return question_get_display_preference($this->get_preference_key(), 0, PARAM_BOOL, new \moodle_url(''));
    }
}

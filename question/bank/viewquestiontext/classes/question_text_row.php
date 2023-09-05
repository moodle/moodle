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
use qbank_viewquestiontext\output\question_text_format;
use question_utils;

/**
 * A column type for the name of the question name.
 *
 * @package   qbank_viewquestiontext
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_text_row extends row_base {

    /** @var int if true, we will show the question text reduced to plain text, else it is fully rendered. */
    protected $preference;

    /** @var \stdClass $formatoptions options used when displaying the question text as HTML. */
    protected $formatoptions;

    protected function init(): void {

        // Cannot use $this->get_preference because of PHP type hints.
        $this->preference = (int)question_get_display_preference($this->get_preference_key(), 0, PARAM_INT, new \moodle_url(''));
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
        // Access 'showtext' filter from pagevars.
        if ($this->preference !== question_text_format::OFF) {
            $text = '';
            if ($this->preference === question_text_format::PLAIN) {
                $text = question_utils::to_plain_text($question->questiontext,
                        $question->questiontextformat, ['noclean' => true, 'para' => false, 'filter' => false]);
            } else if ($this->preference === question_text_format::FULL) {
                $text = question_rewrite_question_preview_urls($question->questiontext, $question->id,
                        $question->contextid, 'question', 'questiontext', $question->id,
                        $question->contextid, 'core_question');
                $text = format_text($text, $question->questiontextformat,
                        $this->formatoptions);
            }
            if ($text == '') {
                $text = '&#160;';
            }
            echo $text;
        }
    }

    public function get_required_fields(): array {
        return ['q.questiontext', 'q.questiontextformat'];
    }

    public function has_preference(): bool {
        return false;
    }

    public function get_preference_key(): string {
        return 'qbshowtext';
    }
}

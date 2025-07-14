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

namespace qbank_viewquestiontext\output;

use core_question\local\bank\view;
use qbank_viewquestiontext\question_text_row;
use renderer_base;

/**
 * Question text format selector.
 *
 * @package   qbank_viewquestiontext
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_text_format implements \renderable, \templatable {
    /**
     * @var int Question text is off.
     */
    const OFF = 0;

    /**
     * @var int Question text is displayed in plain text mode.
     */
    const PLAIN = 1;

    /**
     * @var int Question text is displayed fully rendered.
     */
    const FULL = 2;

    /** @var int|mixed The current display preference value. */
    protected int $preference;

    /**
     * @var \moodle_url The return URL for redirecting back to the current question bank page.
     */
    protected \moodle_url $returnurl;

    /**
     * Store the returnurl and the current preference value.
     *
     * @param view $qbank
     * @throws \moodle_exception
     */
    public function __construct(view $qbank) {
        $row = new question_text_row($qbank);
        $this->returnurl = new \moodle_url($qbank->returnurl);
        $this->preference = question_get_display_preference($row->get_preference_key(), 0, PARAM_INT, new \moodle_url(''));
    }

    public function export_for_template(renderer_base $output): array {
        return [
            'formaction' => new \moodle_url('/question/bank/viewquestiontext/save.php'),
            'sesskey' => sesskey(),
            'returnurl' => $this->returnurl->out(false),
            'options' => [
                (object)[
                    'label' => get_string('showquestiontext_off', 'question'),
                    'value' => self::OFF,
                    'selected' => $this->preference === self::OFF,
                ],
                (object)[
                    'label' => get_string('showquestiontext_plain', 'question'),
                    'value' => self::PLAIN,
                    'selected' => $this->preference === self::PLAIN,
                ],
                (object)[
                    'label' => get_string('showquestiontext_full', 'question'),
                    'value' => self::FULL,
                    'selected' => $this->preference === self::FULL,
                ],
            ],
            'label' => get_string('showquestiontext', 'core_question'),
        ];
    }
}

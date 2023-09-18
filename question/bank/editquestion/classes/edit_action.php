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
 * Class for question bank edit question column.
 *
 * @package   qbank_editquestion
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_editquestion;

use core_question\local\bank\question_action_base;
use moodle_url;

/**
 * Class for question bank edit question column.
 *
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_action extends question_action_base {

    /**
     * Contains the string.
     * @var string
     */
    protected $stredit;

    /**
     * Contains the string.
     * @var string
     */
    protected $strview;

    /**
     * Contains the url of the edit question page.
     * @var moodle_url|string
     */
    public $editquestionurl;

    public function init(): void {
        parent::init();
        $this->stredit = get_string('editquestion', 'question');
        $this->strview = get_string('view');
        $this->editquestionurl = new \moodle_url('/question/bank/editquestion/question.php',
                array('returnurl' => $this->qbank->returnurl));
        if ($this->qbank->cm !== null) {
            $this->editquestionurl->param('cmid', $this->qbank->cm->id);
        } else {
            $this->editquestionurl->param('courseid', $this->qbank->course->id);
        }
    }

    /**
     * Get the URL for editing a question as a link.
     *
     * @param int $questionid the question id.
     * @return moodle_url the URL, HTML-escaped.
     */
    public function edit_question_moodle_url($questionid): moodle_url {
        return new moodle_url($this->editquestionurl, ['id' => $questionid]);
    }

    protected function get_url_icon_and_label(\stdClass $question): array {
        if (!\question_bank::is_qtype_installed($question->qtype)) {
            // It sometimes happens that people end up with junk questions
            // in their question bank of a type that is no longer installed.
            // We cannot do most actions on them, because that leads to errors.
            return [null, null, null];
        }

        if (question_has_capability_on($question, 'edit')) {
            return [$this->edit_question_moodle_url($question->id), 't/edit', $this->stredit];
        } else if (question_has_capability_on($question, 'view')) {
            return [$this->edit_question_moodle_url($question->id), 'i/info', $this->strview];
        } else {
            return [null, null, null];
        }
    }
}

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
 * Question Import for H5P Quiz content type
 *
 * @package    qformat_h5p
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace qformat_h5p\local;

use qformat_h5p\local;
use stdClass;
use context_user;
use question_bank;

/**
 * Question Import for H5P Quiz content type
 *
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class type_fib extends type_mc {
    /**
     * Converts the content object to question object
     *
     * @return object question data
     */
    public function import_question() {
        global $OUTPUT;

        $context = $this->prepare_context();
        if (!empty($this->params->media) && $itemid = $this->import_media_as_draft($this->params->media)) {
            $context->media = $this->params->media;
            $questiontext['questiontextitemid'] = $itemid;
            $this->itemid = $itemid;
        }
        $questiontext['text'] = $OUTPUT->render_from_template($this->template, $context);

        question_bank::get_qtype('multianswer');

        $qo = qtype_multianswer_extract_question($questiontext);
        $errors = qtype_multianswer_validate_question($qo);
        if ($errors) {
            $this->error(get_string('invalidmultianswerquestion', 'qtype_multianswer', implode(' ', $errors)));
            return null;
        }

        // Question name.
        $qo->name = $this->clean_question_name($this->metadata->title);

        $qo->qtype = 'multianswer';
        if (!empty($this->itemid)) {
            $qo->questiontextitemid = $this->itemid;
        }
        $qo->questiontextformat = FORMAT_HTML;
        $qo->questiontext = $qo->questiontext['text'];

        $qo->generalfeedbackformat = FORMAT_HTML;
        $qo->generalfeedback = '';
        return $qo;
    }

    /**
     * Preprocess context
     *
     * @return Object $context
     */
    public function prepare_context() {
        $questions = preg_replace('/\\*([^\\*]+)\\*/', '{1:SHORTANSWER:~%100%$1}', $this->params->questions);

        // Import media file.
        $context = new stdClass();
        $context->questiontext = '<div>' . implode('</div><div>', $questions) . '</div>';

        return $context;
    }
}

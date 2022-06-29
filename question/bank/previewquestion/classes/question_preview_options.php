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

namespace qbank_previewquestion;

use question_display_options;

/**
 * Displays question preview options as default and set the options.
 *
 * Setting default, getting and setting user preferences in question preview options.
 *
 * @package    qbank_previewquestion
 * @copyright  2010 The Open University
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_preview_options extends question_display_options {

    /** @var string the behaviour to use for this preview. */
    public $behaviour;

    /** @var number the maximum mark to use for this preview. */
    public $maxmark;

    /** @var int the variant of the question to preview. */
    public $variant;

    /** @var string prefix to append to field names to get user_preference names. */
    const OPTIONPREFIX = 'question_preview_options_';

    /**
     * Constructor.
     * @param \stdClass $question
     */
    public function __construct($question) {
        $this->behaviour = 'deferredfeedback';
        $this->maxmark = $question->defaultmark;
        $this->variant = null;
        $this->correctness = self::VISIBLE;
        $this->marks = self::MARK_AND_MAX;
        $this->markdp = get_config('quiz', 'decimalpoints');
        $this->feedback = self::VISIBLE;
        $this->numpartscorrect = $this->feedback;
        $this->generalfeedback = self::VISIBLE;
        $this->rightanswer = self::VISIBLE;
        $this->history = self::HIDDEN;
        $this->flags = self::HIDDEN;
        $this->manualcomment = self::HIDDEN;
    }

    /**
     * Names of the options we store in the user preferences table.
     * @return array
     */
    protected function get_user_pref_fields(): array {
        return ['behaviour', 'correctness', 'marks', 'markdp', 'feedback', 'generalfeedback', 'rightanswer', 'history'];
    }

    /**
     * Names and param types of the options we read from the request.
     * @return array
     */
    protected function get_field_types(): array {
        return [
                'behaviour' => PARAM_ALPHA,
                'maxmark' => PARAM_LOCALISEDFLOAT,
                'variant' => PARAM_INT,
                'correctness' => PARAM_BOOL,
                'marks' => PARAM_INT,
                'markdp' => PARAM_INT,
                'feedback' => PARAM_BOOL,
                'generalfeedback' => PARAM_BOOL,
                'rightanswer' => PARAM_BOOL,
                'history' => PARAM_BOOL,
        ];
    }

    /**
     * Load the value of the options from the user_preferences table.
     */
    public function load_user_defaults(): void {
        $defaults = get_config('question_preview');
        foreach ($this->get_user_pref_fields() as $field) {
            $this->$field = get_user_preferences(
                    self::OPTIONPREFIX . $field, $defaults->$field);
        }
        $this->numpartscorrect = $this->feedback;
    }

    /**
     * Save a change to the user's preview options to the database.
     * @param object $newoptions
     */
    public function save_user_preview_options($newoptions): void {
        foreach ($this->get_user_pref_fields() as $field) {
            if (isset($newoptions->$field)) {
                set_user_preference(self::OPTIONPREFIX . $field, $newoptions->$field);
            }
        }
    }

    /**
     * Set the value of any fields included in the request.
     */
    public function set_from_request(): void {
        foreach ($this->get_field_types() as $field => $type) {
            $this->$field = optional_param($field, $this->$field, $type);
        }
        $this->numpartscorrect = $this->feedback;
    }

    /**
     * Parameters needed in the URL when continuing this preview.
     *
     * @return array URL fragment.
     */
    public function get_url_params(): array {
        $params = [];
        foreach ($this->get_field_types() as $field => $notused) {
            if ($field === 'behaviour' || $field === 'maxmark' || is_null($this->$field)) {
                continue;
            }
            $params[$field] = $this->$field;
        }
        return $params;
    }
}

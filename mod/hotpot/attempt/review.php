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
 * Review results of an attempt at a HotPot quiz
 * Output format: hp
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_hotpot_attempt_review
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_review {

    /**
     * return true if question text should appear on review page, or false otherwise
     *
     * @return boolean
     */
    static function show_question_text()  {
        return true;
    }

    /**
     * attempt_fields
     *
     * @return xxx
     */
    static function attempt_fields()  {
        return array('attempt', 'score', 'penalties', 'status', 'duration', 'timemodified');
    }

    /**
     * response_text_fields
     *
     * @return xxx
     */
    static function response_text_fields()  {
        return array('correct', 'ignored', 'wrong');
    }

    /**
     * response_num_fields
     *
     * @return xxx
     */
    static function response_num_fields()  {
        return array('score', 'weighting', 'hints', 'clues', 'checks');
    }

    /**
     * does this output format allow quiz attempts to be reviewed?
     *
     * @return boolean true if attempts can be reviewed, otherwise false
     */
    static function provide_review()  {
        return true;
    }

    /**
     * can the current quiz attempt be reviewed now?
     *
     * @return boolean true if attempt can be reviewed, otherwise false
     */
    static function can_reviewattempt($attempt=null)  {
        if (self::provide_review() && $hotpot->reviewoptions) {
            if ($attempt = $hotpot->get_attempt()) {
                if ($hotpot->reviewoptions & hotpot::REVIEW_DURINGATTEMPT) {
                    // during attempt
                    if ($hotpot->attempt->status==hotpot::STATUS_INPROGRESS) {
                        return true;
                    }
                }
                if ($hotpot->reviewoptions & hotpot::REVIEW_AFTERATTEMPT) {
                    // after attempt (but before quiz closes)
                    if ($hotpot->attempt->status==hotpot::STATUS_COMPLETED) {
                        return true;
                    }
                    if ($hotpot->attempt->status==hotpot::STATUS_ABANDONED) {
                        return true;
                    }
                    if ($hotpot->attempt->status==hotpot::STATUS_TIMEDOUT) {
                        return true;
                    }
                }
                if ($hotpot->reviewoptions & hotpot::REVIEW_AFTERCLOSE) {
                    // after the quiz closes
                    if ($hotpot->timeclose < $hotpot->time) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * review
     *
     * we use call_user_func() a lot in this function to prevent syntax error in PHP 5.2.x
     * note that PHP 5.4 does not allow pass-by-reference in call_user_func()
     * in such a case we must use call_user_func_array($callback, array(&$pass_by_reference))
     */
    static function review($hotpot, $class)  {
        global $DB;

        // for the time-being we set this setting manually here
        // but one day it will be settable in "mod/hotpot/mod_form.php"
        //$hotpot->reviewoptions = hotpot::REVIEW_DURINGATTEMPT | hotpot::REVIEW_AFTERATTEMPT | hotpot::REVIEW_AFTERCLOSE;

        // the $hotpot should already have the $attempt attached to it
        if (! $reviewoptions = $hotpot->can_reviewattempt()) {
            // oops, we are not allowed to review this $hotpot->attempt
            if ($hotpot->timeclose && $hotpot->timeclose > $hotpot->time) {
                // quiz is closed
                if ($hotpot->reviewoptions & hotpot::REVIEW_AFTERATTEMPT) {
                    return get_string('noreviewbeforeclose', 'mod_hotpot', userdate($hotpot->timeclose));
                }
            } else {
                // quiz is still open
                if ($hotpot->reviewoptions & hotpot::REVIEW_AFTERCLOSE) {
                    return get_string('noreviewafterclose', 'mod_hotpot');
                }
            }
            return get_string('noreview', 'mod_hotpot');
        }

        // we need to know if this user can review all attempts (i.e. a "teacher")
        $reviewallattempts = $hotpot->can_reviewallattempts();

        // if necessary, remove score and weighting fields
        $response_num_fields = call_user_func(array($class, 'response_num_fields'));
        if (! ($reviewallattempts || $reviewoptions & hotpot::REVIEW_SCORES)) {
            $response_num_fields = preg_grep('/^score|weighting$/', $response_num_fields, PREG_GREP_INVERT);
        }

        // if necessary, remove reponses fields
        $response_text_fields = call_user_func(array($class, 'response_text_fields'));
        if (! ($reviewallattempts || $reviewoptions & hotpot::REVIEW_RESPONSES)) {
            $response_text_fields = array();
        }

        // set flag to remove, if necessary, labels that show whether responses are correct or not
        if (! ($reviewallattempts || $reviewoptions & hotpot::REVIEW_ANSWERS)) {
            $neutralize_text_fields = true;
        } else {
            $neutralize_text_fields = false;
        }

        $table = new html_table();
        $table->id          = 'responses';
        $table->class       = 'generaltable generalbox';
        $table->cellpadding = 4;
        $table->cellspacing = 0;

        if (count($response_num_fields)) {
            $question_colspan  = count($response_num_fields) * 2;
            $textfield_colspan = $question_colspan - 1;
        } else {
            $question_colspan  = 2;
            $textfield_colspan = 1;
        }

        $strtimeformat = get_string('strftimerecentfull');

        $attempt_fields = call_user_func(array($class, 'attempt_fields'));
        foreach ($attempt_fields as $field) {
            $row = new html_table_row();

            // add heading
            $text = call_user_func(array($class, 'format_attempt_heading'), $field);
            $cell = new html_table_cell($text, array('class'=>'attemptfield'));
            $row->cells[] = $cell;

            // add data
            $text = call_user_func(array($class, 'format_attempt_data'), $hotpot->attempt, $field, $strtimeformat);
            $cell = new html_table_cell($text, array('class'=>'attemptvalue'));
            $cell->colspan = $textfield_colspan;
            $row->cells[] = $cell;

            $table->data[] = $row;
        }

        // get questions and responses relevant to this quiz attempt
        $questions = $DB->get_records('hotpot_questions', array('hotpotid' => $hotpot->id));
        $responses = $DB->get_records('hotpot_responses', array('attemptid' => $hotpot->attempt->id));

        if (empty($questions) || empty($responses)) {
            $row = new html_table_row();

            $cell = new html_table_cell(get_string('noresponses', 'mod_hotpot'), array('class'=>'noresponses'));
            $cell->colspan = $question_colspan;

            $row->cells[] = $cell;
            $table->data[] = $row;

        } else {

            // we have some responses, so print them
            foreach ($responses as $response) {

                if (empty($questions[$response->questionid])) {
                    continue; // invalid question id - shouldn't happen !!
                }

                // add separator
                if (count($table->data)) {
                    $callback = array($class, 'add_separator'); // PHP 5.2
                    call_user_func_array($callback, array(&$table, $question_colspan));
                }

                // question text
                if (call_user_func(array($class, 'show_question_text'))) {
                    if ($text = hotpot::get_question_text($questions[$response->questionid])) {
                        $callback = array($class, 'add_question_text'); // PHP 5.2
                        call_user_func_array($callback, array(&$table, $text, $question_colspan));
                    }
                }

                // string fields
                $neutral_text = '';
                foreach ($response_text_fields as $field) {
                    if (empty($response->$field)) {
                        continue; // shouldn't happen !!
                    }

                    $text = array();
                    if ($records = hotpot::get_strings($response->$field)) {
                        foreach ($records as $record) {
                            $text[] = $record->string;
                        }
                    }
                    unset($records);

                    if (! $text = implode(',', $text)) {
                        continue; // skip empty rows
                    }

                    if ($neutralize_text_fields) {
                        $neutral_text .= ($neutral_text ? ',' : '').$text;
                    } else {
                        $callback = array($class, 'add_text_field'); // PHP 5.2
                        call_user_func_array($callback, array(&$table, $field, $text, $textfield_colspan));
                    }
                }
                if ($neutral_text) {
                    $callback = array($class, 'add_text_field'); // PHP 5.2
                    call_user_func_array($callback, array(&$table, 'responses', $neutral_text, $textfield_colspan));
                }

                // numeric fields
                $row = new html_table_row();
                foreach ($response_num_fields as $field) {
                    $callback = array($class, 'add_num_field'); // PHP 5.2
                    call_user_func_array($callback, array(&$row, $field, $response->$field));
                }
                $table->data[] = $row;
            }
        }

        return html_writer::table($table);
    }

    /**
     * format_attempt_heading
     *
     * @param xxx $field
     * @return xxx
     */
    static function format_attempt_heading($field) {
        switch ($field) {
            case 'timemodified': return get_string('time', 'quiz');
            case 'attempt'     : return get_string('attemptnumber', 'mod_hotpot');
            case 'score'       : return get_string('score', 'quiz');
            default            : return get_string($field, 'mod_hotpot');
        }
    }

    /**
     * format_attempt_data
     *
     * @param xxx $attempt
     * @param xxx $field
     * @param xxx $strtimeformat
     * @return xxx
     */
    static function format_attempt_data($attempt, $field, $strtimeformat) {
        switch ($field) {
            case 'status'      : return hotpot::format_status($attempt->$field);
            case 'duration'    : return format_time($attempt->timemodified - $attempt->timestart);
            case 'timemodified': return trim(userdate($attempt->$field, $strtimeformat));
            default            : return $attempt->$field;
        }
    }

    /**
     * add_separator
     *
     * @param xxx $table (passed by reference)
     * @param xxx $colspan
     */
    static function add_separator(&$table, $colspan)  {
        $row = new html_table_row();

        $text = html_writer::tag('div', '', array('class' => 'tabledivider'));
        $cell = new html_table_cell($text);
        $cell->colspan = $colspan;

        $row->cells[] = $cell;
        $table->data[] = $row;
    }

    /**
     * add_question_text
     *
     * @param xxx $table (passed by reference)
     * @param xxx $text
     * @param xxx $colspan
     */
    static function add_question_text(&$table, $text, $colspan)  {
        $row = new html_table_row();

        $cell = new html_table_cell($text, array('class'=>'questiontext'));
        $cell->colspan = $colspan;

        $row->cells[] = $cell;
        $table->data[] = $row;
    }

    /**
     * add_text_field
     *
     * @param xxx $table (passed by reference)
     * @param xxx $field
     * @param xxx $text
     * @param xxx $colspan
     */
    static function add_text_field(&$table, $field, $text, $colspan)  {
        $row = new html_table_row();

        // heading
        $cell = new html_table_cell(get_string($field, 'mod_hotpot'), array('class'=>'responsefield'));
        $row->cells[] = $cell;

        // data
        $cell = new html_table_cell($text, array('class'=>'responsevalue'));
        $cell->colspan = $colspan;
        $row->cells[] = $cell;

        $table->data[] = $row;
    }

    /**
     * add_num_field
     *
     * @param xxx $row (passed by reference)
     * @param xxx $field
     * @param xxx $value
     */
    static function add_num_field(&$row, $field, $value)  {
        // heading
        $cell = new html_table_cell(get_string($field, 'mod_hotpot'), array('class'=>'responsefield'));
        $row->cells[] = $cell;

        // data
        $cell = new html_table_cell($value, array('class'=>'responsevalue'));
        $row->cells[] = $cell;
    }
}

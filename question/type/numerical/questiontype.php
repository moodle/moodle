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
 * Question type class for the numerical question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/question.php');


/**
 * The numerical question type class.
 *
 * This class contains some special features in order to make the
 * question type embeddable within a multianswer (cloze) question
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical extends question_type {
    const UNITINPUT = 0;
    const UNITRADIO = 1;
    const UNITSELECT = 2;

    const UNITNONE = 3;
    const UNITGRADED = 1;
    const UNITOPTIONAL = 0;

    const UNITGRADEDOUTOFMARK = 1;
    const UNITGRADEDOUTOFMAX = 2;

    public function get_question_options($question) {
        global $CFG, $DB, $OUTPUT;
        parent::get_question_options($question);
        // Get the question answers and their respective tolerances
        // Note: question_numerical is an extension of the answer table rather than
        //       the question table as is usually the case for qtype
        //       specific tables.
        if (!$question->options->answers = $DB->get_records_sql(
                                "SELECT a.*, n.tolerance " .
                                "FROM {question_answers} a, " .
                                "     {question_numerical} n " .
                                "WHERE a.question = ? " .
                                "    AND   a.id = n.answer " .
                                "ORDER BY a.id ASC", array($question->id))) {
            echo $OUTPUT->notification('Error: Missing question answer for numerical question ' .
                    $question->id . '!');
            return false;
        }

        $question->hints = $DB->get_records('question_hints',
                array('questionid' => $question->id), 'id ASC');

        $this->get_numerical_units($question);
        // get_numerical_options() need to know if there are units
        // to set correctly default values
        $this->get_numerical_options($question);

        // If units are defined we strip off the default unit from the answer, if
        // it is present. (Required for compatibility with the old code and DB).
        if ($defaultunit = $this->get_default_numerical_unit($question)) {
            foreach ($question->options->answers as $key => $val) {
                $answer = trim($val->answer);
                $length = strlen($defaultunit->unit);
                if ($length && substr($answer, -$length) == $defaultunit->unit) {
                    $question->options->answers[$key]->answer =
                            substr($answer, 0, strlen($answer)-$length);
                }
            }
        }

        return true;
    }

    public function get_numerical_units(&$question) {
        global $DB;

        if ($units = $DB->get_records('question_numerical_units',
                array('question' => $question->id), 'id ASC')) {
            $units = array_values($units);
        } else {
            $units = array();
        }
        foreach ($units as $key => $unit) {
            $units[$key]->multiplier = clean_param($unit->multiplier, PARAM_NUMBER);
        }
        $question->options->units = $units;
        return true;
    }

    public function get_default_numerical_unit($question) {
        if (isset($question->options->units[0])) {
            foreach ($question->options->units as $unit) {
                if (abs($unit->multiplier - 1.0) < '1.0e-' . ini_get('precision')) {
                    return $unit;
                }
            }
        }
        return false;
    }

    public function get_numerical_options($question) {
        global $DB;
        if (!$options = $DB->get_record('question_numerical_options',
                array('question' => $question->id))) {
            // Old question, set defaults.
            $question->options->unitgradingtype = 0;
            $question->options->unitpenalty = 0.1;
            if ($defaultunit = $this->get_default_numerical_unit($question)) {
                $question->options->showunits = self::UNITINPUT;
            } else {
                $question->options->showunits = self::UNITNONE;
            }
            $question->options->unitsleft = 0;

        } else {
            $question->options->unitgradingtype = $options->unitgradingtype;
            $question->options->unitpenalty = $options->unitpenalty;
            $question->options->showunits = $options->showunits;
            $question->options->unitsleft = $options->unitsleft;
        }

        return true;
    }

    /**
     * Save the units and the answers associated with this question.
     */
    public function save_question_options($question) {
        global $DB;
        $context = $question->context;

        // Get old versions of the objects
        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');
        $oldoptions = $DB->get_records('question_numerical',
                array('question' => $question->id), 'answer ASC');

        // Save the units.
        $result = $this->save_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = $result->units;
        }

        // Insert all the new answers
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ingore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key]['text'])) {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            if (trim($answerdata) === '*') {
                $answer->answer = '*';
            } else {
                $answer->answer = $this->apply_unit($answerdata, $units,
                        !empty($question->unitsleft));
                if ($answer->answer === false) {
                    $result->notice = get_string('invalidnumericanswer', 'quiz');
                }
            }
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];
            $DB->update_record('question_answers', $answer);

            // Set up the options object
            if (!$options = array_shift($oldoptions)) {
                $options = new stdClass();
            }
            $options->question = $question->id;
            $options->answer   = $answer->id;
            if (trim($question->tolerance[$key]) == '') {
                $options->tolerance = '';
            } else {
                $options->tolerance = $this->apply_unit($question->tolerance[$key],
                        $units, !empty($question->unitsleft));
                if ($options->tolerance === false) {
                    $result->notice = get_string('invalidnumerictolerance', 'quiz');
                }
            }
            if (isset($options->id)) {
                $DB->update_record('question_numerical', $options);
            } else {
                $DB->insert_record('question_numerical', $options);
            }
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }
        foreach ($oldoptions as $oldoption) {
            $DB->delete_records('question_numerical', array('id' => $oldoption->id));
        }

        $result = $this->save_unit_options($question);
        if (!empty($result->error) || !empty($result->notice)) {
            return $result;
        }

        $this->save_hints($question);

        return true;
    }

    /**
     * The numerical options control the display and the grading of the unit
     * part of the numerical question and related types (calculateds)
     * Questions previous to 2.0 do not have this table as multianswer questions
     * in all versions including 2.0. The default values are set to give the same grade
     * as old question.
     *
     */
    public function save_unit_options($question) {
        global $DB;
        $result = new stdClass();

        $update = true;
        $options = $DB->get_record('question_numerical_options',
                array('question' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->question = $question->id;
            $options->id = $DB->insert_record('question_numerical_options', $options);
        }

        if (isset($question->unitpenalty)) {
            $options->unitpenalty = $question->unitpenalty;
        } else {
            // Either an old question or a close question type.
            $options->unitpenalty = 1;
        }

        $options->unitgradingtype = 0;
        if (isset($question->unitrole)) {
            // Saving the editing form.
            $options->showunits = $question->unitrole;
            if ($question->unitrole == self::UNITGRADED) {
                $options->unitgradingtype = $question->unitgradingtypes;
                $options->showunits = $question->multichoicedisplay;
            }

        } else if (isset($question->showunits)) {
            // Updated import, e.g. Moodle XML.
            $options->showunits = $question->showunits;

        } else {
            // Legacy import.
            if ($defaultunit = $this->get_default_numerical_unit($question)) {
                $options->showunits = self::UNITINPUT;
            } else {
                $options->showunits = self::UNITNONE;
            }
        }

        $options->unitsleft = !empty($question->unitsleft);

        $DB->update_record('question_numerical_options', $options);

        // Report any problems.
        if (!empty($result->notice)) {
            return $result;
        }

        return true;
    }

    public function save_units($question) {
        global $DB;
        $result = new stdClass();

        // Delete the units previously saved for this question.
        $DB->delete_records('question_numerical_units', array('question' => $question->id));

        // Nothing to do.
        if (!isset($question->multiplier)) {
            $result->units = array();
            return $result;
        }

        // Save the new units.
        $units = array();
        $unitalreadyinsert = array();
        foreach ($question->multiplier as $i => $multiplier) {
            // Discard any unit which doesn't specify the unit or the multiplier
            if (!empty($question->multiplier[$i]) && !empty($question->unit[$i]) &&
                    !array_key_exists($question->unit[$i], $unitalreadyinsert)) {
                $unitalreadyinsert[$question->unit[$i]] = 1;
                $units[$i] = new stdClass();
                $units[$i]->question = $question->id;
                $units[$i]->multiplier = $this->apply_unit($question->multiplier[$i],
                        array(), false);
                $units[$i]->unit = $question->unit[$i];
                $DB->insert_record('question_numerical_units', $units[$i]);
            }
        }
        unset($question->multiplier, $question->unit);

        $result->units = &$units;
        return $result;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_numerical_answers($question, $questiondata);
        $question->unitdisplay = $questiondata->options->showunits;
        $question->unitgradingtype = $questiondata->options->unitgradingtype;
        $question->unitpenalty = $questiondata->options->unitpenalty;
        $question->ap = $this->make_answer_processor($questiondata->options->units,
                $questiondata->options->unitsleft);
    }

    public function initialise_numerical_answers(question_definition $question, $questiondata) {
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new qtype_numerical_answer($a->id, $a->answer,
                    $a->fraction, $a->feedback, $a->feedbackformat, $a->tolerance);
        }
    }

    public function make_answer_processor($units, $unitsleft) {
        if (empty($units)) {
            return new qtype_numerical_answer_processor(array());
        }

        $cleanedunits = array();
        foreach ($units as $unit) {
            $cleanedunits[$unit->unit] = $unit->multiplier;
        }

        return new qtype_numerical_answer_processor($cleanedunits, $unitsleft);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_numerical', array('question' => $questionid));
        $DB->delete_records('question_numerical_options', array('question' => $questionid));
        $DB->delete_records('question_numerical_units', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    public function get_random_guess_score($questiondata) {
        foreach ($questiondata->options->answers as $aid => $answer) {
            if ('*' == trim($answer->answer)) {
                return max($answer->fraction - $questiondata->options->unitpenalty, 0);
            }
        }
        return 0;
    }

    /**
     * Add a unit to a response for display.
     * @param object $questiondata the data defining the quetsion.
     * @param string $answer a response.
     * @param object $unit a unit. If null, {@link get_default_numerical_unit()}
     * is used.
     */
    public function add_unit($questiondata, $answer, $unit = null) {
        if (is_null($unit)) {
            $unit = $this->get_default_numerical_unit($questiondata);
        }

        if (!$unit) {
            return $answer;
        }

        if (!empty($questiondata->options->unitsleft)) {
            return $unit->unit . ' ' . $answer;
        } else {
            return $answer . ' ' . $unit->unit;
        }
    }

    public function get_possible_responses($questiondata) {
        $responses = array();

        $unit = $this->get_default_numerical_unit($questiondata);

        foreach ($questiondata->options->answers as $aid => $answer) {
            $responseclass = $answer->answer;

            if ($responseclass != '*') {
                $responseclass = $this->add_unit($questiondata, $responseclass, $unit);

                $ans = new qtype_numerical_answer($answer->id, $answer->answer, $answer->fraction,
                        $answer->feedback, $answer->feedbackformat, $answer->tolerance);
                list($min, $max) = $ans->get_tolerance_interval();
                $responseclass .= " ($min..$max)";
            }

            $responses[$aid] = new question_possible_response($responseclass,
                    $answer->fraction);
        }
        $responses[null] = question_possible_response::no_response();

        return array($questiondata->id => $responses);
    }

    /**
     * Checks if the $rawresponse has a unit and applys it if appropriate.
     *
     * @param string $rawresponse  The response string to be converted to a float.
     * @param array $units         An array with the defined units, where the
     *                             unit is the key and the multiplier the value.
     * @return float               The rawresponse with the unit taken into
     *                             account as a float.
     */
    public function apply_unit($rawresponse, $units, $unitsleft) {
        $ap = $this->make_answer_processor($units, $unitsleft);
        list($value, $unit, $multiplier) = $ap->apply_units($rawresponse);
        if (!is_null($multiplier)) {
            $value *= $multiplier;
        }
        return $value;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
    }
}


/**
 * This class processes numbers with units.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_answer_processor {
    /** @var array unit name => multiplier. */
    protected $units;
    /** @var string character used as decimal point. */
    protected $decsep;
    /** @var string character used as thousands separator. */
    protected $thousandssep;
    /** @var boolean whether the units come before or after the number. */
    protected $unitsbefore;

    protected $regex = null;

    public function __construct($units, $unitsbefore = false, $decsep = null,
            $thousandssep = null) {
        if (is_null($decsep)) {
            $decsep = get_string('decsep', 'langconfig');
        }
        $this->decsep = $decsep;

        if (is_null($thousandssep)) {
            $thousandssep = get_string('thousandssep', 'langconfig');
        }
        $this->thousandssep = $thousandssep;

        $this->units = $units;
        $this->unitsbefore = $unitsbefore;
    }

    /**
     * Set the decimal point and thousands separator character that should be used.
     * @param string $decsep
     * @param string $thousandssep
     */
    public function set_characters($decsep, $thousandssep) {
        $this->decsep = $decsep;
        $this->thousandssep = $thousandssep;
        $this->regex = null;
    }

    /** @return string the decimal point character used. */
    public function get_point() {
        return $this->decsep;
    }

    /** @return string the thousands separator character used. */
    public function get_separator() {
        return $this->thousandssep;
    }

    /**
     * @return book If the student's response contains a '.' or a ',' that
     * matches the thousands separator in the current locale. In this case, the
     * parsing in apply_unit can give a result that the student did not expect.
     */
    public function contains_thousands_seaparator($value) {
        if (!in_array($this->thousandssep, array('.', ','))) {
            return false;
        }

        return strpos($value, $this->thousandssep) !== false;
    }

    /**
     * Create the regular expression that {@link parse_response()} requires.
     * @return string
     */
    protected function build_regex() {
        if (!is_null($this->regex)) {
            return $this->regex;
        }

        $decsep = preg_quote($this->decsep, '/');
        $thousandssep = preg_quote($this->thousandssep, '/');
        $beforepointre = '([+-]?[' . $thousandssep . '\d]*)';
        $decimalsre = $decsep . '(\d*)';
        $exponentre = '(?:e|E|(?:x|\*|×)10(?:\^|\*\*))([+-]?\d+)';

        $numberbit = "$beforepointre(?:$decimalsre)?(?:$exponentre)?";

        if ($this->unitsbefore) {
            $this->regex = "/$numberbit$/";
        } else {
            $this->regex = "/^$numberbit/";
        }
        return $this->regex;
    }

    /**
     * This method can be used for more locale-strict parsing of repsonses. At the
     * moment we don't use it, and instead use the more lax parsing in apply_units.
     * This is just a note that this funciton was used in the past, so if you are
     * intersted, look through version control history.
     *
     * Take a string which is a number with or without a decimal point and exponent,
     * and possibly followed by one of the units, and split it into bits.
     * @param string $response a value, optionally with a unit.
     * @return array four strings (some of which may be blank) the digits before
     * and after the decimal point, the exponent, and the unit. All four will be
     * null if the response cannot be parsed.
     */
    protected function parse_response($response) {
        if (!preg_match($this->build_regex(), $response, $matches)) {
            return array(null, null, null, null);
        }

        $matches += array('', '', '', ''); // Fill in any missing matches.
        list($matchedpart, $beforepoint, $decimals, $exponent) = $matches;

        // Strip out thousands separators.
        $beforepoint = str_replace($this->thousandssep, '', $beforepoint);

        // Must be either something before, or something after the decimal point.
        // (The only way to do this in the regex would make it much more complicated.)
        if ($beforepoint === '' && $decimals === '') {
            return array(null, null, null, null);
        }

        if ($this->unitsbefore) {
            $unit = substr($response, 0, -strlen($matchedpart));
        } else {
            $unit = substr($response, strlen($matchedpart));
        }
        $unit = trim($unit);

        return array($beforepoint, $decimals, $exponent, $unit);
    }

    /**
     * Takes a number in almost any localised form, and possibly with a unit
     * after it. It separates off the unit, if present, and converts to the
     * default unit, by using the given unit multiplier.
     *
     * @param string $response a value, optionally with a unit.
     * @return array(numeric, sting) the value with the unit stripped, and normalised
     *      by the unit multiplier, if any, and the unit string, for reference.
     */
    public function apply_units($response, $separateunit = null) {
        // Strip spaces (which may be thousands separators) and change other forms
        // of writing e to e.
        $response = str_replace(' ', '', $response);
        $response = preg_replace('~(?:e|E|(?:x|\*|×)10(?:\^|\*\*))([+-]?\d+)~', 'e$1', $response);

        // If a . is present or there are multiple , (i.e. 2,456,789 ) assume ,
        // is a thouseands separator, and strip it, else assume it is a decimal
        // separator, and change it to ..
        if (strpos($response, '.') !== false || substr_count($response, ',') > 1) {
            $response = str_replace(',', '', $response);
        } else {
            $response = str_replace(',', '.', $response);
        }

        $regex = '[+-]?(?:\d+(?:\\.\d*)?|\\.\d+)(?:e[-+]?\d+)?';
        if ($this->unitsbefore) {
            $regex = "/$regex$/";
        } else {
            $regex = "/^$regex/";
        }
        if (!preg_match($regex, $response, $matches)) {
            return array(null, null, null);
        }

        $numberstring = $matches[0];
        if ($this->unitsbefore) {
            // substr returns false when it means '', so cast back to string.
            $unit = (string) substr($response, 0, -strlen($numberstring));
        } else {
            $unit = (string) substr($response, strlen($numberstring));
        }

        if (!is_null($separateunit)) {
            $unit = $separateunit;
        }

        if ($this->is_known_unit($unit)) {
            $multiplier = 1 / $this->units[$unit];
        } else {
            $multiplier = null;
        }

        return array($numberstring + 0, $unit, $multiplier); // + 0 to convert to number.
    }

    /**
     * @return string the default unit.
     */
    public function get_default_unit() {
        reset($this->units);
        return key($this->units);
    }

    /**
     * @param string $answer a response.
     * @param string $unit a unit.
     */
    public function add_unit($answer, $unit = null) {
        if (is_null($unit)) {
            $unit = $this->get_default_unit();
        }

        if (!$unit) {
            return $answer;
        }

        if ($this->unitsbefore) {
            return $unit . ' ' . $answer;
        } else {
            return $answer . ' ' . $unit;
        }
    }

    /**
     * Is this unit recognised.
     * @param string $unit the unit
     * @return bool whether this is a unit we recognise.
     */
    public function is_known_unit($unit) {
        return array_key_exists($unit, $this->units);
    }

    /**
     * Whether the units go before or after the number.
     * @return true = before, false = after.
     */
    public function are_units_before() {
        return $this->unitsbefore;
    }

    /**
     * Get the units as an array suitably for passing to html_writer::select.
     * @return array of unit choices.
     */
    public function get_unit_options() {
        $options = array();
        foreach ($this->units as $unit => $notused) {
            $options[$unit] = $unit;
        }
        return $options;
    }
}

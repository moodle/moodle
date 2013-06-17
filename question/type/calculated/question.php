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
 * Calculated question definition class.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/question.php');


/**
 * Represents a calculated question.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_question extends qtype_numerical_question
        implements qtype_calculated_question_with_expressions {

    /** @var qtype_calculated_dataset_loader helper for loading the dataset. */
    public $datasetloader;

    /** @var qtype_calculated_variable_substituter stores the dataset we are using. */
    public $vs;

    /**
     * @var bool wheter the dataset item to use should be chose based on attempt
     * start time, rather than randomly.
     */
    public $synchronised;

    public function start_attempt(question_attempt_step $step, $variant) {
        qtype_calculated_question_helper::start_attempt($this, $step, $variant);
        parent::start_attempt($step, $variant);
    }

    public function apply_attempt_state(question_attempt_step $step) {
        qtype_calculated_question_helper::apply_attempt_state($this, $step);
        parent::apply_attempt_state($step);
    }

    public function calculate_all_expressions() {
        $this->questiontext = $this->vs->replace_expressions_in_text($this->questiontext);
        $this->generalfeedback = $this->vs->replace_expressions_in_text($this->generalfeedback);

        foreach ($this->answers as $ans) {
            if ($ans->answer && $ans->answer !== '*') {
                $ans->answer = $this->vs->calculate($ans->answer,
                        $ans->correctanswerlength, $ans->correctanswerformat);
            }
            $ans->feedback = $this->vs->replace_expressions_in_text($ans->feedback,
                        $ans->correctanswerlength, $ans->correctanswerformat);
        }
    }

    public function get_num_variants() {
        return $this->datasetloader->get_number_of_items();
    }

    public function get_variants_selection_seed() {
        if (!empty($this->synchronised) &&
                $this->datasetloader->datasets_are_synchronised($this->category)) {
            return 'category' . $this->category;
        } else {
            return parent::get_variants_selection_seed();
        }
    }

    public function get_correct_response() {
        $answer = $this->get_correct_answer();
        if (!$answer) {
            return array();
        }

        $response = array('answer' => $this->vs->format_float($answer->answer,
            $answer->correctanswerlength, $answer->correctanswerformat));

        if ($this->has_separate_unit_field()) {
            $response['unit'] = $this->ap->get_default_unit();
        } else if ($this->unitdisplay == qtype_numerical::UNITINPUT) {
            $response['answer'] = $this->ap->add_unit($response['answer']);
        }

        return $response;
    }

}


/**
 * This interface defines the method that a quetsion type must implement if it
 * is to work with {@link qtype_calculated_question_helper}.
 *
 * As well as this method, the class that implements this interface must have
 * fields
 * public $datasetloader; // of type qtype_calculated_dataset_loader
 * public $vs; // of type qtype_calculated_variable_substituter
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface qtype_calculated_question_with_expressions {
    /**
     * Replace all the expression in the question definition with the values
     * computed from the selected dataset by calling $this->vs->calculate() and
     * $this->vs->replace_expressions_in_text() on the parts of the question
     * that require it.
     */
    public function calculate_all_expressions();
}


/**
 * Helper class for questions that use datasets. Works with the interface
 * {@link qtype_calculated_question_with_expressions} and the class
 * {@link qtype_calculated_dataset_loader} to set up the value of each variable
 * in start_attempt, and restore that in apply_attempt_state.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_calculated_question_helper {
    public static function start_attempt(
            qtype_calculated_question_with_expressions $question,
            question_attempt_step $step, $variant) {

        $question->vs = new qtype_calculated_variable_substituter(
                $question->datasetloader->get_values($variant),
                get_string('decsep', 'langconfig'));
        $question->calculate_all_expressions();

        foreach ($question->vs->get_values() as $name => $value) {
            $step->set_qt_var('_var_' . $name, $value);
        }
    }

    public static function apply_attempt_state(
            qtype_calculated_question_with_expressions $question, question_attempt_step $step) {
        $values = array();
        foreach ($step->get_qt_data() as $name => $value) {
            if (substr($name, 0, 5) === '_var_') {
                $values[substr($name, 5)] = $value;
            }
        }

        $question->vs = new qtype_calculated_variable_substituter(
                $values, get_string('decsep', 'langconfig'));
        $question->calculate_all_expressions();
    }
}


/**
 * This class is responsible for loading the dataset that a question needs from
 * the database.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_dataset_loader {
    /** @var int the id of the question we are helping. */
    protected $questionid;

    /** @var int the id of the question we are helping. */
    protected $itemsavailable = null;

    /**
     * Constructor
     * @param int $questionid the question to load datasets for.
     */
    public function __construct($questionid) {
        $this->questionid = $questionid;
    }

    /**
     * Get the number of items (different values) in each dataset used by this
     * question. This is the minimum number of items in any dataset used by this
     * question.
     * @return int the number of items available.
     */
    public function get_number_of_items() {
        global $DB;

        if (is_null($this->itemsavailable)) {
            $this->itemsavailable = $DB->get_field_sql('
                    SELECT MIN(qdd.itemcount)
                      FROM {question_dataset_definitions} qdd
                      JOIN {question_datasets} qd ON qdd.id = qd.datasetdefinition
                     WHERE qd.question = ?
                    ', array($this->questionid), MUST_EXIST);
        }

        return $this->itemsavailable;
    }

    /**
     * Actually query the database for the values.
     * @param int $itemnumber which set of values to load.
     * @return array name => value;
     */
    protected function load_values($itemnumber) {
        global $DB;

        return $DB->get_records_sql_menu('
                SELECT qdd.name, qdi.value
                  FROM {question_dataset_items} qdi
                  JOIN {question_dataset_definitions} qdd ON qdd.id = qdi.definition
                  JOIN {question_datasets} qd ON qdd.id = qd.datasetdefinition
                 WHERE qd.question = ?
                   AND qdi.itemnumber = ?
                ', array($this->questionid, $itemnumber));
    }

    /**
     * Load a particular set of values for each dataset used by this question.
     * @param int $itemnumber which set of values to load.
     *      0 < $itemnumber <= {@link get_number_of_items()}.
     * @return array name => value.
     */
    public function get_values($itemnumber) {
        if ($itemnumber <= 0 || $itemnumber > $this->get_number_of_items()) {
            $a = new stdClass();
            $a->id = $this->questionid;
            $a->item = $itemnumber;
            throw new moodle_exception('cannotgetdsfordependent', 'question', '', $a);
        }

        return $this->load_values($itemnumber);
    }

    public function datasets_are_synchronised($category) {
        global $DB;
        // We need to ensure that there are synchronised datasets, and that they
        // all use the right category.
        $categories = $DB->get_record_sql('
                SELECT MAX(qdd.category) AS max,
                       MIN(qdd.category) AS min
                  FROM {question_dataset_definitions} qdd
                  JOIN {question_datasets} qd ON qdd.id = qd.datasetdefinition
                 WHERE qd.question = ?
                   AND qdd.category <> 0
            ', array($this->questionid));

        return $categories && $categories->max == $category && $categories->min == $category;
    }
}


/**
 * This class holds the current values of all the variables used by a calculated
 * question.
 *
 * It can compute formulae using those values, and can substitute equations
 * embedded in text.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_variable_substituter {
    /** @var array variable name => value */
    protected $values;

    /** @var string character to use for the decimal point in displayed numbers. */
    protected $decimalpoint;

    /** @var array variable names wrapped in {...}. Used by {@link substitute_values()}. */
    protected $search;

    /**
     * @var array variable values, with negative numbers wrapped in (...).
     * Used by {@link substitute_values()}.
     */
    protected $safevalue;

    /**
     * @var array variable values, with negative numbers wrapped in (...).
     * Used by {@link substitute_values()}.
     */
    protected $prettyvalue;

    /**
     * Constructor
     * @param array $values variable name => value.
     */
    public function __construct(array $values, $decimalpoint) {
        $this->values = $values;
        $this->decimalpoint = $decimalpoint;

        // Prepare an array for {@link substitute_values()}.
        $this->search = array();
        $this->replace = array();
        foreach ($values as $name => $value) {
            if (!is_numeric($value)) {
                $a = new stdClass();
                $a->name = '{' . $name . '}';
                $a->value = $value;
                throw new moodle_exception('notvalidnumber', 'qtype_calculated', '', $a);
            }

            $this->search[] = '{' . $name . '}';
            $this->safevalue[] = '(' . $value . ')';
            $this->prettyvalue[] = $this->format_float($value);
        }
    }

    /**
     * Display a float properly formatted with a certain number of decimal places.
     * @param number $x the number to format
     * @param int $length restrict to this many decimal places or significant
     *      figures. If null, the number is not rounded.
     * @param int format 1 => decimalformat, 2 => significantfigures.
     * @return string formtted number.
     */
    public function format_float($x, $length = null, $format = null) {
        if (!is_null($length) && !is_null($format)) {
            if ($format == '1' ) { // Answer is to have $length decimals.
                // Decimal places.
                $x = sprintf('%.' . $length . 'F', $x);

            } else if ($x) { // Significant figures does only apply if the result is non-zero.
                $answer = $x;
                // Convert to positive answer.
                if ($answer < 0) {
                    $answer = -$answer;
                    $sign = '-';
                } else {
                    $sign = '';
                }

                // Determine the format 0.[1-9][0-9]* for the answer...
                $p10 = 0;
                while ($answer < 1) {
                    --$p10;
                    $answer *= 10;
                }
                while ($answer >= 1) {
                    ++$p10;
                    $answer /= 10;
                }
                // ... and have the answer rounded of to the correct length.
                $answer = round($answer, $length);

                // If we rounded up to 1.0, place the answer back into 0.[1-9][0-9]* format.
                if ($answer >= 1) {
                    ++$p10;
                    $answer /= 10;
                }

                // Have the answer written on a suitable format.
                // Either scientific or plain numeric.
                if (-2 > $p10 || 4 < $p10) {
                    // Use scientific format.
                    $exponent = 'e'.--$p10;
                    $answer *= 10;
                    if (1 == $length) {
                        $x = $sign.$answer.$exponent;
                    } else {
                        // Attach additional zeros at the end of $answer.
                        $answer .= (1 == strlen($answer) ? '.' : '')
                            . '00000000000000000000000000000000000000000x';
                        $x = $sign
                            .substr($answer, 0, $length +1).$exponent;
                    }
                } else {
                    // Stick to plain numeric format.
                    $answer *= "1e$p10";
                    if (0.1 <= $answer / "1e$length") {
                        $x = $sign.$answer;
                    } else {
                        // Could be an idea to add some zeros here.
                        $answer .= (preg_match('~^[0-9]*$~', $answer) ? '.' : '')
                            . '00000000000000000000000000000000000000000x';
                        $oklen = $length + ($p10 < 1 ? 2-$p10 : 1);
                        $x = $sign.substr($answer, 0, $oklen);
                    }
                }

            } else {
                $x = 0.0;
            }
        }
        return str_replace('.', $this->decimalpoint, $x);
    }

    /**
     * Return an array of the variables and their values.
     * @return array name => value.
     */
    public function get_values() {
        return $this->values;
    }

    /**
     * Evaluate an expression using the variable values.
     * @param string $expression the expression. A PHP expression with placeholders
     *      like {a} for where the variables need to go.
     * @return float the computed result.
     */
    public function calculate($expression) {
        return $this->calculate_raw($this->substitute_values_for_eval($expression));
    }

    /**
     * Evaluate an expression after the variable values have been substituted.
     * @param string $expression the expression. A PHP expression with placeholders
     *      like {a} for where the variables need to go.
     * @return float the computed result.
     */
    protected function calculate_raw($expression) {
        // This validation trick from http://php.net/manual/en/function.eval.php .
        if (!@eval('return true; $result = ' . $expression . ';')) {
            throw new moodle_exception('illegalformulasyntax', 'qtype_calculated', '', $expression);
        }
        return eval('return ' . $expression . ';');
    }

    /**
     * Substitute variable placehodlers like {a} with their value wrapped in ().
     * @param string $expression the expression. A PHP expression with placeholders
     *      like {a} for where the variables need to go.
     * @return string the expression with each placeholder replaced by the
     *      corresponding value.
     */
    protected function substitute_values_for_eval($expression) {
        return str_replace($this->search, $this->safevalue, $expression);
    }

    /**
     * Substitute variable placehodlers like {a} with their value without wrapping
     * the value in anything.
     * @param string $text some content with placeholders
     *      like {a} for where the variables need to go.
     * @return string the expression with each placeholder replaced by the
     *      corresponding value.
     */
    protected function substitute_values_pretty($text) {
        return str_replace($this->search, $this->prettyvalue, $text);
    }

    /**
     * Replace any embedded variables (like {a}) or formulae (like {={a} + {b}})
     * in some text with the corresponding values.
     * @param string $text the text to process.
     * @return string the text with values substituted.
     */
    public function replace_expressions_in_text($text, $length = null, $format = null) {
        $vs = $this; // Can't see to use $this in a PHP closure.
        $text = preg_replace_callback('~\{=([^{}]*(?:\{[^{}]+}[^{}]*)*)}~',
                function ($matches) use ($vs, $format, $length) {
                    return $vs->format_float($vs->calculate($matches[1]), $length, $format);
                }, $text);
        return $this->substitute_values_pretty($text);
    }

    /**
     * Return an array describing any problems there are with an expression.
     * Returns false if the expression is fine.
     * @param string $formula an expression.
     * @return array|false list of problems, or false if the exression is OK.
     */
    public function get_formula_errors($formula) {
        // Validates the formula submitted from the question edit page.
        // Returns false if everything is alright
        // otherwise it constructs an error message.
        // Strip away dataset names.
        while (preg_match('~\\{[[:alpha:]][^>} <{"\']*\\}~', $formula, $regs)) {
            $formula = str_replace($regs[0], '1', $formula);
        }

        // Strip away empty space and lowercase it.
        $formula = strtolower(str_replace(' ', '', $formula));

        $safeoperatorchar = '-+/*%>:^\~<?=&|!'; /* */
        $operatorornumber = "[$safeoperatorchar.0-9eE]";

        while (preg_match("~(^|[$safeoperatorchar,(])([a-z0-9_]*)" .
                "\\(($operatorornumber+(,$operatorornumber+((,$operatorornumber+)+)?)?)?\\)~",
            $formula, $regs)) {
            switch ($regs[2]) {
                // Simple parenthesis.
                case '':
                    if ((isset($regs[4]) && $regs[4]) || strlen($regs[3]) == 0) {
                        return get_string('illegalformulasyntax', 'qtype_calculated', $regs[0]);
                    }
                    break;

                    // Zero argument functions.
                case 'pi':
                    if ($regs[3]) {
                        return get_string('functiontakesnoargs', 'qtype_calculated', $regs[2]);
                    }
                    break;

                    // Single argument functions (the most common case).
                case 'abs': case 'acos': case 'acosh': case 'asin': case 'asinh':
                case 'atan': case 'atanh': case 'bindec': case 'ceil': case 'cos':
                case 'cosh': case 'decbin': case 'decoct': case 'deg2rad':
                case 'exp': case 'expm1': case 'floor': case 'is_finite':
                case 'is_infinite': case 'is_nan': case 'log10': case 'log1p':
                case 'octdec': case 'rad2deg': case 'sin': case 'sinh': case 'sqrt':
                case 'tan': case 'tanh':
                    if (!empty($regs[4]) || empty($regs[3])) {
                        return get_string('functiontakesonearg', 'qtype_calculated', $regs[2]);
                    }
                    break;

                    // Functions that take one or two arguments.
                case 'log': case 'round':
                    if (!empty($regs[5]) || empty($regs[3])) {
                        return get_string('functiontakesoneortwoargs', 'qtype_calculated',
                                $regs[2]);
                    }
                    break;

                    // Functions that must have two arguments.
                case 'atan2': case 'fmod': case 'pow':
                    if (!empty($regs[5]) || empty($regs[4])) {
                        return get_string('functiontakestwoargs', 'qtype_calculated', $regs[2]);
                    }
                    break;

                    // Functions that take two or more arguments.
                case 'min': case 'max':
                    if (empty($regs[4])) {
                        return get_string('functiontakesatleasttwo', 'qtype_calculated', $regs[2]);
                    }
                    break;

                default:
                    return get_string('unsupportedformulafunction', 'qtype_calculated', $regs[2]);
            }

            // Exchange the function call with '1' and then check for another function call.

            if ($regs[1]) {
                // The function call is proceeded by an operator.
                $formula = str_replace($regs[0], $regs[1] . '1', $formula);
            } else {
                // The function call starts the formula.
                $formula = preg_replace("~^$regs[2]\\([^)]*\\)~", '1', $formula);
            }
        }

        if (preg_match("~[^$safeoperatorchar.0-9eE]+~", $formula, $regs)) {
            return get_string('illegalformulasyntax', 'qtype_calculated', $regs[0]);
        } else {
            // Formula just might be valid.
            return false;
        }
    }
}
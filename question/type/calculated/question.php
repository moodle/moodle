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
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/question.php');


/**
 * Represents a calculated question.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_question extends qtype_numerical_question {
    /** @var qtype_calculated_dataset_loader helper for loading the dataset. */
    protected $datasetloader;
    /** @var qtype_calculated_variable_substituter stores the dataset we are using. */
    protected $vs;

    public function start_attempt(question_attempt_step $step) {
        $maxnumber = $this->datasetloader->get_number_of_datasets();
        $setnumber = rand(1, $maxnumber);
        // TODO implement the $synchronizecalculated bit from create_session_and_responses.

        $this->vs = $this->datasetloader->load_dataset($setnumber);

        $step->set_qt_var('_dataset', $setnumber);
        foreach ($this->vs->get_values() as $name => $value) {
            $step->set_qt_var('_var_' . $name, $value);
        }

        $this->calculate_all_expressions();

        parent::start_attempt($step);
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $values = array();
        foreach ($step->get_qt_datavalues() as $name => $value) {
            if (substr($name, 0, 5) === '_var_') {
                $values[substr($name, 5)] = $value;
            }
        }
        $this->vs = new qtype_calculated_variable_substituter($values);

        $this->calculate_all_expressions();

        parent::apply_attempt_state($step);
    }

    /**
     * Replace all the expression in the question definition with the values
     * computed from the selected dataset.
     */
    protected function calculate_all_expressions() {
        $this->questiontext = $this->vs->replace_expressions_in_text($this->questiontext);
        $this->generalfeedback = $this->vs->replace_expressions_in_text($this->generalfeedback);
        // TODO etc.

        foreach ($this->answers as $ans) {
            if ($ans->answer && $ans->answer !== '*') {
                $ans->answer = $this->vs->calculate($ans->answer);
            }
            $ans->feedback = $this->vs->replace_expressions_in_text($ans->feedback);
        }
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
     * Load a particular set of values for each dataset used by this question.
     * @param int $itemnumber which set of values to load.
     *      0 < $itemnumber <= {@link get_number_of_items()}.
     * @return qtype_calculated_variable_substituter with the correct variable
     *      -> value substitutions set up.
     */
    public function load_values($itemnumber) {
        if ($itemnumber <= 0 || $itemnumber > $this->get_number_of_items()) {
            $a = new stdClass();
            $a->id = $this->questionid;
            $a->item = $itemnumber;
            throw new moodle_exception('cannotgetdsfordependent', 'question', '', $a);
        }

        $values = $DB->get_records_sql('
                SELECT qdd.name, qdi.value
                  FROM {question_dataset_items} qdi
                  JOIN {question_dataset_definitions} qdd ON qdd.id = qdi.definition
                  JOIN {question_datasets} qd ON qdd.id = qd.datasetdefinition
                 WHERE qd.question = ?
                   AND qdi.itemnumber = ?
                ', array($this->questionid, $itemnumber));

        return new qtype_calculated_variable_substituter($values);
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

    /** @var array variable names wrapped in {...}. Used by {@link substitute_values()}. */
    protected $search;

    /**
     * @var array variable values, with negative numbers wrapped in (...).
     * Used by {@link substitute_values()}.
     */
    protected $replace;

    /**
     * Constructor
     * @param array $values variable name => value.
     */
    public function __construct(array $values) {
        $this->values = $values;

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
            if ($value < 0) {
                $this->replace[] = '(' . $value . ')';
            } else {
                $this->replace[] = $value;
            }
        }
    }

    /**
     * Return an array of the variables and their values.
     * @return array name => value. 
     */
    public function get_values() {
        return clone($this->values);
    }

    /**
     * Evaluate an expression using the variable values.
     * @param string $expression the expression. A PHP expression with placeholders
     *      like {a} for where the variables need to go.
     * @return float the computed result.
     */
    public function calculate($expression) {
        $exp = $this->substitute_values($expression);
        // This validation trick from http://php.net/manual/en/function.eval.php
        if (!@eval('return true; $result = ' . $exp . ';')) {
            throw new moodle_exception('illegalformulasyntax', 'qtype_calculated', '', $expression);
        }
        return eval('return ' . $exp . ';');
    }

    /**
     * Substitute variable placehodlers like {a} with their value.
     * @param string $expression the expression. A PHP expression with placeholders
     *      like {a} for where the variables need to go.
     * @return string the expression with each placeholder replaced by the
     *      corresponding value.
     */
    protected function substitute_values($expression) {
        return str_replace($this->search, $this->replace, $expression);
    }

    public function replace_expressions_in_text($text) {
        // TODO
        return $text;
    }

    /**
     * Return an array describing any problems there are with an expression.
     * Returns false if the expression is fine.
     * @param string $formula an expression.
     * @return array|false list of problems, or false if the exression is OK.
     */
    public function get_formula_errors($formula) {
        // Validates the formula submitted from the question edit page.
        // Returns false if everything is alright.
        // Otherwise it constructs an error message
        // Strip away dataset names
        while (preg_match('~\\{[[:alpha:]][^>} <{"\']*\\}~', $formula, $regs)) {
            $formula = str_replace($regs[0], '1', $formula);
        }
    
        // Strip away empty space and lowercase it
        $formula = strtolower(str_replace(' ', '', $formula));
    
        $safeoperatorchar = '-+/*%>:^\~<?=&|!'; /* */
        $operatorornumber = "[$safeoperatorchar.0-9eE]";
    
        while (preg_match("~(^|[$safeoperatorchar,(])([a-z0-9_]*)" .
                "\\(($operatorornumber+(,$operatorornumber+((,$operatorornumber+)+)?)?)?\\)~",
            $formula, $regs)) {
            switch ($regs[2]) {
                // Simple parenthesis
                case '':
                    if ((isset($regs[4]) && $regs[4]) || strlen($regs[3]) == 0) {
                        return get_string('illegalformulasyntax', 'qtype_calculated', $regs[0]);
                    }
                    break;
    
                    // Zero argument functions
                case 'pi':
                    if ($regs[3]) {
                        return get_string('functiontakesnoargs', 'qtype_calculated', $regs[2]);
                    }
                    break;
    
                    // Single argument functions (the most common case)
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
    
                    // Functions that take one or two arguments
                case 'log': case 'round':
                    if (!empty($regs[5]) || empty($regs[3])) {
                        return get_string('functiontakesoneortwoargs', 'qtype_calculated', $regs[2]);
                    }
                    break;
    
                    // Functions that must have two arguments
                case 'atan2': case 'fmod': case 'pow':
                    if (!empty($regs[5]) || empty($regs[4])) {
                        return get_string('functiontakestwoargs', 'qtype_calculated', $regs[2]);
                    }
                    break;
    
                    // Functions that take two or more arguments
                case 'min': case 'max':
                    if (empty($regs[4])) {
                        return get_string('functiontakesatleasttwo', 'qtype_calculated', $regs[2]);
                    }
                    break;
    
                default:
                    return get_string('unsupportedformulafunction', 'qtype_calculated', $regs[2]);
            }
    
            // Exchange the function call with '1' and then chack for
            // another function call...
            if ($regs[1]) {
                // The function call is proceeded by an operator
                $formula = str_replace($regs[0], $regs[1] . '1', $formula);
            } else {
                // The function call starts the formula
                $formula = preg_replace("~^$regs[2]\\([^)]*\\)~", '1', $formula);
            }
        }
    
        if (preg_match("~[^$safeoperatorchar.0-9eE]+~", $formula, $regs)) {
            return get_string('illegalformulasyntax', 'qtype_calculated', $regs[0]);
        } else {
            // Formula just might be valid
            return false;
        }
    }
}
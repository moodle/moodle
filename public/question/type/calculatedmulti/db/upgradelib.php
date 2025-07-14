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
 * Class for converting attempt data for calculated multiple-choice questions
 * when upgrading attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @package    qtype_calculatedmulti
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedmulti_qe2_attempt_updater extends question_qtype_attempt_updater {
    protected $selecteditem = null;
    /** @var array variable name => value */
    protected $values = [];

    /** @var array variable names wrapped in {...}. Used by {@link substitute_values()}. */
    protected $search = [];

    /**
     * @var array variable values, with negative numbers wrapped in (...).
     * Used by {@link substitute_values()}.
     */
    protected $safevalue = [];

    /**
     * @var array variable values, with negative numbers wrapped in (...).
     * Used by {@link substitute_values()}.
     */
    protected $prettyvalue = [];

    protected $order;

    public function question_summary() {
        return ''; // Done later, after we know which dataset is used.
    }

    public function right_answer() {
        if ($this->question->options->single) {
            foreach ($this->question->options->answers as $ans) {
                if ($ans->fraction > 0.999) {
                    return $this->to_text($this->replace_expressions_in_text($ans->answer));
                }
            }
        } else {
            $rightbits = [];
            foreach ($this->question->options->answers as $ans) {
                if ($ans->fraction >= 0.000001) {
                    $rightbits[] = $this->to_text($this->replace_expressions_in_text($ans->answer));
                }
            }
            return implode('; ', $rightbits);
        }
    }

    protected function explode_answer($state) {
        if (strpos($state->answer, '-') < 7) {
            // Broken state, skip it.
            throw new coding_exception("Brokes state {$state->id} for calcluatedmulti
                    question {$state->question}. (It did not specify a dataset.");
        }
        [$datasetbit, $answer] = explode('-', $state->answer, 2);
        $selecteditem = substr($datasetbit, 7);

        if (is_null($this->selecteditem)) {
            $this->load_dataset($selecteditem);
        } else if ($this->selecteditem != $selecteditem) {
            $this->logger->log_assumption("Different states for calcluatedmulti question
                    {$state->question} used different dataset items. Ignoring the change
                    in state {$state->id} and coninuting to use item {$this->selecteditem}.");
        }

        if (strpos($answer, ':') !== false) {
            [$order, $responses] = explode(':', $answer);
            return $responses;
        } else {
            // Sometimes, a bug means that a state is missing the <order>: bit,
            // We need to deal with that.
            $this->logger->log_assumption("Dealing with missing order information
                    in attempt at multiple choice question {$this->question->id}");
            return $answer;
        }
    }

    public function response_summary($state) {
        $responses = $this->explode_answer($state);
        if ($this->question->options->single) {
            if (is_numeric($responses)) {
                if (array_key_exists($responses, $this->question->options->answers)) {
                    return $this->to_text($this->replace_expressions_in_text(
                        $this->question->options->answers[$responses]->answer
                    ));
                } else {
                    $this->logger->log_assumption("Dealing with a place where the
                            student selected a choice that was later deleted for
                            multiple choice question {$this->question->id}");
                    return '[CHOICE THAT WAS LATER DELETED]';
                }
            } else {
                return null;
            }
        } else {
            if (!empty($responses)) {
                $responses = explode(',', $responses);
                $bits = [];
                foreach ($responses as $response) {
                    if (array_key_exists($response, $this->question->options->answers)) {
                        $bits[] = $this->to_text($this->replace_expressions_in_text(
                            $this->question->options->answers[$response]->answer
                        ));
                    } else {
                        $this->logger->log_assumption("Dealing with a place where the
                                student selected a choice that was later deleted for
                                multiple choice question {$this->question->id}");
                        $bits[] = '[CHOICE THAT WAS LATER DELETED]';
                    }
                }
                return implode('; ', $bits);
            } else {
                return null;
            }
        }
    }

    public function was_answered($state) {
        $responses = $this->explode_answer($state);
        if ($this->question->options->single) {
            return is_numeric($responses);
        } else {
            return !empty($responses);
        }
    }

    public function set_first_step_data_elements($state, &$data) {
        $this->explode_answer($state);
        $this->updater->qa->questionsummary = $this->to_text(
            $this->replace_expressions_in_text($this->question->questiontext)
        );
        $this->updater->qa->rightanswer = $this->right_answer($this->question);

        foreach ($this->values as $name => $value) {
            $data['_var_' . $name] = $value;
        }

        [$datasetbit, $answer] = explode('-', $state->answer, 2);
        [$order, $responses] = explode(':', $answer);
        $data['_order'] = $order;
        $this->order = explode(',', $order);
    }

    public function supply_missing_first_step_data(&$data) {
        $data['_order'] = implode(',', array_keys($this->question->options->answers));
    }

    public function set_data_elements_for_step($state, &$data) {
        $responses = $this->explode_answer($state);
        if ($this->question->options->single) {
            if (is_numeric($responses)) {
                $flippedorder = array_combine(array_values($this->order), array_keys($this->order));
                if (array_key_exists($responses, $flippedorder)) {
                    $data['answer'] = $flippedorder[$responses];
                } else {
                    $data['answer'] = '-1';
                }
            }
        } else {
            $responses = explode(',', $responses);
            foreach ($this->order as $key => $ansid) {
                if (in_array($ansid, $responses)) {
                    $data['choice' . $key] = 1;
                } else {
                    $data['choice' . $key] = 0;
                }
            }
        }
    }

    public function load_dataset($selecteditem) {
        $this->selecteditem = $selecteditem;
        $this->updater->qa->variant = $selecteditem;
        $this->values = $this->qeupdater->load_dataset(
            $this->question->id,
            $selecteditem
        );

        // Prepare an array for {@link substitute_values()}.
        $this->search = [];
        $this->safevalue = [];
        $this->prettyvalue = [];
        foreach ($this->values as $name => $value) {
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
     * This function should be identical to
     * {@link qtype_calculated_variable_substituter::format_float()}. Except that
     * we do not try to do locale-aware replacement of the decimal point.
     *
     * Having to copy it here is a pain, but it is the standard rule about not
     * using library code (which may change in future) in upgrade code, which
     * exists at a point in time.
     *
     * Display a float properly formatted with a certain number of decimal places.
     * @param number $x the number to format
     * @param int $length restrict to this many decimal places or significant
     *      figures. If null, the number is not rounded.
     * @param int format 1 => decimalformat, 2 => significantfigures.
     * @return string formtted number.
     */
    public function format_float($x, $length = null, $format = null) {
        if (!is_null($length) && !is_null($format)) {
            if ($format == 1) {
                // Decimal places.
                $x = sprintf('%.' . $length . 'F', $x);
            } else if ($format == 2) {
                // Significant figures.
                $x = sprintf('%.' . $length . 'g', $x);
            }
        }
        return $x;
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
        try {
            // In older PHP versions this this is a way to validate code passed to eval.
            // The trick came from http://php.net/manual/en/function.eval.php.
            if (@eval('return true; $result = ' . $expression . ';')) {
                return eval('return ' . $expression . ';');
            }
        } catch (Throwable $e) {
            // PHP7 and later now throws ParseException and friends from eval(),
            // which is much better.
        }
        // In either case of an invalid $expression, we end here.
        return '[Invalid expression ' . $expression . ']';
    }

    /**
     * Substitute variable placehodlers like {a} with their value wrapped in ().
     * @param string $expression the expression. A PHP expression with placeholders
     *      like {a} for where the variables need to go.
     * @return string the expression with each placeholder replaced by the
     *      corresponding value.
     */
    protected function substitute_values_for_eval($expression) {
        return str_replace($this->search, $this->safevalue, $expression ?? '');
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
        return str_replace($this->search, $this->prettyvalue, $text ?? '');
    }

    /**
     * Replace any embedded variables (like {a}) or formulae (like {={a} + {b}})
     * in some text with the corresponding values.
     * @param string $text the text to process.
     * @return string the text with values substituted.
     */
    public function replace_expressions_in_text($text, $length = null, $format = null) {
        if ($text === null || $text === '') {
            return $text;
        }

        $vs = $this; // Can't see to use $this in a PHP closure.
        $text = preg_replace_callback(
            qtype_calculated::FORMULAS_IN_TEXT_REGEX,
            function ($matches) use ($vs, $format, $length) {
                return $vs->format_float($vs->calculate($matches[1]), $length, $format);
            },
            $text
        );
        return $this->substitute_values_pretty($text);
    }
}

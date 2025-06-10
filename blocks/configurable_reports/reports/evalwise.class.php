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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class EvalWise
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class EvalWise extends EvalMath {

    /**
     * @var array
     */
    public array $data = [];

    /**
     * @var int
     */
    public int $index = 0;

    /**
     * Set data
     *
     * @param array $data
     * @return void
     */
    public function set_data($data): void {
        $this->data = $data;
        $this->index = count($this->data);
    }

    /**
     * pfx
     *
     * @param array|bool $tokens
     * @param array $vars
     * @return array|false|mixed|null
     */
    public function pfx($tokens, array $vars = []) {

        if ($tokens === false) {
            return false;
        }

        $stack = new EvalMathStack;

        foreach ($tokens as $token) {

            // If the token is a function, pop arguments off the stack, hand them to the function, and push the result back on.
            if (is_array($token)) {

                // It's a function!
                $fnn = $token['fnn'];
                $count = $token['argcount'];

                if (in_array($fnn, $this->fb)) { // Built-in function.
                    if (is_null($op1 = $stack->pop())) {
                        return $this->trigger("internal error");
                    }
                    $fnn = preg_replace("/^arc/", "a", $fnn); // For the 'arc' trig synonyms.
                    if ($fnn === 'ln') {
                        $fnn = 'log';
                    }

                    // TODO Use the PHP internal function if possible.
                    // @codingStandardsIgnoreStart
                    /*
                        evaluate postfix notation
                        modified to perform bitwise-like operations in arrays
                        * => & => array_intersect
                        + => | => array_merge
                        - => ^ => array_diff
                     */
                    eval('$stack->push(' . $fnn . '($op1));'); // Perfectly safe eval().
                    // @codingStandardsIgnoreEnd

                } else if (array_key_exists($fnn, $this->fc)) { // Calc emulation function.
                    // Get args.
                    $args = [];
                    for ($i = $count - 1; $i >= 0; $i--) {
                        if (is_null($args[] = $stack->pop())) {
                            return $this->trigger('internal error');
                        }
                    }
                    $res = call_user_func(['EvalMathCalcEmul', $fnn], $args);
                    if ($res === false) {
                        return $this->trigger("internal error");
                    }
                    $stack->push($res);
                } else if (array_key_exists($fnn, $this->f)) {
                    // User function.
                    // Get args.
                    $args = [];
                    for ($i = count($this->f[$fnn]['args']) - 1; $i >= 0; $i--) {
                        if (is_null($args[$this->f[$fnn]['args'][$i]] = $stack->pop())) {
                            return $this->trigger('internal error');
                        }
                    }

                    // Yay recursion!
                    $stack->push($this->pfx($this->f[$fnn]['func'], $args));
                }
            } else if (in_array($token, ['+', '-', '*', '/', '^'], true)) {
                // If the token is a binary operator, pop two values off the stack, do the operation, and push the result back on.
                if (is_null($op2 = $stack->pop())) {
                    return $this->trigger('internal error');
                }
                if (is_null($op1 = $stack->pop())) {
                    return $this->trigger('internal error');
                }

                switch ($token) {
                    case '+':
                        $this->index += 1;
                        $stack->push($this->index);
                        $this->data[$this->index] = array_merge($this->data[$op1], $this->data[$op2]);
                        break;
                    case '-':
                        $this->index += 1;
                        $stack->push($this->index);
                        $this->data[$this->index] = array_diff($this->data[$op1], $this->data[$op2]);
                        break;
                    case '*':
                        $this->index += 1;
                        $stack->push($this->index);
                        $this->data[$this->index] = array_intersect($this->data[$op1], $this->data[$op2]);
                        break;
                }

            } else if ($token === "_") {
                // If the token is a unary operator, pop one value off the stack, do the operation, and push it back on.
                $stack->push(-1 * $stack->pop());
            } else {
                // If the token is a number or variable, push it on the stack.
                if (is_numeric($token)) {
                    $stack->push($token);
                } else if (array_key_exists($token, $this->v)) {
                    $stack->push($this->v[$token]);
                } else if (array_key_exists($token, $vars)) {
                    $stack->push($vars[$token]);
                } else {
                    return $this->trigger("undefined variable '$token'");
                }
            }
        }
        // When we're out of tokens, the stack should have a single element, the final result.
        if ($stack->count != 1) {
            return $this->trigger("internal error");
        }

        $last = $stack->pop();

        return $this->data[$last] ?? false;
    }

}

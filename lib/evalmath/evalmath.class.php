<?php

/*
================================================================================

EvalMath - PHP Class to safely evaluate math expressions
Copyright (C) 2005 Miles Kaufmann <http://www.twmagic.com/>

================================================================================

NAME
    EvalMath - safely evaluate math expressions
    
SYNOPSIS
    <?
      include('evalmath.class.php');
      $m = new EvalMath;
      // basic evaluation:
      $result = $m->evaluate('2+2');
      // supports: order of operation; parentheses; negation; built-in functions
      $result = $m->evaluate('-8(5/2)^2*(1-sqrt(4))-8');
      // create your own variables
      $m->evaluate('a = e^(ln(pi))');
      // or functions
      $m->evaluate('f(x,y) = x^2 + y^2 - 2x*y + 1');
      // and then use them
      $result = $m->evaluate('3*f(42,a)');
    ?>
      
DESCRIPTION
    Use the EvalMath class when you want to evaluate mathematical expressions 
    from untrusted sources.  You can define your own variables and functions,
    which are stored in the object.  Try it, it's fun!

METHODS
    $m->evalute($expr)
        Evaluates the expression and returns the result.  If an error occurs,
        prints a warning and returns false.  If $expr is a function assignment,
        returns true on success.
    
    $m->e($expr)
        A synonym for $m->evaluate().
    
    $m->vars()
        Returns an associative array of all user-defined variables and values.
        
    $m->funcs()
        Returns an array of all user-defined functions.

PARAMETERS
    $m->suppress_errors
        Set to true to turn off warnings when evaluating expressions

    $m->last_error
        If the last evaluation failed, contains a string describing the error.
        (Useful when suppress_errors is on).

AUTHOR INFORMATION
    Copyright 2005, Miles Kaufmann.

LICENSE
    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are
    met:
    
    1   Redistributions of source code must retain the above copyright
        notice, this list of conditions and the following disclaimer.
    2.  Redistributions in binary form must reproduce the above copyright
        notice, this list of conditions and the following disclaimer in the
        documentation and/or other materials provided with the distribution.
    3.  The name of the author may not be used to endorse or promote
        products derived from this software without specific prior written
        permission.
    
    THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
    IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
    INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
    SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
    HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
    STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
    ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
    POSSIBILITY OF SUCH DAMAGE.

*/

/**
 * This class was heavily modified in order to get usefull spreadsheet emulation ;-)
 * skodak
 * 
 */

class EvalMath {

    var $suppress_errors = false;
    var $last_error = null;
    
    var $v = array(); // variables (and constants)
    var $f = array(); // user-defined functions
    var $vb = array(); // constants
    var $fb = array(  // built-in functions
        'sin','sinh','arcsin','asin','arcsinh','asinh',
        'cos','cosh','arccos','acos','arccosh','acosh',
        'tan','tanh','arctan','atan','arctanh','atanh',
        'sqrt','abs','ln','log','exp');

    var $fc = array( // calc functions emulation
        'average'=>array(-1), 'max'=>array(-1),  'min'=>array(-1),
        'mod'=>array(2),      'pi'=>array(0),    'power'=>array(2),
        'round'=>array(1, 2), 'sum'=>array(-1));
    
    function EvalMath() {
    }
    
    function e($expr) {
        return $this->evaluate($expr);
    }
    
    function evaluate($expr) {
        $this->last_error = null;
        $expr = trim($expr);
        if (substr($expr, -1, 1) == ';') $expr = substr($expr, 0, strlen($expr)-1); // strip semicolons at the end
        //===============
        // is it a variable assignment?
        if (preg_match('/^\s*([a-z][a-z0-9]*)\s*=\s*(.+)$/', $expr, $matches)) {
            if (in_array($matches[1], $this->vb)) { // make sure we're not assigning to a constant
                return $this->trigger("cannot assign to constant '$matches[1]'");
            }
            if (($tmp = $this->pfx($this->nfx($matches[2]))) === false) return false; // get the result and make sure it's good
            $this->v[$matches[1]] = $tmp; // if so, stick it in the variable array
            return $this->v[$matches[1]]; // and return the resulting value
        //===============
        // is it a function assignment?
        } elseif (preg_match('/^\s*([a-z][a-z0-9]*)\s*\(\s*([a-z][a-z0-9]*(?:\s*,\s*[a-z][a-z0-9]*)*)\s*\)\s*=\s*(.+)$/', $expr, $matches)) {
            $fnn = $matches[1]; // get the function name
            if (in_array($matches[1], $this->fb)) { // make sure it isn't built in
                return $this->trigger("cannot redefine built-in function '$matches[1]()'");
            }
            $args = explode(",", preg_replace("/\s+/", "", $matches[2])); // get the arguments
            if (($stack = $this->nfx($matches[3])) === false) return false; // see if it can be converted to postfix
            for ($i = 0; $i<count($stack); $i++) { // freeze the state of the non-argument variables
                $token = $stack[$i];
                if (preg_match('/^[a-z][a-z0-9]*$/', $token) and !in_array($token, $args)) {
                    if (array_key_exists($token, $this->v)) {
                        $stack[$i] = $this->v[$token];
                    } else {
                        return $this->trigger("undefined variable '$token' in function definition");
                    }
                }
            }
            $this->f[$fnn] = array('args'=>$args, 'func'=>$stack);
            return true;
        //===============
        } else {
            return $this->pfx($this->nfx($expr)); // straight up evaluation, woo
        }
    }
    
    function vars() {
        return $this->v;
    }
    
    function funcs() {
        $output = array();
        foreach ($this->f as $fnn=>$dat)
            $output[] = $fnn . '(' . implode(',', $dat['args']) . ')';
        return $output;
    }

    //===================== HERE BE INTERNAL METHODS ====================\\

    // Convert infix to postfix notation
    function nfx($expr) {
    
        $index = 0;
        $stack = new EvalMathStack;
        $output = array(); // postfix form of expression, to be passed to pfx()
        $expr = trim(strtolower($expr));
        
        $ops   = array('+', '-', '*', '/', '^', '_');
        $ops_r = array('+'=>0,'-'=>0,'*'=>0,'/'=>0,'^'=>1); // right-associative operator?  
        $ops_p = array('+'=>0,'-'=>0,'*'=>1,'/'=>1,'_'=>1,'^'=>2); // operator precedence
        
        $expecting_op = false; // we use this in syntax-checking the expression
                               // and determining when a - is a negation
    
        if (preg_match("/[^\w\s+*^\/()\.,-]/", $expr, $matches)) { // make sure the characters are all good
            return $this->trigger("illegal character '{$matches[0]}'");
        }
    
        while(1) { // 1 Infinite Loop ;)
            $op = substr($expr, $index, 1); // get the first character at the current index
            // find out if we're currently at the beginning of a number/variable/function/parenthesis/operand
            $ex = preg_match('/^([a-z][a-z0-9]*\(?|\d+(?:\.\d*)?|\.\d+|\()/', substr($expr, $index), $match);
            //===============
            if ($op == '-' and !$expecting_op) { // is it a negation instead of a minus?
                $stack->push('_'); // put a negation on the stack
                $index++;
            } elseif ($op == '_') { // we have to explicitly deny this, because it's legal on the stack 
                return $this->trigger("illegal character '_'"); // but not in the input expression
            //===============
            } elseif ((in_array($op, $ops) or $ex) and $expecting_op) { // are we putting an operator on the stack?
                if ($ex) { // are we expecting an operator but have a number/variable/function/opening parethesis?
                    return $this->trigger("expecting operand");
                    //$op = '*'; $index--; // it's an implicit multiplication
                }
                // heart of the algorithm:
                while($stack->count > 0 and ($o2 = $stack->last()) and in_array($o2, $ops) and ($ops_r[$op] ? $ops_p[$op] < $ops_p[$o2] : $ops_p[$op] <= $ops_p[$o2])) {
                    $output[] = $stack->pop(); // pop stuff off the stack into the output
                }
                // many thanks: http://en.wikipedia.org/wiki/Reverse_Polish_notation#The_algorithm_in_detail
                $stack->push($op); // finally put OUR operator onto the stack
                $index++;
                $expecting_op = false;
            //===============
            } elseif ($op == ')' and $expecting_op) { // ready to close a parenthesis?
                while (($o2 = $stack->pop()) != '(') { // pop off the stack back to the last (
                    if (is_null($o2)) return $this->trigger("unexpected ')'");
                    else $output[] = $o2;
                }
                if (preg_match("/^([a-z][a-z0-9]*)\($/", $stack->last(2), $matches)) { // did we just close a function?
                    $fnn = $matches[1]; // get the function name
                    $arg_count = $stack->pop(); // see how many arguments there were (cleverly stored on the stack, thank you)
                    $fn = $stack->pop();
                    $output[] = array('fn'=>$fn, 'fnn'=>$fnn, 'argcount'=>$arg_count); // send function to output
                    if (in_array($fnn, $this->fb)) { // check the argument count
                        if($arg_count > 1)
                            return $this->trigger("too many arguments ($arg_count given, 1 expected)");
                    } elseif (array_key_exists($fnn, $this->fc)) {
                        $counts = $this->fc[$fnn];
                        if (in_array(-1, $counts) and $arg_count > 0) {}
                        elseif (!in_array($arg_count, $counts))
                            return $this->trigger("wrong number of arguments ($arg_count given, " . implode('/',$this->fc[$fnn]) . " expected)");
                    } elseif (array_key_exists($fnn, $this->f)) {
                        if ($arg_count != count($this->f[$fnn]['args']))
                            return $this->trigger("wrong number of arguments ($arg_count given, " . count($this->f[$fnn]['args']) . " expected)");
                    } else { // did we somehow push a non-function on the stack? this should never happen
                        return $this->trigger("internal error");
                    }
                }
                $index++;
            //===============
            } elseif ($op == ',' and $expecting_op) { // did we just finish a function argument?
                while (($o2 = $stack->pop()) != '(') { 
                    if (is_null($o2)) return $this->trigger("unexpected ','"); // oops, never had a (
                    else $output[] = $o2; // pop the argument expression stuff and push onto the output
                }
                // make sure there was a function
                if (!preg_match("/^([a-z][a-z0-9]*)\($/", $stack->last(2), $matches))
                    return $this->trigger("unexpected ','");
                $stack->push($stack->pop()+1); // increment the argument count
                $stack->push('('); // put the ( back on, we'll need to pop back to it again
                $index++;
                $expecting_op = false;
            //===============
            } elseif ($op == '(' and !$expecting_op) {
                $stack->push('('); // that was easy
                $index++;
                $allow_neg = true;
            //===============
            } elseif ($ex and !$expecting_op) { // do we now have a function/variable/number?
                $expecting_op = true;
                $val = $match[1];
                if (preg_match("/^([a-z][a-z0-9]*)\($/", $val, $matches)) { // may be func, or variable w/ implicit multiplication against parentheses...
                    if (in_array($matches[1], $this->fb) or array_key_exists($matches[1], $this->f) or array_key_exists($matches[1], $this->fc)) { // it's a func
                        $stack->push($val);
                        $stack->push(1);
                        $stack->push('(');
                        $expecting_op = false;
                    } else { // it's a var w/ implicit multiplication
                        $val = $matches[1];
                        $output[] = $val;
                    }
                } else { // it's a plain old var or num
                    $output[] = $val;
                }
                $index += strlen($val);
            //===============
            } elseif ($op == ')') {
                //it could be only custom function with no params or general error
                if ($stack->last() != '(' or $stack->last(2) != 1) return $this->trigger("unexpected ')'");
                if (preg_match("/^([a-z][a-z0-9]*)\($/", $stack->last(3), $matches)) { // did we just close a function?
                    $stack->pop();// (
                    $stack->pop();// 1
                    $fn = $stack->pop();
                    $fnn = $matches[1]; // get the function name
                    $counts = $this->fc[$fnn];
                    if (!in_array(0, $counts))
                        return $this->trigger("wrong number of arguments ($arg_count given, " . implode('/',$this->fc[$fnn]) . " expected)");
                    $output[] = array('fn'=>$fn, 'fnn'=>$fnn, 'argcount'=>0); // send function to output
                    $index++;
                } else {
                    return $this->trigger("unexpected ')'");
                }
            //===============
            } elseif (in_array($op, $ops) and !$expecting_op) { // miscellaneous error checking
                return $this->trigger("unexpected operator '$op'");
            } else { // I don't even want to know what you did to get here
                return $this->trigger("an unexpected error occured");
            }
            if ($index == strlen($expr)) {
                if (in_array($op, $ops)) { // did we end with an operator? bad.
                    return $this->trigger("operator '$op' lacks operand");
                } else {
                    break;
                }
            }
            while (substr($expr, $index, 1) == ' ') { // step the index past whitespace (pretty much turns whitespace 
                $index++;                             // into implicit multiplication if no operator is there)
            }
        
        } 
        while (!is_null($op = $stack->pop())) { // pop everything off the stack and push onto output
            if ($op == '(') return $this->trigger("expecting ')'"); // if there are (s on the stack, ()s were unbalanced
            $output[] = $op;
        }
        return $output;
    }

    // evaluate postfix notation
    function pfx($tokens, $vars = array()) {
        
        if ($tokens == false) return false;
    
        $stack = new EvalMathStack;
        
        foreach ($tokens as $token) { // nice and easy

            // if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on
            if (is_array($token)) { // it's a function!
                $fnn = $token['fnn'];
                $count = $token['argcount'];
                if (in_array($fnn, $this->fb)) { // built-in function:
                    if (is_null($op1 = $stack->pop())) return $this->trigger("internal error");
                    $fnn = preg_replace("/^arc/", "a", $fnn); // for the 'arc' trig synonyms
                    if ($fnn == 'ln') $fnn = 'log';
                    eval('$stack->push(' . $fnn . '($op1));'); // perfectly safe eval()
                } elseif (array_key_exists($fnn, $this->fc)) { // calc emulation function
                    // get args
                    $args = array();
                    for ($i = $count-1; $i >= 0; $i--) {
                        if (is_null($args[] = $stack->pop())) return $this->trigger("internal error");
                    }
                    $res = call_user_func(array('EvalMathCalcEmul', $fnn), $args);
                    if ($res === FALSE) {
                        return $this->trigger("internal error");
                    }
                    $stack->push($res);
                } elseif (array_key_exists($fnn, $this->f)) { // user function
                    // get args
                    $args = array();
                    for ($i = count($this->f[$fnn]['args'])-1; $i >= 0; $i--) {
                        if (is_null($args[$this->f[$fnn]['args'][$i]] = $stack->pop())) return $this->trigger("internal error");
                    }
                    $stack->push($this->pfx($this->f[$fnn]['func'], $args)); // yay... recursion!!!!
                }
            // if the token is a binary operator, pop two values off the stack, do the operation, and push the result back on
            } elseif (in_array($token, array('+', '-', '*', '/', '^'), true)) {
                if (is_null($op2 = $stack->pop())) return $this->trigger("internal error");
                if (is_null($op1 = $stack->pop())) return $this->trigger("internal error");
                switch ($token) {
                    case '+':
                        $stack->push($op1+$op2); break;
                    case '-':
                        $stack->push($op1-$op2); break;
                    case '*':
                        $stack->push($op1*$op2); break;
                    case '/':
                        if ($op2 == 0) return $this->trigger("division by zero");
                        $stack->push($op1/$op2); break;
                    case '^':
                        $stack->push(pow($op1, $op2)); break;
                }
            // if the token is a unary operator, pop one value off the stack, do the operation, and push it back on
            } elseif ($token == "_") {
                $stack->push(-1*$stack->pop());
            // if the token is a number or variable, push it on the stack
            } else {
                if (is_numeric($token)) {
                    $stack->push($token);
                } elseif (array_key_exists($token, $this->v)) {
                    $stack->push($this->v[$token]);
                } elseif (array_key_exists($token, $vars)) {
                    $stack->push($vars[$token]);
                } else {
                    return $this->trigger("undefined variable '$token'");
                }
            }
        }
        // when we're out of tokens, the stack should have a single element, the final result
        if ($stack->count != 1) return $this->trigger("internal error");
        return $stack->pop();
    }
    
    // trigger an error, but nicely, if need be
    function trigger($msg) {
        $this->last_error = $msg;
        if (!$this->suppress_errors) trigger_error($msg, E_USER_WARNING);
        return false;
    }
}

// for internal use
class EvalMathStack {

    var $stack = array();
    var $count = 0;
    
    function push($val) {
        $this->stack[$this->count] = $val;
        $this->count++;
    }
    
    function pop() {
        if ($this->count > 0) {
            $this->count--;
            return $this->stack[$this->count];
        }
        return null;
    }
    
    function last($n=1) {
        if ($this->count - $n >= 0) {
            return $this->stack[$this->count-$n];
        }
        return null;
    }
}

// spreadsheed functions emulation
// watch out for reversed args!!
class EvalMathCalcEmul {

    function average($args) {
        return (EvalMathCalcEmul::sum($args)/count($args));
    }

    function max($args) {
        $res = array_pop($args);
        foreach($args as $a) {
            if ($res < $a) {
                $res = $a;
            }
        }
        return $res;
    }

    function min($args) {
        $res = array_pop($args);
        foreach($args as $a) {
            if ($res > $a) {
                $res = $a;
            }
        }
        return $res;
    }

    function mod($args) {
        return $args[1] % $args[0];
    }

    function pi($args) {
        return pi();
    }

    function power($args) {
        return $args[1]^$args[0];
    }

    function round($args) {
        if (count($args)==1) {
            return round($args[0]);
        } else {
            return round($args[1], $args[0]);
        }
    }

    function sum($args) {
        $res = 0;
        foreach($args as $a) {
           $res += $a;
        }
        return $res;
    }
}

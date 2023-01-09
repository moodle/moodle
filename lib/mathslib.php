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
 * @package    core
 * @subpackage lib
 * @copyright  Petr Skoda (skodak)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** @see evalmath/evalmath.class.php */
require_once $CFG->dirroot.'/lib/evalmath/evalmath.class.php';

/**
 * This class abstracts evaluation of spreadsheet formulas.
 * See unit tests in lib/tests/mathslib_test.php for sample usage.
 *
 * @package moodlecore
 * @copyright Petr Skoda (skodak)
  * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calc_formula {

    // private properties
    var $_em;
    var $_nfx   = false;   // postfix notation
    var $_error = false; // last error

    /**
     * Constructor for spreadsheet formula with optional parameters
     *
     * @param string $formula with leading =
     * @param array $params associative array of parameters used in formula. All parameter names must be lowercase!
     */
    public function __construct($formula, $params=false) {
        $this->_em = new EvalMath();
        $this->_em->suppress_errors = true; // no PHP errors!
        if (strpos($formula, '=') !== 0) {
            $this->_error = "missing leading '='";
            return;
        }
        $formula = substr($formula, 1);

        $this->_nfx = $this->_em->nfx($formula);
        if ($this->_nfx == false) {
            $this->_error = $this->_em->last_error;
            return;
        }
        if ($params != false) {
            $this->set_params($params);
        }
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function calc_formula($formula, $params=false) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($formula, $params);
    }

    /**
     * Raplace parameters used in existing formula,
     * parameter names must contain only lowercase [a-z] letters, no other characters are allowed!
     *
     * @param array $params associative array of parameters used in formula
     */
    function set_params($params) {
        $this->_em->v = $params;
    }

    /**
     * Evaluate formula
     *
     * @return mixed number if ok, false if error
     */
    function evaluate() {
        if ($this->_nfx == false) {
            return false;
        }
        $res = $this->_em->pfx($this->_nfx);
        if ($res === false) {
            $this->_error = $this->_em->last_error;
            return false;
        } else {
            $this->_error = false;
            return $res;
        }
    }

    /**
     * Get last error.
     * TODO: localize the strings from contructor and EvalMath library
     *
     * @return mixed string with last error description or false if ok
     */
    function get_error() {
        return $this->_error;
    }

    /**
     * Similar to format_float, formats the numbers and list separators
     * according to locale specifics.
     * @param string $formula
     * @return string localised formula
     */
    public static function localize($formula) {
        $formula = str_replace('.', '$', $formula ?? ''); // Temp placeholder.
        $formula = str_replace(',', get_string('listsep', 'langconfig'), $formula);
        $formula = str_replace('$', get_string('decsep', 'langconfig'), $formula);
        return $formula;
    }

    /**
     * Similar to unformat_float, converts floats and lists to PHP standards.
     * @param string $formula localised formula
     * @return string
     */
    public static function unlocalize($formula) {
        $formula = str_replace(get_string('decsep', 'langconfig'), '$', $formula);
        $formula = str_replace(get_string('listsep', 'langconfig'), ',', $formula);
        $formula = str_replace('$', '.', $formula); // temp placeholder
        return $formula;
    }
}

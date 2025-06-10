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
 * This class stores information about a particular attempt's result on a question
 *
 * @copyright  2013 Middlebury College {@link http://www.middlebury.edu/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\questionanalysis;

use Exception;
use InvalidArgumentException;

class question_result {

    /** @var float $_measuredability The measured ability of the user who attempted this question */
    protected $_measuredability = null;

    /** @var boolean $_correct True if the user was correct in their answer */
    protected $_correct = null;

    /**
     * Constructor - Create a new result.
     *
     * @param float $measuredability The measured ability (0-1) of the user in this attempt.
     * @param boolean $correct
     * @return void
     */
    public function __construct ($measuredability, $correct) {
        if (!is_numeric($measuredability) || $measuredability < 0 || $measuredability > 1) {
            throw new InvalidArgumentException('$measuredability must be a float between 0 and 1.');
        }
        $this->_measuredability = $measuredability;
        $this->_correct = (bool)$correct;
    }

    /**
     * Magic method to provide read-only access to our parameters
     *
     * @param $key
     * @return mixed
     */
    public function __get ($key) {
        $param = '$_'.$key;
        if (isset($this->$param)) {
            return $this->$param;
        } else {
            throw new Exception('Unknown property, '.get_class($this).'->'.$key.'.');
        }
    }
}

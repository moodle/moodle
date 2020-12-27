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
 * Class to store data for "hide if" rules for the settings form.
 *
 * @package    quizaccess_seb
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to store data for "hide if" rules for the settings form.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hideif_rule {

    /**
     * Name of the element to hide.
     * @var string
     */
    protected $element;

    /**
     * Name of the element that $element is dependant on.
     * @var string
     */
    protected $dependantname;

    /**
     * Condition. E.g. 'eq', 'noteq' and etc.
     * @var string
     */
    protected $condition;

    /**
     * Value to check the $condition against.
     * @var string
     */
    protected $dependantvalue;

    /**
     * Constructor.
     *
     * @param string $element Name of the element to hide.
     * @param string $dependantname Name of the element that $element is dependant on.
     * @param string $condition Condition. E.g. 'eq', 'noteq' and etc.
     * @param string $dependantvalue Value to check the $condition against.
     */
    public function __construct(string $element, string $dependantname, string $condition, string $dependantvalue) {
        $this->element = $element;
        $this->dependantname = $dependantname;
        $this->condition = $condition;
        $this->dependantvalue = $dependantvalue;
    }

    /**
     * Return name of the element to hide.
     * @return string
     */
    public function get_element(): string {
        return $this->element;
    }

    /**
     * Returns name of the element that $element is dependant on.
     * @return string
     */
    public function get_dependantname(): string {
        return $this->dependantname;
    }

    /**
     * Returns condition. E.g. 'eq', 'noteq' and etc
     * @return string
     */
    public function get_condition(): string {
        return $this->condition;
    }

    /**
     * Returns value to check the $condition against.
     * @return string
     */
    public function get_dependantvalue(): string {
        return $this->dependantvalue;
    }

}

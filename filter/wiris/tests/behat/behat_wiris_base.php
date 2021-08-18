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
 * Steps definitions base class for wiris.
 *
 * To extend by the steps definitions of the different Moodle components.
 *
 * It can not contain steps definitions to avoid duplicates, only utility
 * methods shared between steps.
 */

/**
 * This class provides necessary methods to run behat scripts for MathType.
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.


require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

class behat_wiris_base extends behat_base {

    /**
     * @Transform /^(\d+)$/
     */
    public function cast_string_to_number($string) {
        return intval($string);
    }

    const MAX_NUNMBER_ROWS = 500;
    /**
     * Looks for the position of an element in the first row in a table given certain text displayed.
     *
     * @param  string $text Text displayed by element desired.
     *
     * @throws  Exception If it iterates until end of table and finds no element with given parameters.
     * @throws  Exception If table is too long.
     */
    protected function look_in_table($text) {
        // $menu = $this->find('xpath', "//td[text()='$text']");
        for ($i = 1; $i < self::MAX_NUNMBER_ROWS; ++$i) {
            $possible = $this->find('xpath', "(//td[@class='leftalign cell c0'])[$i]");
            if ($possible == null) {
                throw new \Exception('There is no menu called $name');
            }
            if ($possible->getText() == $text) {
                return $i;
            }
        }
        throw new \Exception('Table is too long');
    }
}

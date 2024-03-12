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

namespace mod_quiz\adminpresets;

use ReflectionMethod;
use core_adminpresets\local\setting\adminpresets_setting;

/**
 * Admin settings class for the quiz review options.
 *
 * @package          mod_quiz
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_review_setting extends adminpresets_setting {

    /**
     * The setting value is a sum of 'review_setting::times'
     */
    protected function set_visiblevalue() {

        // Getting the masks descriptions (review_setting protected method).
        $reflectiontimes = new ReflectionMethod('mod_quiz\admin\review_setting', 'times');
        $times = $reflectiontimes->invoke(null);

        $visiblevalue = '';
        foreach ($times as $timemask => $namestring) {

            // If the value is checked.
            if ($this->value & $timemask) {
                $visiblevalue .= $namestring . ', ';
            }
        }
        $visiblevalue = rtrim($visiblevalue, ', ');

        $this->visiblevalue = $visiblevalue;
    }
}

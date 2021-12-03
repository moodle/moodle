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

namespace mod_quiz\local\setting;

use ReflectionMethod;
use tool_admin_presets\local\setting\admin_preset_setting;

/**
 * Admin settings class for the quiz review options.
 *
 * @package          mod_quiz
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_preset_mod_quiz_admin_review_setting extends admin_preset_setting {

    /**
     * Overwrite to add the reviewoptions text
     */
    public function set_text() {

        $this->set_visiblevalue();

        $name = get_string('reviewoptionsheading', 'quiz') .
            ': ' . $this->settingdata->visiblename;
        $namediv = '<div class="admin_presets_tree_name">' . $name . '</div>';
        $valuediv = '<div class="admin_presets_tree_value">' . $this->visiblevalue . '</div>';

        $this->text = $namediv . $valuediv . '<br/>';
    }

    /**
     * The setting value is a sum of 'mod_quiz_admin_review_setting::times'
     */
    protected function set_visiblevalue() {

        // Getting the masks descriptions (mod_quiz_admin_review_setting protected method).
        $reflectiontimes = new ReflectionMethod('mod_quiz_admin_review_setting', 'times');
        $reflectiontimes->setAccessible(true);
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

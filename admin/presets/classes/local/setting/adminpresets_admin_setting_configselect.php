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

namespace core_adminpresets\local\setting;

/**
 * Select one value from list.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_configselect extends adminpresets_setting {

    /**
     * Sets the setting value cleaning it.
     *
     * @param mixed $value must be one of the setting choices.
     * @return bool true if the value one of the setting choices
     */
    protected function set_value($value) {
        // When we intantiate the class we need the choices.
        if (empty($this->settindata->choices) && method_exists($this->settingdata, 'load_choices')) {
            $this->settingdata->load_choices();
        }

        if (!is_null($this->settingdata->choices) and is_array($this->settingdata->choices)) {
            foreach ($this->settingdata->choices as $key => $choice) {

                if ($key == $value) {
                    $this->value = $value;
                    $this->set_visiblevalue();
                    return true;
                }
            }
        }
        $this->value = false;
        $this->set_visiblevalue();
        return false;
    }

    protected function set_visiblevalue() {
        // Just to avoid heritage problems.
        if (empty($this->settingdata->choices[$this->value])) {
            $this->visiblevalue = '';
        } else {
            $this->visiblevalue = $this->settingdata->choices[$this->value];
        }

    }
}

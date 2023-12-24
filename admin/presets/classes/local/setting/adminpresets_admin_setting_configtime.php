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
 * Time selector.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_configtime extends adminpresets_setting {

    /** @var \admin_setting_configtime $settingdata */
    protected $settingdata;

    /**
     * To check that the value is one of the options
     *
     * @param string $name
     * @param mixed $value
     */
    public function set_attribute_value($name, $value) {
        for ($i = 0; $i < 60; $i = $i + 5) {
            $minutes[$i] = $i;
        }

        if (!empty($minutes[$value])) {
            $this->attributesvalues[$name] = $value;
        } else {
            $this->attributesvalues[$name] = $this->settingdata->defaultsetting['m'];
        }
    }

    protected function set_value($value) {
        $this->attributes = ['m' => $this->settingdata->name2];

        for ($i = 0; $i < 24; $i++) {
            $hours[$i] = $i;
        }

        if (empty($hours[$value])) {
            $this->value = false;
        }

        $this->value = $value;
        $this->set_visiblevalue();
    }

    protected function set_visiblevalue() {
        if (!is_null($this->attributesvalues) && array_key_exists($this->settingdata->name2, $this->attributesvalues)) {
            $this->visiblevalue = $this->value . ':' . $this->attributesvalues[$this->settingdata->name2];
        }
    }
}

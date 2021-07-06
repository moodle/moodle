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

namespace tool_admin_presets\local\setting;

/**
 * Extends configselect to reuse set_valuevisible.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_preset_admin_setting_users_with_capability extends admin_preset_admin_setting_configmultiselect {

    protected function set_behaviors() {
        $this->behaviors['loadchoices'] = &$this->settingdata;
    }

    protected function set_value($value) {
        // Dirty hack (the value stored in the DB is '').
        $this->settingdata->choices[''] = $this->settingdata->choices['$@NONE@$'];

        return parent::set_value($value);
    }
}

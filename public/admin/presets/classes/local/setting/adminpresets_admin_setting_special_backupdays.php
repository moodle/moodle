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
 * Special control for selecting days to backup.
 *
 * It doesn't specify loadchoices behavior because is set_visiblevalue who needs it.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_special_backupdays extends adminpresets_setting {

    /** @var \admin_setting_special_backupdays $settingdata */
    protected $settingdata;

    protected function set_value($value) {
        $this->value = clean_param($value, PARAM_SEQUENCE);
        $this->set_visiblevalue();
    }

    protected function set_visiblevalue() {
        $this->settingdata->load_choices();

        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        $selecteddays = [];

        $week = str_split($this->value);
        foreach ($week as $key => $day) {
            if ($day) {
                $index = $days[$key];
                $selecteddays[] = $this->settingdata->choices[$index];
            }
        }

        $this->visiblevalue = implode(', ', $selecteddays);
    }
}

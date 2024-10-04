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

use admin_setting;

/**
 * Checkbox with an advanced checkbox that controls an additional $name.'_locked' config setting.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_configcheckbox_with_lock extends adminpresets_admin_setting_configcheckbox {

    public function __construct(admin_setting $settingdata, $dbsettingvalue) {
        // To look for other values.
        $this->attributes = ['locked' => $settingdata->name . '_locked'];
        parent::__construct($settingdata, $dbsettingvalue);
    }

    /**
     * Uses delegation
     */
    protected function set_visiblevalue() {
        parent::set_visiblevalue();
        if (!is_null($this->attributesvalues) && array_key_exists($this->attributes['locked'], $this->attributesvalues)) {
            $value = $this->attributesvalues[$this->attributes['locked']];
            $this->visiblevalue .= $this->delegation->extra_set_visiblevalue($value, 'locked');
        }
    }
}

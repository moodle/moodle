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
 * Adds support for the "advanced" attribute.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_configselect_with_advanced extends adminpresets_admin_setting_configselect {

    /** @var string Name of the advanced setting. **/
    protected $advancedkey;

    public function __construct(admin_setting $settingdata, $dbsettingvalue) {
        // Getting the advanced defaultsetting attribute name.
        if (is_array($settingdata->defaultsetting)) {
            foreach ($settingdata->defaultsetting as $key => $defaultvalue) {
                if ($key != 'value') {
                    $this->advancedkey = $key;
                }
            }
        }

        // To look for other values.
        $this->attributes = [$this->advancedkey => $settingdata->name . '_adv'];
        parent::__construct($settingdata, $dbsettingvalue);
    }

    /**
     * Funcionality used by other _with_advanced settings
     */
    protected function set_visiblevalue() {
        parent::set_visiblevalue();
        if (!is_null($this->attributesvalues)) {
            $attribute = $this->attributes[$this->advancedkey];
            $this->visiblevalue .= $this->delegation->extra_set_visiblevalue($this->attributesvalues[$attribute], 'advanced');
        }
    }
}

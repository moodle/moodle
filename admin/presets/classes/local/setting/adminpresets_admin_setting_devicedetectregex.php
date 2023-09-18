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
 * Reimplementation to allow human friendly view of the selected regexps.
 *
 * @deprecated Moodle 4.3 MDL-78468 - No longer used since the devicedetectregex was removed.
 * @todo Final deprecation on Moodle 4.7 MDL-79052
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_devicedetectregex extends adminpresets_admin_setting_configtext {

    /**
     * @deprecated Moodle 4.3 MDL-78468 - No longer used since the devicedetectregex was removed.
     * @todo Final deprecation on Moodle 4.7 MDL-79052
     */
    public function set_visiblevalue() {
        debugging(
            __FUNCTION__ . '() is deprecated.' .
                'All functions associated with devicedetectregex theme setting are being removed.',
            DEBUG_DEVELOPER
        );
        $values = json_decode($this->get_value());

        if (!$values) {
            parent::set_visiblevalue();
            return;
        }

        $this->visiblevalue = '';
        foreach ($values as $key => $value) {
            $this->visiblevalue .= $key . ' = ' . $value . ', ';
        }
        $this->visiblevalue = rtrim($this->visiblevalue, ', ');
    }
}

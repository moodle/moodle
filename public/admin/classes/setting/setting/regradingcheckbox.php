<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Regrading checkbox setting.
 *
 * @package    core_admin
 * @copyright  onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class regradingcheckbox extends \core_admin\setting\setting\configcheckbox {
    #[\Override]
    public function write_setting($data) {
        global $CFG, $DB;

        $oldvalue  = $this->config_read($this->name);
        $return    = parent::write_setting($data);
        $newvalue  = $this->config_read($this->name);

        if ($oldvalue !== $newvalue) {
            // Force full regrading.
            $DB->set_field('grade_items', 'needsupdate', 1, ['needsupdate' => 0]);
        }

        return $return;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(regradingcheckbox::class, \admin_setting_regradingcheckbox::class);

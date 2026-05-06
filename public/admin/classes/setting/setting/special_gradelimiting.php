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
 * Special setting to limit grade items in the grader report.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class special_gradelimiting extends \core_admin\setting\setting\configcheckbox {
    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        parent::__construct(
            'unlimitedgrades',
            get_string('unlimitedgrades', 'grades'),
            get_string('unlimitedgrades_help', 'grades'),
            '0',
            '1',
            '0'
        );
    }

    /**
     * Force site regrading
     */
    public function regrade_all() {
        global $CFG;
        require_once("$CFG->libdir/gradelib.php");
        grade_force_site_regrading();
    }

    #[\Override]
    public function write_setting($data) {
        $previous = $this->get_setting();

        if ($previous === null) {
            if ($data) {
                $this->regrade_all();
            }
        } else {
            if ($data != $previous) {
                $this->regrade_all();
            }
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_gradelimiting::class, \admin_setting_special_gradelimiting::class);

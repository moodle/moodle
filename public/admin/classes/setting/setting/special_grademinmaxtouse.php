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
 * Special setting for $CFG->grade_minmaxtouse.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class special_grademinmaxtouse extends \core_admin\setting\setting\configselect {
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
            'grade_minmaxtouse',
            new \lang_string('minmaxtouse', 'grades'),
            new \lang_string('minmaxtouse_desc', 'grades'),
            GRADE_MIN_MAX_FROM_GRADE_ITEM,
            [
                GRADE_MIN_MAX_FROM_GRADE_ITEM => get_string('gradeitemminmax', 'grades'),
                GRADE_MIN_MAX_FROM_GRADE_GRADE => get_string('gradegrademinmax', 'grades'),
            ]
        );
    }

    #[\Override]
    public function write_setting($data) {
        global $CFG;

        $previous = $this->get_setting();
        $result = parent::write_setting($data);

        // If saved and the value has changed.
        if (empty($result) && $previous != $data) {
            require_once($CFG->libdir . '/gradelib.php');
            grade_force_site_regrading();
        }

        return $result;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_grademinmaxtouse::class, \admin_setting_special_grademinmaxtouse::class);

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
 * A config select for the default number of sections in a course, simply so we can lazy-load the choices.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class num_course_sections extends \core_admin\setting\setting\configselect {
    /**
     * Constructor.
     *
     * @param string $name The name of the setting
     * @param string $visiblename The visible name of the setting
     * @param string $description The description of the setting
     * @param int $defaultsetting The default setting value
     * @deprecated since Moodle 5.2
     * @todo Final deprecation in Moodle 6.0 (MDL-84291)
     */
    #[\core\attribute\deprecated(
        replacement: 'admin_setting_configtext',
        since: '5.1',
        mdl: 'MDL-84291',
    )]
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        \core\deprecation::emit_deprecation(__FUNCTION__);
        parent::__construct($name, $visiblename, $description, $defaultsetting, []);
    }

    #[\Override]
    public function load_choices() {
        $max = get_config('moodlecourse', 'maxsections');
        if (!isset($max) || !is_numeric($max)) {
            $max = 52;
        }
        for ($i = 0; $i <= $max; $i++) {
            $this->choices[$i] = "$i";
        }
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(num_course_sections::class, \admin_settings_num_course_sections::class);

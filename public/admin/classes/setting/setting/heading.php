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
 * Non-interactive heading and text display.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class heading extends \core_admin\setting {
    /**
     * Constructor for a heading.
     *
     * This is not a setting, just text.
     *
     * @param string $name A unique ascii name for the setting.
     *      Either 'mysetting' for core settings, or 'myplugin/mysetting' for those belonging to a plugin.
     * @param string $heading The heading text.
     * @param string $information The information text to display in the box.
     */
    public function __construct($name, $heading, $information) {
        $this->nosave = true;
        parent::__construct($name, $heading, $information, '');
    }

    #[\Override]
    public function get_setting() {
        return true;
    }

    #[\Override]
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings.
     *
     * @param mixed $data Unused
     * @return string Always returns an empty string
     */
    #[\Override]
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;
        $context = new \stdClass();
        $context->title = $this->visiblename;
        $context->description = $this->description;
        $context->descriptionformatted = highlight($query, markdown_to_html($this->description));
        return $OUTPUT->render_from_template('core_admin/setting_heading', $context);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(heading::class, \admin_setting_heading::class);

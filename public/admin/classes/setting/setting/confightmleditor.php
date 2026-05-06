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
 * General text area with html editor.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confightmleditor extends \core_admin\setting\setting\configtextarea {
    /**
     * Constructor for the confightmleditor class.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     * @param string $cols The number of columns for the underlying text area
     * @param string $rows The number of rows for the underlying text area
     */
    public function __construct(
        $name,
        $visiblename,
        $description,
        $defaultsetting,
        $paramtype = PARAM_RAW,
        $cols = '60',
        $rows = '8',
    ) {
        parent::__construct(
            $name,
            $visiblename,
            $description,
            $defaultsetting,
            $paramtype,
            $cols,
            $rows,
        );
        $this->set_force_ltr(false);
        editors_head_setup();
    }

    #[\Override]
    public function output_html($data, $query = '') {
        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $editor->set_text($data);
        $editor->use_editor($this->get_id(), ['noclean' => true]);
        return parent::output_html($data, $query);
    }

    #[\Override]
    public function write_setting($data) {
        if (trim(html_to_text($data)) === '') {
            $data = '';
        }
        return parent::write_setting($data);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(confightmleditor::class, \admin_setting_confightmleditor::class);

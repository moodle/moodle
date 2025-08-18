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

namespace editor_tiny;

use admin_setting_configdirectory;

/**
 * Class for the TinyMCE package source setting.
 *
 * @package    editor_tiny
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_tiny_admin_setting_package_source extends admin_setting_configdirectory {
    #[\Override]
    public function output_html($data, $query = ''): string {
        global $CFG, $OUTPUT;
        $default = $this->get_defaultsetting();
        $valid = false;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . $data;
        $pathtotiny = $path . DIRECTORY_SEPARATOR . 'tinymce.js';
        if (!empty($data) && file_exists($path) && file_exists($pathtotiny) && is_dir($path) && is_readable($path)) {
            $valid = true;
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'showvalidity' => !empty($data),
            'valid' => $valid,
            'readonly' => !empty($CFG->preventexecpath),
            'forceltr' => $this->get_force_ltr(),
        ];

        if (!empty($CFG->preventexecpath)) {
            $this->visiblename .= \html_writer::div(
                content: get_string('execpathnotallowed', 'admin'),
                class: 'alert alert-info',
            );
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configdirectory', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }

    #[\Override]
    public function write_setting($data) {
        global $CFG;

        if (empty($data) && get_config('editor_tiny', 'package_source') == manager::PACKAGE_SOURCE_STANDALONE) {
            return get_string('package_source_standalone_path_invalid', 'editor_tiny');
        }

        $data = trim($data, '/'); // Kill leading slash.
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . $data;
        $pathtotiny = $path . DIRECTORY_SEPARATOR . 'tinymce.js';
        if (!empty($data) && (!file_exists($path) || !file_exists($pathtotiny) || !is_dir($path) || !is_readable($path))) {
            // The directory must exist and be writable.
            return get_string('package_source_standalone_path_invalid', 'editor_tiny');
        }
        return parent::write_setting($data);
    }
}

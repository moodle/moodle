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
 * Text area for entering backup filename mustache templates, which are validated after submission.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configbackupfilenamemustachetemplate extends \core_admin\setting\setting\configtextarea {
    /**
     * Validates submitted data.
     * @param string $data
     * @return string|true string if error, else true if ok
     */
    public function validate($data) {
        $errors = \backup_plan_dbops::get_default_backup_filename_template_syntax_errors($data);

        if (!empty($errors)) {
            return get_string('validateerror', 'admin');
        }

        return parent::validate($data);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configbackupfilenamemustachetemplate::class, \admin_setting_configbackupfilenamemustachetemplate::class);

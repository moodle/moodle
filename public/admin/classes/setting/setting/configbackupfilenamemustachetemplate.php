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

/**
 * Text area for entering backup filename mustache templates, which are validated after submission.
 */
class admin_setting_configbackupfilenamemustachetemplate extends admin_setting_configtextarea {
    /**
     * Validates submitted data.
     * @param string $data
     * @return string|true string if error, else true if ok
     */
    public function validate($data) {
        $errors = backup_plan_dbops::get_default_backup_filename_template_syntax_errors($data);

        if (!empty($errors)) {
            return get_string('validateerror', 'admin');
        }

        return parent::validate($data);
    }
}

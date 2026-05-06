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
 * Roles to display on the course description page.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class special_coursecontact extends \core_admin\setting\setting\pickroles {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct(
            'coursecontact',
            get_string('coursecontact', 'admin'),
            get_string('coursecontact_desc', 'admin'),
            ['editingteacher']
        );
        $this->set_updatedcallback(function () {
            \cache::make('core', 'coursecontacts')->purge();
        });
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_coursecontact::class, \admin_setting_special_coursecontact::class);

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
 * Which roles to show on course description page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_coursecontact extends admin_setting_pickroles {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('coursecontact', get_string('coursecontact', 'admin'),
            get_string('coursecontact_desc', 'admin'),
            array('editingteacher'));
        $this->set_updatedcallback(function (){
            cache::make('core', 'coursecontacts')->purge();
        });
    }
}

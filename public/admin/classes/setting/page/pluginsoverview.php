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
 * General plugins manager
 */
class admin_page_pluginsoverview extends admin_externalpage {

    /**
     * Sets basic information about the external page
     */
    public function __construct() {
        global $CFG;
        parent::__construct('pluginsoverview', get_string('pluginsoverview', 'core_admin'),
            "$CFG->wwwroot/$CFG->admin/plugins.php");
    }
}

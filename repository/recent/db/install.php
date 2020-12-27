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

function xmldb_repository_recent_install() {
    global $CFG;
    $result = true;
    require_once($CFG->dirroot.'/repository/lib.php');
    $recent_plugin = new repository_type('recent', array(), true);
    if(!$id = $recent_plugin->create(true)) {
        $result = false;
    }
    return $result;
}

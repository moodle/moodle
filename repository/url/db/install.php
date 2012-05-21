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
 *
 * @package    repository_url
 * @category   repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Create a instance of url repository
 *
 * @return bool A status indicating success or failure
 */
function xmldb_repository_url_install() {
    global $CFG;
    $result = true;
    require_once($CFG->dirroot.'/repository/lib.php');
    $url_plugin = new repository_type('url', array(), true);
    if(!$id = $url_plugin->create(true)) {
        $result = false;
    }
    return $result;
}

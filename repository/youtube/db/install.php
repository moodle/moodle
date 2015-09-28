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
 * Installation file for the Youtube repository.
 *
 * @package    repository_youtube
 * @category   repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This was supposed to be the installer script for the Youtube repository.
 *
 * However, since the Youtube repository is disabled in new Moodle installations from 3.0, and since we cannot
 * just delete this file, the function's contents has been replaced to just return true.
 * See https://tracker.moodle.org/browse/MDL-50572 for more details.
 *
 * @return bool Return true.
 */
function xmldb_repository_youtube_install() {
    return true;
}

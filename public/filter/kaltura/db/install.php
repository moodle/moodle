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
 * Filter post install hook
 *
 * @package    filter_kaltura
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_filter_kaltura_install() {
    global $CFG;
    require_once("$CFG->libdir/filterlib.php");

    // Do not enable the filter when running unit tests because some core
    // tests expect a specific number of filters enabled.
    if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
        filter_set_global_state('kaltura', TEXTFILTER_ON);
    }

}

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
 * @package   enrol_license
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_license_install() {
    global $CFG, $DB;

    // Migrate welcome message.
    if (isset($CFG->sendcoursewelcomemessage)) {
        // New course default.
        set_config('sendcoursewelcomemessage', $CFG->sendcoursewelcomemessage, 'enrol_license');
        // Each instance has different setting now.
        $DB->set_field('enrol', 'customint4', $CFG->sendcoursewelcomemessage, array('enrol' => 'license'));
        unset_config('sendcoursewelcomemessage');
    }

    // Migrate long-time-no-see feature settings.
    if (isset($CFG->longtimenosee)) {
        $nosee = $CFG->longtimenosee * 3600 * 24;
        set_config('longtimenosee', $nosee, 'enrol_license');
        $DB->set_field('enrol', 'customint2', $nosee, array('enrol' => 'license'));
        unset_config('longtimenosee');
    }

    // Enable by default on the site.
    $enabledenrols = explode(',', $CFG->enrol_plugins_enabled);
    if (!in_array('license', $enabledenrols)) {
        $enabledenrols[] = 'license';
        set_config('enrol_plugins_enabled', implode(',', $enabledenrols));
    }
}

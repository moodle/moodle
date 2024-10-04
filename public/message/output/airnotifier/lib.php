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
 * Callbacks for message_airnotifier.
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2020 Moodle Pty Ltd <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.9
 */

/**
 * Callback for when a site is first registered. The function generates an Airnotifier accesskey for the new site.
 *
 * @param  int $registrationid the new registration id (registration_hubs table)
 */
function message_airnotifier_post_site_registration_confirmed(int $registrationid) {
    global $CFG;

    // Do nothing if the site already has an Airnotifier access key configured.
    if (!empty($CFG->airnotifieraccesskey)) {
        return;
    }

    $manager = new message_airnotifier_manager();

    // Do nothing for custom Airnotifier instances.
    if (strpos($CFG->airnotifierurl, $manager::AIRNOTIFIER_PUBLICURL) === false ) {
        return;
    }

    if ($key = $manager->request_accesskey()) {
        set_config('airnotifieraccesskey', $key);
    }
}

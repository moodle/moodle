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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Define administration settings on the Location settings page.
 *
 * @package     core
 * @category    admin
 * @copyright   2006 Martin Dougiamas <martin@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $temp = new admin_settingpage('locationsettings', new lang_string('locationsettings', 'core_admin'));

    if ($ADMIN->fulltree) {
        $temp->add(new admin_setting_servertimezone());

        $temp->add(new admin_setting_forcetimezone());

        $temp->add(new admin_settings_country_select('country', new lang_string('country', 'core_admin'),
            new lang_string('configcountry', 'core_admin'), 0));

        $temp->add(new admin_setting_configtext('defaultcity', new lang_string('defaultcity', 'core_admin'),
            new lang_string('defaultcity_help', 'core_admin'), ''));

        $temp->add(new admin_setting_countrycodes('allcountrycodes', new lang_string('allcountrycodes', 'core_admin'),
            new lang_string('configallcountrycodes', 'core_admin')));

        $temp->add(new admin_setting_heading('iplookup', new lang_string('iplookup', 'core_admin'),
            new lang_string('iplookupinfo', 'core_admin')));

        $temp->add(new admin_setting_configfile('geoip2file', new lang_string('geoipfile', 'core_admin'),
            new lang_string('configgeoipfile', 'core_admin', $CFG->dataroot . '/geoip/'),
            $CFG->dataroot . '/geoip/GeoIP-City.mmdb'));

        $temp->add(new admin_setting_configselect('geoipdbedition',
            new lang_string('geoipdbedition', 'core_admin'),
            new lang_string('geoipdbedition_desc', 'core_admin'), 'GeoLite2-City',
            ['GeoLite2-City' => 'GeoLite2-City', 'GeoIP2-City' => 'GeoIP2-City']));

        $temp->add(new admin_setting_configtext('geoipmaxmindaccid',
            new lang_string('geoipmaxmindaccid', 'core_admin'),
            new lang_string('geoipmaxmindaccid_desc', 'core_admin'),
            '',
            PARAM_TEXT,
        ));

        $temp->add(new admin_setting_configtext('geoipmaxmindlicensekey',
            new lang_string('geoipmaxmindlicensekey', 'core_admin'),
            new lang_string('geoipmaxmindlicensekey_desc', 'core_admin'),
            '',
            PARAM_TEXT,
        ));

        $temp->add(new admin_setting_configtext('googlemapkey3', new lang_string('googlemapkey3', 'core_admin'),
            new lang_string('googlemapkey3_help', 'core_admin'), '', PARAM_RAW, 60));
    }

    $ADMIN->add('location', $temp);
}

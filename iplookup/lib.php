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
 * IP Lookup utility functions
 *
 * @package    core
 * @subpackage iplookup
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns location information
 * @param string $ip
 * @return array
 */
function iplookup_find_location($ip) {
    global $CFG;

    $info = array('city'=>null, 'country'=>null, 'longitude'=>null, 'latitude'=>null, 'error'=>null, 'note'=>'',  'title'=>array());

    if (!empty($CFG->geoipfile) and file_exists($CFG->geoipfile)) {
        require_once('Net/GeoIP.php');

        $geoip = Net_GeoIP::getInstance($CFG->geoipfile, Net_GeoIP::STANDARD);
        $location = $geoip->lookupLocation($ip);
        $geoip->close();

        if (empty($location)) {
            $info['error'] = get_string('iplookupfailed', 'error', $ip);
            return $info;
        }
        if (!empty($location->city)) {
            $info['city'] = core_text::convert($location->city, 'iso-8859-1', 'utf-8');
            $info['title'][] = $info['city'];
        }

        if (!empty($location->countryCode)) {
            $countries = get_string_manager()->get_list_of_countries(true);
            if (isset($countries[$location->countryCode])) {
                // prefer our localized country names
                $info['country'] = $countries[$location->countryCode];
            } else {
                $info['country'] = $location->countryName;
            }
            $info['title'][] = $info['country'];

        } else if (!empty($location->countryName)) {
            $info['country'] = $location->countryName;
            $info['title'][] = $info['country'];
        }

        $info['longitude'] = $location->longitude;
        $info['latitude']  = $location->latitude;
        $info['note'] = get_string('iplookupmaxmindnote', 'admin');

        return $info;

    } else {
        require_once($CFG->libdir.'/filelib.php');

        $ipdata = download_file_content('http://www.geoplugin.net/json.gp?ip='.$ip);
        if ($ipdata) {
            $ipdata = preg_replace('/^geoPlugin\((.*)\)\s*$/s', '$1', $ipdata);
            $ipdata = json_decode($ipdata, true);
        }
        if (!is_array($ipdata)) {
            $info['error'] = get_string('cannotgeoplugin', 'error');
            return $info;
        }
        $info['latitude']  = (float)$ipdata['geoplugin_latitude'];
        $info['longitude'] = (float)$ipdata['geoplugin_longitude'];
        $info['city']      = s($ipdata['geoplugin_city']);

        $countrycode = $ipdata['geoplugin_countryCode'];
        $countries = get_string_manager()->get_list_of_countries(true);
        if (isset($countries[$countrycode])) {
            // prefer our localized country names
            $info['country'] = $countries[$countrycode];
        } else {
            $info['country'] = s($ipdata['geoplugin_countryName']);
        }

        $info['note'] = get_string('iplookupgeoplugin', 'admin');

        $info['title'][] = $info['city'];
        $info['title'][] = $info['country'];

        return $info;
    }

}

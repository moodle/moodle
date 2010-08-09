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

        $textlib = textlib_get_instance();

        $geoip = Net_GeoIP::getInstance($CFG->geoipfile, Net_GeoIP::STANDARD);
        $location = $geoip->lookupLocation($ip);
        $geoip->close();

        if (empty($location)) {
            $info['error'] = get_string('iplookupfailed', 'error', $ip);
            return $info;
        }
        if (!empty($location->city)) {
            $info['city'] = $textlib->convert($location->city, 'iso-8859-1', 'utf-8');
            $info['title'][] = $info['city'];
        }

        if (!empty($location->country_code)) {
            $countries = get_string_manager()->get_list_of_countries(true);
            if (isset($countries[$location->country_code])) {
                // prefer our localized country names
                $info['country'] = $countries[$location->country_code];
            } else {
                $info['country'] = $location->country_name;
            }
            $info['title'][] = $info['country'];
        }
        $info['longitude'] = $location->longitude;
        $info['latitude']  = $location->latitude;
        $info['note'] = get_string('iplookupmaxmindnote', 'admin');

        return $info;

    } else {
        require_once($CFG->libdir.'/filelib.php');

        $ipdata = download_file_content('http://netgeo.caida.org/perl/netgeo.cgi?target='.$ip);
        if ($ipdata === false) {
            $info['error'] = get_string('cannotnetgeo', 'error');
            return $info;
        }
        $matches = null;
        if (!preg_match('/LAT:\s*(-?\d+\.\d+)/s', $ipdata, $matches)) {
            $info['error'] = get_string('iplookupfailed', 'error', $ip);
            return $info;
        }
        $info['latitude'] = (float)$matches[1];
        if (!preg_match('/LONG:\s*(-?\d+\.\d+)/s', $ipdata, $matches)) {
            $info['error'] = get_string('iplookupfailed', 'error', $ip);
            return $info;
        }
        $info['longitude'] = (float)$matches[1];

        if (preg_match('/CITY:\s*([^<]*)/', $ipdata, $matches)) {
            if (!empty($matches[1])) {
                $info['city'] = s($matches[1]);
                $info['title'][] = $info['city'];
            }
        }

        if (preg_match('/COUNTRY:\s*([^<]*)/', $ipdata, $matches)) {
            if (!empty($matches[1])) {
                $countrycode = $matches[1];
                $countries = get_string_manager()->get_list_of_countries(true);
                if (isset($countries[$countrycode])) {
                    // prefer our localized country names
                    $info['country'] = $countries[$countrycode];
                } else {
                    $info['country'] = $countrycode;
                }
                $info['title'][] = $info['country'];
            }
        }
        $info['note'] = get_string('iplookupnetgeonote', 'admin');

        return $info;
    }

}
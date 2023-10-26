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
 * Caches for Bigbluebuttonbn
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */

defined('MOODLE_INTERNAL') || die();

$definitions = [
    // Server information
    // version  (double) => server version.
    'serverinfo' => [
        'mode' => cache_store::MODE_APPLICATION,
        'invalidationevents' => [
            'mod_bigbluebuttonbn/serversettingschanged',
        ],
    ],

    // The validatedurls cache stores a list of URLs which are either valid, or invalid.
    // Keys are a URL
    // Values are an integer.
    'validatedurls' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simpledata' => true,
    ],

    // The 'recordings' cache stores a cache of recording data.
    'recordings' => [
        'mode' => cache_store::MODE_APPLICATION,
        'invalidationevents' => [
            'mod_bigbluebuttonbn/recordingchanged',
            'mod_bigbluebuttonbn/serversettingschanged',
        ],
        'ttl' => 5 * MINSECS,
    ],

    'currentfetch' => [
        'mode' => cache_store::MODE_REQUEST,
    ],
];

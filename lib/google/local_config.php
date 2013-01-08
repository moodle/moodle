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
 * Basic configuration overwrite for Google API.
 *
 * @package   core
 * @copyright Frédéric Massart <fred@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG, $SITE;
require_once($CFG->libdir . '/weblib.php');
require_once($CFG->libdir . '/google/curlio.php');

make_temp_directory('googleapi');
$GoogleConfigTempDir = $CFG->tempdir . '/googleapi';

global $apiConfig;
$apiConfig = array(
    // The application_name is included in the User-Agent HTTP header.
    'application_name' => 'Moodle ' . $CFG->release,

    // Site name to show in the Google's OAuth 1 authentication screen.
    'site_name' => $SITE->fullname,

    // Which HTTP IO classes to use.
    'ioClass' => 'moodle_google_curlio',

    // IO Class dependent configuration, you only have to configure the values
    // for the class that was configured as the ioClass above
    'ioFileCache_directory' => $GoogleConfigTempDir
);

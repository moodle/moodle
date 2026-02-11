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

defined('MOODLE_INTERNAL') || die();

function xmldb_hvp_install() {

    // Try to install all the default content types.
    require_once(__DIR__ . '/../autoloader.php');

    // Fetch info about library updates.
    $core = \mod_hvp\framework::instance('core');
    $core->fetchLibrariesMetadata();

    // Check that plugin is set up correctly.
    $core->checkSetupForRequirements();

    // Print any messages.
    echo '<h3>' . get_string('welcomeheader', 'hvp') . '</h3>' .
         '<p>' .
         get_string('welcomegettingstarted', 'hvp', array(
             'moodle_tutorial' => 'href="https://h5p.org/moodle" target="_blank"',
             'example_content' => 'href="https://h5p.org/content-types-and-applications" target="_blank"'
         )) .
         '</p>' .
         '<p>' .
         get_string('welcomecommunity', 'hvp', array(
             'forums' => 'href="https://h5p.org/forum" target="_blank"'
         )) .
         '</p>' .
         '<p>' . get_string('welcomecontactus', 'hvp',
            'href="https://h5p.org/contact" target="_blank"') .
         '</p>';

    // Notify of communication with H5P Hub.
    // @codingStandardsIgnoreLine
    echo "<p>H5P fetches content types directly from the H5P Hub. In order to do this the H5P plugin will communicate with the Hub once a day to fetch information about new and updated content types. It will send in anonymous data to the Hub about H5P usage. Read more at <a href=\"https://h5p.org/tracking-the-usage-of-h5p\">the plugin communication page at H5P.org</a>.</p>";

    \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
    \mod_hvp\framework::printMessages('error', \mod_hvp\framework::messages('error'));
}

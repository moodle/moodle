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
 * Lists renamed classes so that the autoloader can make the old names still work.
 *
 * @package   mod_lti
 * @copyright 2018 Thom Rawson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Array 'old_class_name' => 'new\class_name'.
$renamedclasses = array(

    // Changed for PHP 7.0 which now has the word "resource" as a reserved word.
    'ltiservice_toolsettings\local\resource\contextsettings' => 'ltiservice_toolsettings\local\resources\contextsettings',
    'ltiservice_toolsettings\local\resource\linksettings' => 'ltiservice_toolsettings\local\resources\linksettings',
    'ltiservice_toolsettings\local\resource\systemsettings' => 'ltiservice_toolsettings\local\resources\systemsettings',
);


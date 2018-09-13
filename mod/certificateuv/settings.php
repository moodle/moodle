<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Provides some custom settings for the certificate module
 *
 * @package    mod_certificate
 * @copyright  Michael Avelar <michaela@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new mod_certificateuv_admin_setting_upload('certificateuv/uploadimage',
        get_string('uploadimage', 'mod_certificateuv'), get_string('uploadimagedesc', 'certificateuv'), ''));

    $settings->add(new mod_certificateuv_admin_setting_font('certificateuv/fontsans',
        get_string('fontsans', 'mod_certificateuv'), get_string('fontsans_desc', 'mod_certificateuv'), 'freesans'));

    $settings->add(new mod_certificateuv_admin_setting_font('certificateuv/fontserif',
        get_string('fontserif', 'mod_certificateuv'), get_string('fontserif_desc', 'mod_certificateuv'), 'freeserif'));

}

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
 * Settings file for plugin 'media_vimeo'
 *
 * @package   media_vimeo
 * @copyright 2023 Matt Porritt <matt.porritt@moodle.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Add a settings checkbox to enable or disable do not track vimeo links.
    $settings->add(new admin_setting_configcheckbox('media_vimeo/donottrack',
            new lang_string('donottrack', 'media_vimeo'),
            new lang_string('donottrack_desc', 'media_vimeo'), 0));
}

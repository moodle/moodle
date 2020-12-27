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
 * This file defines the admin settings for this plugin
 *
 * @package   assignfeedback_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$settings->add(new admin_setting_configcheckbox('assignfeedback_comments/default',
                   new lang_string('default', 'assignfeedback_comments'),
                   new lang_string('default_help', 'assignfeedback_comments'), 1));

$setting = new admin_setting_configcheckbox('assignfeedback_comments/inline',
                   new lang_string('commentinlinedefault', 'assignfeedback_comments'),
                   new lang_string('commentinlinedefault_help', 'assignfeedback_comments'), 0);

$setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
$setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);

$settings->add($setting);

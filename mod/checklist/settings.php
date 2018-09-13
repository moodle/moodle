<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * Global settings for the checklist
 *
 * @author  2012, Davo Smith <moodle@davosmith.co.uk>
 * @package mod_checklist
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('checklist/showmymoodle',
                                                    get_string('showmymoodle', 'mod_checklist'),
                                                    get_string('configshowmymoodle', 'mod_checklist'), 1));
    $settings->add(new admin_setting_configcheckbox('checklist/showcompletemymoodle',
                                                    get_string('showcompletemymoodle', 'mod_checklist'),
                                                    get_string('configshowcompletemymoodle', 'mod_checklist'), 1));
    $settings->add(new admin_setting_configcheckbox('checklist/showupdateablemymoodle',
                                                    get_string('showupdateablemymoodle', 'mod_checklist'),
                                                    get_string('configshowupdateablemymoodle', 'mod_checklist'), 1));

    $settings->add(new admin_setting_configcheckbox('mod_checklist/linkcourses',
                                                    get_string('linkcourses', 'mod_checklist'),
                                                    get_string('linkcourses_desc', 'mod_checklist'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_checklist/onlyenrolled',
                                                    get_string('onlyenrolled', 'mod_checklist'),
                                                    get_string('onlyenrolleddesc', 'mod_checklist'), 1));
}
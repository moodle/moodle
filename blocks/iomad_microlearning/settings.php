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
 * iomad - admin settings
 *
 * @package    iomad
 * @copyright  2011 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings = new admin_settingpage('block_iomad_microlearning', get_string('pluginname', 'block_iomad_microlearning'));

    $settings->add(new admin_setting_configtext('microlearningdefaultdue',
                                                get_string('microlearningdefaultdue', 'block_iomad_microlearning'),
                                                get_string('microlearningdefaultdue_help', 'block_iomad_microlearning'),
                                                '30',
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('microlearningdefaultpulse',
                                                get_string('microlearningdefaultpulse', 'block_iomad_microlearning'),
                                                get_string('microlearningdefaultpulse_help', 'block_iomad_microlearning'),
                                                '30',
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('microlearningdefaultreminder1',
                                                get_string('microlearningdefaultreminder1', 'block_iomad_microlearning'),
                                                get_string('microlearningdefaultreminder1_help', 'block_iomad_microlearning'),
                                                '14',
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('microlearningdefaultreminder2',
                                                get_string('microlearningdefaultreminder2', 'block_iomad_microlearning'),
                                                get_string('microlearningdefaultreminder2_help', 'block_iomad_microlearning'),
                                                '21',
                                                PARAM_INT));

}

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
 * Settings for the Report dashboard block
 *
 * @copyright 2017 Naveen kumar
 * @package   block_reportdashboard
 */
defined('MOODLE_INTERNAL') || die;
use block_reportdashboard\local;
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configstoredfile('block_reportdashboard/bannerbackground',
     get_string('bannerbackground', 'block_reportdashboard'),
     get_string('bannerbackground_desc', 'block_reportdashboard'), 'bannerbackground', 0,
     array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/statisticsone',
     get_string('statisticsone', 'block_reportdashboard'),
     get_string('statisticsone_desc',
        'block_reportdashboard'), '#FFFFFF'));
    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/statisticstwo',
        get_string('statisticstwo', 'block_reportdashboard'),
        get_string('statisticstwo_desc',
            'block_reportdashboard'), '#FFFFFF'));
    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/statisticsthree',
     get_string('statisticsthree', 'block_reportdashboard'),
     get_string('statisticsthree_desc',
        'block_reportdashboard'), '#FFFFFF'));
    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/statisticsfour',
     get_string('statisticsfour', 'block_reportdashboard'),
     get_string('statisticsfour_desc',
        'block_reportdashboard'), '#FFFFFF'));
    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/statisticsfive',
     get_string('statisticsfive', 'block_reportdashboard'),
     get_string('statisticsfive_desc',
        'block_reportdashboard'), '#FFFFFF'));
    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/statisticssix',
     get_string('statisticssix', 'block_reportdashboard'),
     get_string('statisticssix_desc',
        'block_reportdashboard'), '#FFFFFF'));

    $fontoptions = array(0 => get_string('userthemefonts', 'block_reportdashboard'),
        1 => get_string('opensansfont', 'block_reportdashboard'),
        2 => get_string('ptsansfont', 'block_reportdashboard'));
    $settings->add(new admin_setting_configselect('block_reportdashboard/reportsfont',
        get_string('reportsfont', 'block_reportdashboard'),
        get_string('reportsfont_desc', 'block_reportdashboard'), 0, $fontoptions));

    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/header_start',
     get_string('header_start_color', 'block_reportdashboard'),
      get_string('header_start_desc', 'block_reportdashboard'), '#0d3c56'));

    $settings->add(new admin_setting_configcolourpicker('block_reportdashboard/header_end',
     get_string('header_end_color', 'block_reportdashboard'),
     get_string('header_end_desc', 'block_reportdashboard'), '#35779b'));
}
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
 * Settings for block_iseek
 * @package    block_iseek
 * @copyright  iseek.ai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();

$settings->add(
        new admin_setting_heading(
        'headerconfig',
        get_string('settings_header', 'block_iseek'),
        get_string('settings_desc', 'block_iseek')
    )
);
 
$settings->add(
    new admin_setting_configtext(
        'iseek/LTI_KEY',
        get_string('lti_key', 'block_iseek'),
        '',
        ''
    )
);
    
$settings->add(
    new admin_setting_configtext(
        'iseek/LTI_SECRET',
        get_string('lti_secret', 'block_iseek'),
        '',
        ''
    )
);
    
$settings->add(
    new admin_setting_configtext(
        'iseek/LTI_URL',
        get_string('lti_url', 'block_iseek'), 
        '', 
        'https://api.iseek.com/lti/launch'
    )
);

$settings->add(
    new admin_setting_configcheckbox('iseek/categorylimit',
        get_string('categorylimit', 'block_iseek'),
        get_string('categorylimit_help', 'block_iseek'), 0
    )
);

$coursecats = $DB->get_records_menu(
    'course_categories', null, 'name ASC', 'id, name');

$settings->add(
    new admin_setting_configmultiselect(
        'iseek/cats', get_string('categories', 'block_iseek'),
            get_string('categories_help', 'block_iseek'),
            array(), $coursecats
    )
);



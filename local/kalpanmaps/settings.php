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
 * @package   local_kalpanmaps
 * @copyright 2021 onwards LSUOnline & Continuing Education
 * @copyright 2017 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // Build this for later.
    $yesno = [
        0 => get_string('no'),
        1 => get_string('yes')
    ];

    // Instantiate the settings in Moodle.
    $settings = new admin_settingpage('local_kalpanmaps', get_string('convert_kalvids', 'local_kalpanmaps'));

    // Add the settings to the admin menu.
    $ADMIN->add('localplugins', $settings);

    // Add the general subheading.
    $settings->add(
        new admin_setting_heading(
            'local_kalpanmaps_general', get_string('general', 'local_kalpanmaps'),
            ''
        )
    );

    // Add the option to turn on/off verbose mode.
    $settings->add(
        new admin_setting_configselect(
            'local_kalpanmaps_verbose',
            get_string('verbose', 'local_kalpanmaps'),
            get_string('verbose_help', 'local_kalpanmaps'),
            0, // Default.
            $yesno
        )
    );

    // Add the option to purge previous import data on new import.
    $settings->add(
        new admin_setting_configselect(
            'local_kalpanmaps_purge',
            get_string('purge', 'local_kalpanmaps'),
            get_string('purge_help', 'local_kalpanmaps'),
            0, // Default.
            $yesno
        )
    );

    // Add a subhead for kaltur video resource conversion settings.
    $settings->add(
        new admin_setting_heading(
            'local_kalpanmaps_res', get_string('convert_kalvidres', 'local_kalpanmaps'),
            get_string('convert_kalvidres_help', 'local_kalpanmaps')
        )
    );

    // Add an option to hide kaltura video resources on conversion.
    $settings->add(
        new admin_setting_configselect(
            'local_kalpanmaps_kalvidres_conv_hide',
            get_string('hide_kaltura_items', 'local_kalpanmaps'),
            get_string('hide_kaltura_items_help', 'local_kalpanmaps'),
            0, // Default.
            $yesno
        )
    );

    // Add an option to find visible kaltura video resources that have already been converted and hide them.
    $settings->add(
        new admin_setting_configselect(
            'local_kalpanmaps_kalvidres_postconv_hide',
            get_string('hide_kaltura_items2', 'local_kalpanmaps'),
            get_string('hide_kaltura_items2_help', 'local_kalpanmaps'),
            0, // Default.
            $yesno
        )
    );

    // Add an option to specify a file location for automated importing of kaltura to panopto mappings.
    $settings->add(
        new admin_setting_configtext(
            'local_kalpanmaps_kalpanmapfile',
            get_string('kalpanmapfile', 'local_kalpanmaps'),
            get_string('kalpanmapfile_help', 'local_kalpanmaps'),
            $CFG->dirroot . '/local/kalpanmaps/example.csv'
        )
    );

    // Add the subhead for iframe conversion specific settings.
    $settings->add(
        new admin_setting_heading('local_kalpanmaps_iframes',
            get_string('convert_kalembeds', 'local_kalpanmaps'),
            get_string('convert_kalembeds_help', 'local_kalpanmaps')
        )
    );

    // Add an option to process student data.
    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_kalprocessstudents',
            get_string('kalembeds_studentdata', 'local_kalpanmaps'),
            get_string('kalembeds_studentdata_help', 'local_kalpanmaps'),
            0 // Default.
        )
    );

    // Add a configurable default width.
    $settings->add(
        new admin_setting_configtext(
            'local_kalpanmaps_width',
            get_string('kalembeds_width', 'local_kalpanmaps'),
            get_string('kalembeds_width_help', 'local_kalpanmaps'),
            '400'
        )
    );

    // Add a configuable default height.
    $settings->add(
        new admin_setting_configtext(
            'local_kalpanmaps_height',
            get_string('kalembeds_height', 'local_kalpanmaps'),
            get_string('kalembeds_height_help', 'local_kalpanmaps'),
            '285'
        )
    );

    // Add options for urlparms.
    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_showtitle',
            get_string('kalembeds_showtitle', 'local_kalpanmaps'),
            get_string('kalembeds_showtitle_help', 'local_kalpanmaps'),
            0 // Default.
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_captions',
            get_string('kalembeds_captions', 'local_kalpanmaps'),
            get_string('kalembeds_captions_help', 'local_kalpanmaps'),
            1 // Default.
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_autoplay',
            get_string('kalembeds_autoplay', 'local_kalpanmaps'),
            get_string('kalembeds_autoplay_help', 'local_kalpanmaps'),
            0 // Default.
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_offerviewer',
            get_string('kalembeds_offerviewer', 'local_kalpanmaps'),
            get_string('kalembeds_offerviewer_help', 'local_kalpanmaps'),
            1 // Default.
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_showbrand',
            get_string('kalembeds_showbrand', 'local_kalpanmaps'),
            get_string('kalembeds_showbrand_help', 'local_kalpanmaps'),
            0 // Default.
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'local_kalpanmaps_interactivity',
            get_string('kalembeds_interactivity', 'local_kalpanmaps'),
            get_string('kalembeds_interactivity_help', 'local_kalpanmaps'),
            0 // Default.
        )
    );

    // Add the category limit settings checkbox.
    $settings->add(
         new admin_setting_configcheckbox('local_kalpanmaps_categorylimit',
             get_string('categorylimit', 'local_kalpanmaps'),
             get_string('categorylimit_help', 'local_kalpanmaps'), 0
         )
     );

    // Add the category limit course categories.
     $course_cats = $DB->get_records_menu(
         'course_categories', null, 'name ASC', 'id, name');

    // Add the category milti-select limit settings.
     $settings->add(
         new admin_setting_configmultiselect(
             'local_kalpanmaps_cats', get_string('categories', 'local_kalpanmaps'),
                 get_string('categories_help', 'local_kalpanmaps'),
                 array(), $course_cats
         )
     );
}

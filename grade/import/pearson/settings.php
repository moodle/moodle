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
 * Version details
 *
 * @package    gradeimport_pearson
 * @copyright  2008 onwards Robert Russo, Philip Cali, Adam Zapletal, David Lowe
 * @copyright  2008 onwards Louisiana State University
 * @Copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Create the settings block.
$settings = new admin_settingpage('gradeimportpearson', get_string('settings', 'gradeimport_pearson'));

$encodings = "ASCII
UTF-8
ISO-8859-1
ISO-8859-5";
// Make sure only admins see this one.
if ($ADMIN->fulltree) {
    // --------------------------------
    // Dashboard Link.
    $settings->add(
        new admin_setting_heading(
            'gradeimport_pearson_link_back',
            get_string('gradeimport_pearson_link_back', 'gradeimport_pearson'),
            ''
        )
    );
    // --------------------------------
    // Pearson Importer Settings Title.
    // $settings->add(
    //     new admin_setting_heading(
    //         'gradeimport_pearson_main_title',
    //         get_string('maintitle', 'gradeimport_pearson'),
    //         ''
    //     )
    // );

    // --------------------------------
    // Encode Warning.
    $settings->add(
        new admin_setting_configcheckbox(
            'gradeimport_pearson_encoding_message',
            get_string('gradeimport_pearson_encoding_message_title', 'gradeimport_pearson'),
            get_string('gradeimport_pearson_encoding_message_desc', 'gradeimport_pearson'),
            0
        )
    );

    // --------------------------------
    // Encode Settings.
    $settings->add(
        new admin_setting_configcheckbox(
            'gradeimport_pearson_convert_encoding',
            get_string('gradeimport_pearson_convert_encoding_title', 'gradeimport_pearson'),
            get_string('gradeimport_pearson_convert_encoding_desc', 'gradeimport_pearson'),
            0
        )
    );

    // --------------------------------
    // Encoding List.
    $settings->add(
        new admin_setting_configtextarea(
            'gradeimport_pearson_encoding_list',
            get_string('gradeimport_pearson_encoding_list', 'gradeimport_pearson'),
            'List of file encodings',
            $encodings,
            PARAM_TEXT
        )
    );
}

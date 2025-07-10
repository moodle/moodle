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
 * Settings for UbD course format.
 *
 * @package    format_ubd
 * @copyright  2025 Moodle Evolved Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    
    // General settings
    $settings->add(new admin_setting_heading(
        'format_ubd/general',
        get_string('general', 'core'),
        get_string('ubd_help_content', 'format_ubd')
    ));
    
    // Default number of sections
    $settings->add(new admin_setting_configtext(
        'format_ubd/defaultnumsections',
        get_string('defaultnumsections', 'core'),
        get_string('defaultnumsections_help', 'core'),
        10,
        PARAM_INT
    ));
    
    // Auto-save interval
    $settings->add(new admin_setting_configselect(
        'format_ubd/autosave_interval',
        get_string('autosave_interval', 'format_ubd'),
        get_string('autosave_interval_desc', 'format_ubd'),
        30,
        array(
            15 => '15 seconds',
            30 => '30 seconds',
            60 => '1 minute',
            120 => '2 minutes',
            300 => '5 minutes',
            0 => 'Disabled'
        )
    ));
    
    // Maximum field length
    $settings->add(new admin_setting_configtext(
        'format_ubd/max_field_length',
        get_string('max_field_length', 'format_ubd'),
        get_string('max_field_length_desc', 'format_ubd'),
        5000,
        PARAM_INT
    ));
    
    // Maximum total content length
    $settings->add(new admin_setting_configtext(
        'format_ubd/max_total_length',
        get_string('max_total_length', 'format_ubd'),
        get_string('max_total_length_desc', 'format_ubd'),
        25000,
        PARAM_INT
    ));
    
    // Enable templates
    $settings->add(new admin_setting_configcheckbox(
        'format_ubd/enable_templates',
        get_string('enable_templates', 'format_ubd'),
        get_string('enable_templates_desc', 'format_ubd'),
        1
    ));
    
    // Enable export functionality
    $settings->add(new admin_setting_configcheckbox(
        'format_ubd/enable_export',
        get_string('enable_export', 'format_ubd'),
        get_string('enable_export_desc', 'format_ubd'),
        1
    ));
    
    // Default stage visibility
    $settings->add(new admin_setting_configmulticheckbox(
        'format_ubd/default_expanded_stages',
        get_string('default_expanded_stages', 'format_ubd'),
        get_string('default_expanded_stages_desc', 'format_ubd'),
        array('stage1' => 1, 'stage2' => 1, 'stage3' => 1),
        array(
            'stage1' => get_string('ubd_stage1', 'format_ubd'),
            'stage2' => get_string('ubd_stage2', 'format_ubd'),
            'stage3' => get_string('ubd_stage3', 'format_ubd')
        )
    ));
    
    // Theme customization
    $settings->add(new admin_setting_heading(
        'format_ubd/theme',
        get_string('theme_customization', 'format_ubd'),
        get_string('theme_customization_desc', 'format_ubd')
    ));
    
    // Stage 1 color
    $settings->add(new admin_setting_configcolourpicker(
        'format_ubd/stage1_color',
        get_string('stage1_color', 'format_ubd'),
        get_string('stage1_color_desc', 'format_ubd'),
        '#e91e63'
    ));
    
    // Stage 2 color
    $settings->add(new admin_setting_configcolourpicker(
        'format_ubd/stage2_color',
        get_string('stage2_color', 'format_ubd'),
        get_string('stage2_color_desc', 'format_ubd'),
        '#ff9800'
    ));
    
    // Stage 3 color
    $settings->add(new admin_setting_configcolourpicker(
        'format_ubd/stage3_color',
        get_string('stage3_color', 'format_ubd'),
        get_string('stage3_color_desc', 'format_ubd'),
        '#4caf50'
    ));
    
}

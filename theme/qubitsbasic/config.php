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
 * Theme config
 *
 * @package   theme_qubitsbasic
 * @copyright 2023 Qubits Dev Team.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

$THEME->doctype = 'html5';
$THEME->name = 'qubitsbasic';
$THEME->parents = array('boost');
$THEME->sheets = ['qubitsfonts','qubitsbasic', 'styles'];
$THEME->editor_sheets = [];
$THEME->enable_dock = false;
$THEME->scss = function($theme) {
    return theme_qubitsbasic_get_main_scss_content($theme);
}; 
$THEME->extrascsscallback = 'theme_qubitsbasic_get_extra_scss';
$THEME->precompiledcsscallback = 'theme_qubitsbasic_get_precompiled_css';
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// By default, all boost theme do not need their titles displayed.
$THEME->activityheaderconfig = [
    'notitle' => true
];

$THEME->layouts = [
    'login' => array(
        'file' => 'login.php',
        'regions' => array(),
        'options' => array('langmenu' => true)
    ),
    // My courses page.
    'mycourses' => array(
        'file' => 'mycourses.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => false),
    )
];
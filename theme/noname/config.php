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
 * Noname config.
 *
 * @package   theme_noname
 * @copyright 2016 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'noname';
$THEME->sheets = ['build'];
$THEME->editor_sheets = ['editor'];

$THEME->layouts = [
    'standard' => [
        'file' => 'default.php',
        'regions' => ['side-pre', 'side-post'],
    ],

    // 'course' => [],
    // 'coursecategory' => [],
    // 'incourse' => [],
    // 'frontpage' => [],
    // 'admin' => [],
    // 'mydashboard' => [],
    // 'mypublic' => [],
    // 'report' => [],
    // 'login' => [],

    // General purpose.
    // 'embedded' => [],
    // 'frametop' => [],
    // 'maintenance' => [],
    // 'popup' => [],
    // 'print' => [],
    // 'redirect' => [],
    // 'secure' => [],
];

// $THEME->javascripts = array();
// $THEME->javascripts_footer = array();
$THEME->parents = [];
$THEME->rarrow = '▶';
$THEME->larrow = '◀';
$THEME->enable_dock = false;
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = array();
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->blockrtlmanipulations = array(
    'side-pre' => 'side-post',
    'side-post' => 'side-pre'
);

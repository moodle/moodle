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
 * Theme More config file.
 *
 * @package    theme_more
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->name = 'more';
$THEME->parents = array('clean', 'bootstrapbase');

$THEME->doctype = 'html5';
$THEME->sheets = array('custom');
$THEME->lessfile = 'moodle';
$THEME->parents_exclude_sheets = array('bootstrapbase' => array('moodle'), 'clean' => array('custom'));
$THEME->lessvariablescallback = 'theme_more_less_variables';
$THEME->extralesscallback = 'theme_more_extra_less';
$THEME->yuicssmodules = array();
$THEME->enable_dock = true;
$THEME->editor_sheets = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_more_process_css';

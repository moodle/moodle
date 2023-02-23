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
 * This file describes jQuery plugins available in the Moodle
 * core component. These can be included in page using:
 *   $PAGE->requires->jquery();
 *   $PAGE->requires->jquery_plugin('ui');
 *   $PAGE->requires->jquery_plugin('ui-css');
 *
 * Please note that other moodle plugins can not use the same
 * jquery plugin names, only one is loaded if collision detected.
 *
 * Any Moodle plugin may add jquery/plugins.php that defines extra
 * jQuery plugins.
 *
 * Themes and other plugins may override any jquery plugin,
 * for example to override default jQueryUI theme.
 *
 * @package    core
 * @copyright  2013 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugins = array(
    'jquery'  => array('files' => array('jquery-3.6.1.min.js')),
    'ui'      => array('files' => array('ui-1.13.2/jquery-ui.min.js')),
    'ui-css'  => array('files' => array('ui-1.13.2/theme/smoothness/jquery-ui.min.css')),
);

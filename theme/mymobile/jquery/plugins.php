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
 * This file describes jQuery plugins available in the moodle
 * core component. These can be included in page using:
 *   $PAGE->requires->jquery();
 *   $PAGE->requires->jquery_plugin('migrate', 'core');
 *   $PAGE->requires->jquery_plugin('ui', 'core');
 *   $PAGE->requires->jquery_plugin('ui-css', 'core');
 *
 * Please note that other moodle plugins can not use the sample
 * jquery plugin names, only one is loaded if collision detected.
 *
 * Any Moodle plugin may add jquery/plugins.php and include extra
 * jQuery plugins.
 *
 * Themes or other plugin may blacklist any jquery plugin,
 * for example to override default jQueryUI theme.
 *
 * @package    core
 * @copyright  2013 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * Note: If you want to change the contents of 'custom131.js' because you are
 *       developing but don't want to up the version until you are happy with
 *       it, then 'Purge All Caches' does not work, you need to clear the browsers
 *       cache at the client end so that it requests a new copy on page load.
 *       This also applies to the small 'hack' that is contained witin
 *       'jquery.mobile-1.3.1.js'.
 *       G J Barnard 14th April 2013
 */
$plugins = array(
    'mymobile'   => array('files' => array('custom131.js')), // Do not forget to bump up version after modification!
    'mobile'     => array('files' => array('jquery.mobile-1.3.1.js'))
);

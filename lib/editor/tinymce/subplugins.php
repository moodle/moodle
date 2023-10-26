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
 * TinyMCE subplugin management.
 *
 * @package   editor_tinymce
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$disable = optional_param('disable', '', PARAM_PLUGIN);
$enable  = optional_param('enable', '', PARAM_PLUGIN);
$return  = optional_param('return', 'overview', PARAM_ALPHA);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/subplugins.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

if ($return === 'settings') {
    $returnurl = new moodle_url('/admin/settings.php', array('section'=>'editorsettingstinymce'));
} else {
    $returnurl = new moodle_url('/admin/plugins.php');
}

if ($disable) {
    $class = \core_plugin_manager::resolve_plugininfo_class('tinymce');
    $class::enable_plugin($disable, false);
} else if ($enable) {
    $class = \core_plugin_manager::resolve_plugininfo_class('tinymce');
    $class::enable_plugin($enable, true);
}

redirect($returnurl);

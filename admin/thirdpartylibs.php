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
 * List of 3rd party libs used in moodle and all plugins.
 *
 * @package   admin
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

admin_externalpage_setup('thirdpartylibs');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('thirdpartylibs', 'core_admin'));

$files = array('core' => "$CFG->libdir/thirdpartylibs.xml");

$plugintypes = core_component::get_plugin_types();
foreach ($plugintypes as $type => $ignored) {
    $plugins = core_component::get_plugin_list_with_file($type, 'thirdpartylibs.xml', false);
    foreach ($plugins as $plugin => $path) {
        $files[$type.'_'.$plugin] = $path;
    }
}

$table = new html_table();
$table->head = array(
    get_string('thirdpartylibrary', 'core_admin'), get_string('version'),
    get_string('thirdpartylibrarylocation', 'core_admin'), get_string('license'));
$table->align = array('left', 'left', 'left', 'left');
$table->id = 'thirdpartylibs';
$table->attributes['class'] = 'admintable generaltable';
$table->data  = array();

foreach ($files as $component => $xmlpath) {
    $xml = simplexml_load_file($xmlpath);
    foreach ($xml as $lib) {
        $base = realpath(dirname($xmlpath));
        $location = substr($base, strlen($CFG->dirroot)).'/'.$lib->location;
        if (is_dir($CFG->dirroot.$location)) {
            $location .= '/';
        }
        $version = '';
        if (!empty($lib->version)) {
            $version = $lib->version;
        }
        $license = $lib->license;
        if (!empty($lib->licenseversion)) {
            $license .= ' '.$lib->licenseversion;
        }

        $table->data[] = new html_table_row(array($lib->name, $version, $location, $license));
    }
}

echo html_writer::table($table);

echo $OUTPUT->footer();

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
 * TinyMCE admin setting stuff.
 *
 * @package   editor_tinymce
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/pluginlib.php");

/**
 * Editor subplugin info class.
 */
class plugininfo_tinymce extends plugininfo_base {

    public function get_uninstall_url() {
        return new moodle_url('/lib/editor/tinymce/subplugins.php', array('delete' => $this->name, 'sesskey' => sesskey()));
    }

    public function get_settings_url() {
        global $CFG;
        if (file_exists("$CFG->dirroot/lib/editor/tinymce/plugins/$this->name/settings.php")) {
            return new moodle_url('/admin/settings.php', array('section'=>'tinymce'.$this->name.'settings'));
        } else {
            return null;
        }
    }
}

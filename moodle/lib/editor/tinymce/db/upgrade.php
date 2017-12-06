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
 * TinyMCE editor integration upgrade.
 *
 * @package    editor_tinymce
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_editor_tinymce_upgrade($oldversion) {
    global $CFG, $DB;

    if ($oldversion < 2014062900) {
        // We only want to delete DragMath from the customtoolbar setting if the directory no longer exists. If
        // the directory is present then it means it has been restored, so do not remove any settings.
        if (!check_dir_exists($CFG->libdir . '/editor/tinymce/plugins/dragmath', false)) {
            // Remove the DragMath plugin from the 'customtoolbar' setting (if it exists) as it has been removed.
            $currentorder = get_config('editor_tinymce', 'customtoolbar');
            $newtoolbarrows = array();
            $currenttoolbarrows = explode("\n", $currentorder);
            foreach ($currenttoolbarrows as $currenttoolbarrow) {
                $currenttoolbarrow = implode(',', array_diff(str_getcsv($currenttoolbarrow), array('dragmath')));
                $newtoolbarrows[] = $currenttoolbarrow;
            }
            $neworder = implode("\n", $newtoolbarrows);
            unset_config('customtoolbar', 'editor_tinymce');
            set_config('customtoolbar', $neworder, 'editor_tinymce');
        }

        upgrade_plugin_savepoint(true, 2014062900, 'editor', 'tinymce');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

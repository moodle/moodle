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
 * Algebra filter upgrade code.
 *
 * @package    filter_algebra
 * @copyright  2025 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade function for the algebra filter.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_algebra_upgrade($oldversion) {

    if ($oldversion < 2025122200) {
        global $OUTPUT;

        // Show notification if filter_algebra is enabled.
        if (filter_is_enabled('algebra')) {
            echo $OUTPUT->notification(get_string('mimetexdeprecated', 'admin', ['plugin_name' => 'filter_algebra']), 'info');
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2025122200, 'filter', 'algebra');
    }

    // Automatically generated Moodle v5.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026062500) {
        global $CFG;

        // Migrate existing rendered images from the legacy dataroot location to the Moodle file system,
        // then remove the old directory. Images not migrated (corrupt/unreadable) will be re-rendered.
        $olddir = str_replace('\\', '/', "{$CFG->dataroot}/filter/algebra");
        if (file_exists($olddir) && is_dir($olddir)) {
            $syscontext = \core\context\system::instance();
            $fs = get_file_storage();

            $files = array_merge(
                glob($olddir . '/*.png') ?: [],
                glob($olddir . '/*.gif') ?: [],
                glob($olddir . '/*.svg') ?: [],
            );
            foreach ($files as $filepath) {
                $filename = basename($filepath);
                if ($fs->file_exists($syscontext->id, 'filter_algebra', 'rendered_images', 0, '/', $filename)) {
                    continue;
                }
                $filerecord = [
                    'contextid' => $syscontext->id,
                    'component' => 'filter_algebra',
                    'filearea' => 'rendered_images',
                    'itemid' => 0,
                    'filepath' => '/',
                    'filename' => $filename,
                ];
                try {
                    $fs->create_file_from_pathname($filerecord, $filepath);
                } catch (\stored_file_creation_exception) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
                    // Corrupt or unreadable images will be re-rendered on demand.
                }
            }
            remove_dir($olddir);
        }

        upgrade_plugin_savepoint(true, 2026062500, 'filter', 'algebra');
    }

    return true;
}

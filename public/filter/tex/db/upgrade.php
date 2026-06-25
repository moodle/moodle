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
 * TeX filter upgrade code.
 *
 * @package    filter
 * @subpackage tex
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade function for the tex filter.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_tex_upgrade($oldversion) {
    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025122200) {
        global $OUTPUT;

        // Remove MimeTeX configuration setting as MimeTeX support has been removed.
        unset_config('pathmimetex', 'filter_tex');

        // Show notification if filter_tex is enabled.
        if (filter_is_enabled('tex')) {
            echo $OUTPUT->notification(get_string('mimetexdeprecated', 'admin', ['plugin_name' => 'filter_tex']), 'info');
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2025122200, 'filter', 'tex');
    }

    // Automatically generated Moodle v5.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026062500) {
        global $CFG;

        // Migrate existing rendered images from the legacy dataroot location to the Moodle file system,
        // then remove the old directory. Images not migrated (corrupt/unreadable) will be re-rendered.
        $olddir = str_replace('\\', '/', "{$CFG->dataroot}/filter/tex");
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
                if ($fs->file_exists($syscontext->id, 'filter_tex', 'rendered_images', 0, '/', $filename)) {
                    continue;
                }
                $filerecord = [
                    'contextid' => $syscontext->id,
                    'component' => 'filter_tex',
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

        upgrade_plugin_savepoint(true, 2026062500, 'filter', 'tex');
    }

    return true;
}

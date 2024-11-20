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
 * Theme pimenko database upgrade file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade.
 *
 * @param int $oldversion Is this an old version
 * @return bool Success.
 */
function xmldb_theme_pimenko_upgrade($oldversion = 0) {
    if ($oldversion < 2022041222) {
        global $DB;

        $courses = $DB->get_records_sql('SELECT * FROM {course}');

        // Check if moockie2 exist.
        if (core_plugin_manager::instance()->get_plugin_info('theme_moockie2')) {
            foreach ($courses as $course) {
                $context = context_course::instance($course->id);

                // Check if file existe.
                $fs = get_file_storage();

                $filepimenko = $fs->get_area_files(
                    $context->id,
                    'theme_pimenko',
                    'coverimage',
                    0,
                    "itemid, filepath, filename",
                    false
                );
                if (!$filepimenko) {
                    $oldfilesinfo = $fs->get_area_files(
                        $context->id,
                        'theme_moockie2',
                        'coverimage',
                        0,
                        "itemid, filepath, filename",
                        false
                    );

                    foreach ($oldfilesinfo as $file) {

                        $filerecord = array(
                            'contextid' => $context->id,
                            'component' => 'theme_pimenko',
                            'filearea' => $file->get_filearea(),
                            'filename' => $file->get_filename(),
                            'filepath' => '/',
                            'itemid' => 0,
                            'timemodified' => time()
                        );
                        if (!$filerec = $fs->get_file(
                            $filerecord['contextid'],
                            $filerecord['component'],
                            $filerecord['filearea'],
                            $filerecord['itemid'],
                            $filerecord['filepath'],
                            $filerecord['filename'])) {
                            $filerec = $fs->create_file_from_storedfile($filerecord, $file);
                        }
                    }
                }
            }
        }

        upgrade_plugin_savepoint(true, 2022041222, 'theme', 'pimenko');
    }

    // Automatic 'Purge all caches'....
    purge_all_caches();

    return true;
}

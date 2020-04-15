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
 * Contains API class for the H5P area.
 *
 * @package    core_h5p
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains API class for the H5P area.
 *
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Delete a library and also all the libraries depending on it and the H5P contents using it. For the H5P content, only the
     * database entries in {h5p} are removed (the .h5p files are not removed in order to let users to deploy them again).
     *
     * @param  factory   $factory The H5P factory.
     * @param  \stdClass $library The library to delete.
     */
    public static function delete_library(factory $factory, \stdClass $library): void {
        global $DB;

        // Get the H5P contents using this library, to remove them from DB. The .h5p files won't be removed
        // so they will be displayed by the player next time a user with the proper permissions accesses it.
        $sql = 'SELECT DISTINCT hcl.h5pid
                  FROM {h5p_contents_libraries} hcl
                 WHERE hcl.libraryid = :libraryid';
        $params = ['libraryid' => $library->id];
        $h5pcontents = $DB->get_records_sql($sql, $params);
        foreach ($h5pcontents as $h5pcontent) {
            $factory->get_framework()->deleteContentData($h5pcontent->h5pid);
        }

        $fs = $factory->get_core()->fs;
        $framework = $factory->get_framework();
        // Delete the library from the file system.
        $fs->delete_library(array('libraryId' => $library->id));
        // Delete also the cache assets to rebuild them next time.
        $framework->deleteCachedAssets($library->id);

        // Remove library data from database.
        $DB->delete_records('h5p_library_dependencies', array('libraryid' => $library->id));
        $DB->delete_records('h5p_libraries', array('id' => $library->id));

        // Remove the libraries using this library.
        $requiredlibraries = self::get_dependent_libraries($library->id);
        foreach ($requiredlibraries as $requiredlibrary) {
            self::delete_library($factory, $requiredlibrary);
        }
    }

    /**
     * Get all the libraries using a defined library.
     *
     * @param  int    $libraryid The library to get its dependencies.
     * @return array  List of libraryid with all the libraries required by a defined library.
     */
    public static function get_dependent_libraries(int $libraryid): array {
        global $DB;

        $sql = 'SELECT *
                  FROM {h5p_libraries}
                 WHERE id IN (SELECT DISTINCT hl.id
                                FROM {h5p_library_dependencies} hld
                                JOIN {h5p_libraries} hl ON hl.id = hld.libraryid
                               WHERE hld.requiredlibraryid = :libraryid)';
        $params = ['libraryid' => $libraryid];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get a library from an identifier.
     *
     * @param  int    $libraryid The library identifier.
     * @return \stdClass The library object having the library identifier defined.
     * @throws dml_exception A DML specific exception is thrown if the libraryid doesn't exist.
     */
    public static function get_library(int $libraryid): \stdClass {
        global $DB;

        return $DB->get_record('h5p_libraries', ['id' => $libraryid], '*', MUST_EXIST);
    }
}

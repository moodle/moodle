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
 * Class \core_h5p\editor_framework
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>, base on code by Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use H5peditorStorage;
use stdClass;

/**
 * Moodle's implementation of the H5P Editor storage interface.
 *
 * Makes it possible for the editor's core library to communicate with the
 * database used by Moodle.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>, base on code by Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_framework implements H5peditorStorage {

    /**
     * Load language file(JSON).
     * Used to translate the editor fields(title, description etc.)
     *
     * @param string $name The machine readable name of the library(content type)
     * @param int $major Major part of version number
     * @param int $minor Minor part of version number
     * @param string $lang Language code
     *
     * @return string|boolean Translation in JSON format if available, false otherwise
     */
    public function getLanguage($name, $major, $minor, $lang) {
        global $DB;

        // Check if this information has been saved previously into the cache.
        $langcache = \cache::make('core', 'h5p_content_type_translations');
        $library = new stdClass();
        $library->machinename = $name;
        $library->majorversion = $major;
        $library->minorversion = $minor;
        $librarykey = helper::get_cache_librarykey(core::record_to_string($library));
        $cachekey = "{$librarykey}/{$lang}";
        $translation = $langcache->get($cachekey);

        if ($translation !== false) {
            // When there is no translation we store it in the cache as `null`.
            // This API requires it be returned as `false`.
            if ($translation === null) {
                return false;
            }

            return $translation;
        }

        // Get the language file for this library.
        $params = [
            file_storage::COMPONENT,
            file_storage::LIBRARY_FILEAREA,
        ];
        $sqllike = $DB->sql_like('f.filepath', '?');
        $params[] = '%language%';

        $sql = "SELECT hl.id, f.pathnamehash
                  FROM {h5p_libraries} hl
             LEFT JOIN {files} f
                    ON hl.id = f.itemid AND f.component = ? AND f.filearea = ? AND $sqllike
                 WHERE ((hl.machinename = ? AND hl.majorversion = ? AND hl.minorversion = ?)
                   AND f.filename = ?)
              ORDER BY hl.patchversion DESC";
        $params[] = $name;
        $params[] = $major;
        $params[] = $minor;
        $params[] = $lang.'.json';

        $result = $DB->get_record_sql($sql, $params);

        if (empty($result)) {
            // Save the fact that there is no translation into the cache.
            // The cache API cannot handle setting a literal `false` value so conver to `null` instead.
            $langcache->set($cachekey, null);

            return false;
        }

        // Save translation into the cache, and return its content.
        $fs = get_file_storage();
        $file = $fs->get_file_by_hash($result->pathnamehash);
        $translation = $file->get_content();

        $langcache->set($cachekey, $translation);

        return $translation;
    }

    /**
     * Load a list of available language codes.
     *
     * Until translations is implemented, only returns the "en" language.
     *
     * @param string $machinename The machine readable name of the library(content type)
     * @param int $major Major part of version number
     * @param int $minor Minor part of version number
     *
     * @return array List of possible language codes
     */
    public function getAvailableLanguages($machinename, $major, $minor): array {
        global $DB;

        // Check if this information has been saved previously into the cache.
        $langcache = \cache::make('core', 'h5p_content_type_translations');
        $library = new stdClass();
        $library->machinename = $machinename;
        $library->majorversion = $major;
        $library->minorversion = $minor;
        $librarykey = helper::get_cache_librarykey(core::record_to_string($library));
        $languages = $langcache->get($librarykey);
        if ($languages) {
            // This contains a list of all of the available languages for the library.
            return $languages;
        }

        // Get the language files for this library.
        $params = [
            file_storage::COMPONENT,
            file_storage::LIBRARY_FILEAREA,
        ];
        $filepathsqllike = $DB->sql_like('f.filepath', '?');
        $params[] = '%language%';
        $filenamesqllike = $DB->sql_like('f.filename', '?');
        $params[] = '%.json';

        $sql = "SELECT DISTINCT f.filename
                           FROM {h5p_libraries} hl
                      LEFT JOIN {files} f
                             ON hl.id = f.itemid AND f.component = ? AND f.filearea = ?
                            AND $filepathsqllike AND $filenamesqllike
                          WHERE hl.machinename = ? AND hl.majorversion = ? AND hl.minorversion = ?";
        $params[] = $machinename;
        $params[] = $major;
        $params[] = $minor;

        $defaultcode = 'en';
        $languages = [];

        $results = $DB->get_recordset_sql($sql, $params);
        if ($results->valid()) {
            // Extract the code language from the JS language files.
            foreach ($results as $result) {
                if (!empty($result->filename)) {
                    $lang = substr($result->filename, 0, -5);
                    $languages[$lang] = $languages;
                }
            }
            $results->close();

            // Semantics is 'en' by default. It has to be added always.
            if (!array_key_exists($defaultcode, $languages)) {
                $languages = array_keys($languages);
                array_unshift($languages, $defaultcode);
            }
        } else {
            $results->close();
            $params = [
                'machinename' => $machinename,
                'majorversion' => $major,
                'minorversion' => $minor,
            ];
            if ($DB->record_exists('h5p_libraries', $params)) {
                // If the library exists (but it doesn't contain any language file), at least defaultcode should be returned.
                $languages[] = $defaultcode;
            }
        }

        // Save available languages into the cache.
        $langcache->set($librarykey, $languages);

        return $languages;
    }

    /**
     * "Callback" for mark the given file as a permanent file.
     *
     * Used when saving content that has new uploaded files.
     *
     * @param int $fileid
     */
    public function keepFile($fileid): void {
        // Temporal files will be removed on a task when they are in the "editor" file area and and are at least one day older.
    }

    /**
     * Return libraries details.
     *
     * Two use cases:
     * 1. No input, will list all the available content types.
     * 2. Libraries supported are specified, load additional data and verify
     * that the content types are available. Used by e.g. the Presentation Tool
     * Editor that already knows which content types are supported in its
     * slides.
     *
     * @param array $libraries List of library names + version to load info for.
     *
     * @return array List of all libraries loaded.
     */
    public function getLibraries($libraries = null): ?array {

        if ($libraries !== null) {
            // Get details for the specified libraries.
            $librariesin = [];
            $fields = 'title, runnable';

            foreach ($libraries as $library) {
                $params = [
                    'machinename' => $library->name,
                    'majorversion' => $library->majorVersion,
                    'minorversion' => $library->minorVersion
                ];

                $details = api::get_library_details($params, true, $fields);

                if ($details) {
                    $library->title = $details->title;
                    $library->runnable = $details->runnable;
                    $librariesin[] = $library;
                }
            }
        } else {
            $fields = 'id, machinename as name, title, majorversion, minorversion';
            $librariesin = api::get_contenttype_libraries($fields);
        }

        return $librariesin;
    }

    /**
     * Allow for other plugins to decide which styles and scripts are attached.
     *
     * This is useful for adding and/or modifying the functionality and look of
     * the content types.
     *
     * @param array $files List of files as objects with path and version as properties.
     * @param array $libraries List of libraries indexed by machineName with objects as values. The objects have majorVersion and
     *     minorVersion as properties.
     */
    public function alterLibraryFiles(&$files, $libraries): void {
        // This is to be implemented when the renderer is used.
    }

    /**
     * Saves a file or moves it temporarily.
     *
     * This is often necessary in order to validate and store uploaded or fetched H5Ps.
     *
     * @param string $data Uri of data that should be saved as a temporary file.
     * @param bool $movefile Can be set to TRUE to move the data instead of saving it.
     *
     * @return bool|object Returns false if saving failed or an object with path
     * of the directory and file that is temporarily saved.
     */
    public static function saveFileTemporarily($data, $movefile = false) {
        // This is to be implemented when the Hub client is used to upload libraries.
        return false;
    }

    /**
     * Marks a file for later cleanup.
     *
     * Useful when files are not instantly cleaned up. E.g. for files that are uploaded through the editor.
     *
     * @param int $file Id of file that should be cleaned up
     * @param int|null $contentid Content id of file
     */
    public static function markFileForCleanup($file, $contentid = null): ?int {
        // Temporal files will be removed on a task when they are in the "editor" file area and and are at least one day older.
        return null;
    }

    /**
     * Clean up temporary files
     *
     * @param string $filepath Path to file or directory
     */
    public static function removeTemporarilySavedFiles($filepath): void {
        // This is to be implemented when the Hub client is used to upload libraries.
    }
}

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
 * Class \core_h5p\editor_ajax
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>, base on code by Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use H5PEditorAjaxInterface;
use core\dml\table as dml_table;

/**
 * Moodle's implementation of the H5P Editor Ajax interface.
 *
 * Makes it possible for the editor's core ajax functionality to communicate with the
 * database used by Moodle.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>, base on code by Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_ajax implements H5PEditorAjaxInterface {

    /** The component for H5P. */
    public const EDITOR_AJAX_TOKEN = 'editorajax';

    /**
     * Gets latest library versions that exists locally
     *
     * @return array Latest version of all local libraries
     */
    public function getLatestLibraryVersions(): array {
        global $DB;

        $sql = "SELECT hl2.id, hl2.machinename as machine_name, hl2.title, hl2.majorversion as major_version,
                       hl2.minorversion AS minor_version, hl2.patchversion as patch_version, '' as has_icon, 0 as restricted
                  FROM {h5p_libraries} hl2
             LEFT JOIN {h5p_libraries} hl1
                        ON hl1.machinename = hl2.machinename
                        AND (hl2.majorversion < hl1.majorversion
                             OR (hl2.majorversion = hl1.majorversion
                                 AND hl2.minorversion < hl1.minorversion)
                            )
                 WHERE hl2.runnable = 1
                       AND hl1.majorversion is null
              ORDER BY hl2.title";

        return $DB->get_records_sql($sql);
    }

    /**
     * Get locally stored Content Type Cache.
     *
     * If machine name is provided it will only get the given content type from the cache.
     *
     * @param null|string $machinename
     *
     * @return mixed|null Returns results from querying the database
     */
    public function getContentTypeCache($machinename = null) {
        // This is to be implemented when the Hub client is used.
        return [];
    }

    /**
     * Gets recently used libraries for the current author
     *
     * @return array machine names. The first element in the array is the
     * most recently used.
     */
    public function getAuthorsRecentlyUsedLibraries(): array {
        // This is to be implemented when the Hub client is used.
        return [];
    }

    /**
     * Checks if the provided token is valid for this endpoint.
     *
     * @param string $token The token that will be validated for.
     *
     * @return bool True if successful validation
     */
    public function validateEditorToken($token): bool {
        return core::validToken(self::EDITOR_AJAX_TOKEN, $token);
    }

    /**
     * Get translations in one language for a list of libraries.
     *
     * @param array $libraries An array of libraries, in the form "<machineName> <majorVersion>.<minorVersion>
     * @param string $languagecode Language code
     *
     * @return array Translations in $languagecode available for libraries $libraries
     */
    public function getTranslations($libraries, $languagecode): array {
        $translations = [];
        $langcache = \cache::make('core', 'h5p_content_type_translations');

        $missing = [];
        foreach ($libraries as $libstring) {
            // Check if this library has been saved previously into the cache.
            $librarykey = helper::get_cache_librarykey($libstring);
            $cachekey = "{$librarykey}/{$languagecode}";
            $libtranslation = $langcache->get($cachekey);
            if ($libtranslation) {
                // The library has this language stored into the cache.
                $translations[$libstring] = $libtranslation;
            } else {
                // This language for the library hasn't been stored previously into the cache, so we need to get it from DB.
                $missing[] = $libstring;
            }
        }

        // Get all language files for libraries which aren't stored into the cache and merge them with the cache ones.
        return array_merge(
            $translations,
            $this->get_missing_translations($missing, $languagecode)
        );
    }

    /**
     * Get translation for $language for libraries in $missing.
     *
     * @param  array  $missing  An array of libraries, in the form "<machineName> <majorVersion>.<minorVersion>
     * @param  string $language Language code
     * @return array  Translations in $language available for libraries $missing
     */
    protected function get_missing_translations(array $missing, string $language): array {
        global $DB;

        if (empty($missing)) {
            return [];
        }

        $wheres = [];
        $params = [
            file_storage::COMPONENT,
            file_storage::LIBRARY_FILEAREA,
        ];
        $sqllike = $DB->sql_like('f.filepath', '?');
        $params[] = '%language%';

        foreach ($missing as $library) {
            $librarydata = core::libraryFromString($library);
            $wheres[] = '(h.machinename = ? AND h.majorversion = ? AND h.minorversion = ?)';
            $params[] = $librarydata['machineName'];
            $params[] = $librarydata['majorVersion'];
            $params[] = $librarydata['minorVersion'];
        }
        $params[] = "{$language}.json";
        $wheresql = implode(' OR ', $wheres);

        $filestable = new dml_table('files', 'f', 'f_');
        $filestableselect = $filestable->get_field_select();

        $libtable = new dml_table('h5p_libraries', 'h', 'h_');
        $libtableselect = $libtable->get_field_select();

        $sql = "SELECT {$filestableselect}, {$libtableselect}
                  FROM {h5p_libraries} h
             LEFT JOIN {files} f
                    ON h.id = f.itemid AND f.component = ?
                   AND f.filearea = ? AND $sqllike
                 WHERE ($wheresql) AND f.filename = ?";

        // Get the content of all these language files and put them into the translations array.
        $langcache = \cache::make('core', 'h5p_content_type_translations');
        $fs = get_file_storage();
        $translations = [];
        $results = $DB->get_recordset_sql($sql, $params);
        $toset = [];
        foreach ($results as $result) {
            $file = $fs->get_file_instance($filestable->extract_from_result($result));
            $library = $libtable->extract_from_result($result);
            $libstring = core::record_to_string($library);
            $librarykey = helper::get_cache_librarykey($libstring);
            $translations[$libstring] = $file->get_content();
            $cachekey = "{$librarykey}/{$language}";
            $toset[$cachekey] = $translations[$libstring];
        }
        $langcache->set_many($toset);

        $results->close();

        return $translations;
    }
}

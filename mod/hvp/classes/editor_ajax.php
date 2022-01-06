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
 * \mod_hvp\editor_ajax class
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../autoloader.php');

/**
 * Moodle's implementation of the H5P Editor Ajax interface.
 * Makes it possible for the editor's core ajax functionality to communicate with the
 * database used by Moodle.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_ajax implements \H5PEditorAjaxInterface {

    /**
     * Gets latest library versions that exists locally
     *
     * @return array Latest version of all local libraries
     * @throws \dml_exception
     */
    // @codingStandardsIgnoreLine
    public function getLatestLibraryVersions() {
        global $DB;

        $maxmajorversionsql = "
            SELECT hl.machine_name, MAX(hl.major_version) AS major_version
            FROM {hvp_libraries} hl
            WHERE hl.runnable = 1
            GROUP BY hl.machine_name";

        $maxminorversionsql = "
            SELECT hl2.machine_name, hl2.major_version, MAX(hl2.minor_version) AS minor_version
            FROM ({$maxmajorversionsql}) hl1
            JOIN {hvp_libraries} hl2
            ON hl1.machine_name = hl2.machine_name
            AND hl1.major_version = hl2.major_version
            GROUP BY hl2.machine_name, hl2.major_version";

        return $DB->get_records_sql("
            SELECT hl4.id, hl4.machine_name, hl4.title, hl4.major_version,
                hl4.minor_version, hl4.patch_version, hl4.has_icon, hl4.restricted
            FROM {hvp_libraries} hl4
            JOIN ({$maxminorversionsql}) hl3
            ON hl4.machine_name = hl3.machine_name
            AND hl4.major_version = hl3.major_version
            AND hl4.minor_version = hl3.minor_version"
        );
    }

    /**
     * Get locally stored Content Type Cache. If machine name is provided
     * it will only get the given content type from the cache
     *
     * @param null $machinename
     *
     * @return array|mixed|null|object Returns results from querying the database
     * @throws \dml_exception
     */
    // @codingStandardsIgnoreLine
    public function getContentTypeCache($machinename = null) {
        global $DB;

        if ($machinename) {
            return $DB->get_record_sql(
                "SELECT id, is_recommended
                   FROM {hvp_libraries_hub_cache}
                  WHERE machine_name = ?",
                array($machinename)
            );
        }

        return $DB->get_records("hvp_libraries_hub_cache");
    }

    /**
     * Gets recently used libraries for the current author
     *
     * @return array machine names. The first element in the array is the
     * most recently used.
     */
    // @codingStandardsIgnoreLine
    public function getAuthorsRecentlyUsedLibraries() {
        global $DB;
        global $USER;
        $recentlyused = array();

        $results = $DB->get_records_sql(
            "SELECT library_name, max(created_at) AS max_created_at
            FROM {hvp_events}
           WHERE type='content' AND sub_type = 'create' AND user_id = ?
        GROUP BY library_name
        ORDER BY max_created_at DESC", array($USER->id));

        foreach ($results as $row) {
            $recentlyused[] = $row->library_name;
        }

        return $recentlyused;
    }

    /**
     * Checks if the provided token is valid for this endpoint
     *
     * @param string $token The token that will be validated for.
     *
     * @return bool True if successful validation
     */
    // @codingStandardsIgnoreLine
    public function validateEditorToken($token) {
        return \H5PCore::validToken('editorajax', $token);
    }

    /**
     * Get translations for a language for a list of libraries
     *
     * @param array $libraries An array of libraries, in the form "<machineName> <majorVersion>.<minorVersion>
     * @param string $language_code
     *
     * @return array
     * @throws \dml_exception
     */
    public function getTranslations($libraries, $language_code) {
        global $DB;

        $translations = array();

        foreach ($libraries as $library) {
            $parsedLib = \H5PCore::libraryFromString($library);

            $sql         = "
                    SELECT language_json
                    FROM {hvp_libraries} lib
                      LEFT JOIN {hvp_libraries_languages} lang
                    ON lib.id = lang.library_id
                    WHERE lib.machine_name = :machine_name AND
                      lib.major_version = :major_version AND
                      lib.minor_version = :minor_version AND
                      lang.language_code = :language_code";
            $translation = $DB->get_field_sql($sql, array(
                'machine_name'  => $parsedLib['machineName'],
                'major_version' => $parsedLib['majorVersion'],
                'minor_version' => $parsedLib['minorVersion'],
                'language_code' => $language_code,
            ));

            if ($translation !== false) {
                $translations[$library] = $translation;
            }
        }

        return $translations;
    }
}

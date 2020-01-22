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
 * H5P core class.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filelib.php");

use H5PCore;
use H5PFrameworkInterface;
use stdClass;
use moodle_url;

/**
 * H5P core class, containing functions and storage shared by the other H5P classes.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core extends \H5PCore {

    /** @var array The array containing all the present libraries */
    protected $libraries;

    /**
     * Constructor for core_h5p/core.
     *
     * @param H5PFrameworkInterface $framework The frameworks implementation of the H5PFrameworkInterface
     * @param string|\H5PFileStorage $path The H5P file storage directory or class
     * @param string $url The URL to the file storage directory
     * @param string $language The language code. Defaults to english
     * @param boolean $export Whether export is enabled
     */
    public function __construct(H5PFrameworkInterface $framework, $path, string $url, string $language = 'en',
            bool $export = false) {

        parent::__construct($framework, $path, $url, $language, $export);

        // Aggregate the assets by default.
        $this->aggregateAssets = true;
    }

    /**
     * Get the path to the dependency.
     *
     * @param array $dependency An array containing the information of the requested dependency library
     * @return string The path to the dependency library
     */
    protected function getDependencyPath(array $dependency): string {
        $library = $this->find_library($dependency);

        return "libraries/{$library->id}/{$library->machinename}-{$library->majorversion}.{$library->minorversion}";
    }

    /**
     * Get the paths to the content dependencies.
     *
     * @param int $id The H5P content ID
     * @return array An array containing the path of each content dependency
     */
    public function get_dependency_roots(int $id): array {
        $roots = [];
        $dependencies = $this->h5pF->loadContentDependencies($id);
        $context = \context_system::instance();
        foreach ($dependencies as $dependency) {
            $library = $this->find_library($dependency);
            $roots[self::libraryToString($dependency, true)] = (moodle_url::make_pluginfile_url(
                $context->id,
                'core_h5p',
                'libraries',
                $library->id,
                "/" . self::libraryToString($dependency, true),
                ''
            ))->out(false);
        }

        return $roots;
    }

    /**
     * Get a particular dependency library.
     *
     * @param array $dependency An array containing information of the dependency library
     * @return stdClass|null The library object if the library dependency exists, null otherwise
     */
    protected function find_library(array $dependency): ?\stdClass {
        global $DB;
        if (null === $this->libraries) {
            $this->libraries = $DB->get_records('h5p_libraries');
        }

        $major = $dependency['majorVersion'];
        $minor = $dependency['minorVersion'];
        $patch = $dependency['patchVersion'];

        foreach ($this->libraries as $library) {
            if ($library->machinename !== $dependency['machineName']) {
                continue;
            }

            if ($library->majorversion != $major) {
                continue;
            }
            if ($library->minorversion != $minor) {
                continue;
            }
            if ($library->patchversion != $patch) {
                continue;
            }

            return $library;
        }

        return null;
    }

    /**
     * Get core JavaScript files.
     *
     * @return array The array containg urls of the core JavaScript files
     */
    public static function get_scripts(): array {
        global $CFG;
        $cachebuster = '?ver='.$CFG->jsrev;
        $liburl = $CFG->wwwroot . '/lib/h5p/';
        $urls = [];

        foreach (self::$scripts as $script) {
            $urls[] = new moodle_url($liburl . $script . $cachebuster);
        }
        $urls[] = new moodle_url("/h5p/js/h5p_overrides.js");

        return $urls;
    }

    /**
     * Fetch and install the latest H5P content types libraries from the official H5P repository.
     * If the latest version of a content type library is present in the system, nothing is done for that content type.
     *
     * @return stdClass
     */
    public function fetch_latest_content_types(): ?\stdClass {

        $contenttypes = self::get_latest_content_types();
        if (!empty($contenttypes->error)) {
            return $contenttypes;
        }

        $typesinstalled = [];

        $factory = new factory();
        $framework = $factory->get_framework();

        foreach ($contenttypes->contentTypes as $type) {
            // Don't fetch content types that require a higher H5P core API version.
            if (!$this->is_required_core_api($type->coreApiVersionNeeded)) {
                continue;
            }

            $library = [
                'machineName' => $type->id,
                'majorVersion' => $type->version->major,
                'minorVersion' => $type->version->minor,
                'patchVersion' => $type->version->patch
            ];

            $shoulddownload = true;
            if ($framework->getLibraryId($type->id, $type->version->major, $type->version->minor)) {
                if (!$framework->isPatchedLibrary($library)) {
                    $shoulddownload = false;
                }
            }

            if ($shoulddownload) {
                $installed['id'] = $this->fetch_content_type($library);
                if ($installed['id']) {
                    $installed['name'] = $librarykey = \H5PCore::libraryToString($library);
                    $typesinstalled[] = $installed;
                }
            }
        }

        $result = new stdClass();
        $result->error = '';
        $result->typesinstalled = $typesinstalled;

        return $result;
    }

    /**
     * Given an H5P content type machine name, fetch and install the required library from the official H5P repository.
     *
     * @param array $library Library machineName, majorversion and minorversion.
     * @return int|null Returns the id of the content type library installed, null otherwise.
     */
    public function fetch_content_type(array $library): ?int {
        $factory = new factory();

        // Get a temp path to download the content type.
        $temppath = make_request_directory();
        $tempfile = "{$temppath}/" . $library['machineName'] . ".h5p";

        // Download the latest content type from the H5P official repository.
        $fs = get_file_storage();
        $file = $fs->create_file_from_url(
            (object) [
                'component' => 'core_h5p',
                'filearea' => 'library_sources',
                'itemid' => 0,
                'contextid' => (\context_system::instance())->id,
                'filepath' => '/',
                'filename' => $library['machineName'],
            ],
            $this->get_api_endpoint($library['machineName']),
            null,
            true
        );

        if (!$file) {
            return null;
        }

        helper::save_h5p($factory, $file, (object) [], false, true);

        $file->delete();

        $librarykey = static::libraryToString($library);
        $libraryid = $factory->get_storage()->h5pC->librariesJsonData[$librarykey]["libraryId"];

        return $libraryid;
    }

    /**
     * Get H5P endpoints.
     *
     * If $library is null, moodle_url is the endpoint of the latest version of the H5P content types. If library is the
     * machine name of a content type, moodle_url is the endpoint to download the content type.
     *
     * @param string|null $library The machineName of the library whose endpoint is requested.
     * @return moodle_url The endpoint moodle_url object.
     */
    public function get_api_endpoint(?string $library): moodle_url {
        $h5purl = \H5PHubEndpoints::createURL(\H5PHubEndpoints::CONTENT_TYPES ) . $library;
        return new moodle_url($h5purl);
    }

    /**
     * Get the latest version of the H5P content types available in the official repository.
     *
     * @return stdClass An object with 2 properties:
     *     - string error: error message when there is any problem, empty otherwise
     *     - array contentTypes: an object for each H5P content type with its information
     */
    public function get_latest_content_types(): \stdClass {
        // Get the latest content-types json.
        $postdata = ['uuid' => 'foo'];
        $endpoint = $this->get_api_endpoint(null);
        $request = download_file_content($endpoint, null, $postdata, true);

        if (!empty($request->error) || $request->status != '200' || empty($request->results)) {
            if (empty($request->error)) {
                $request->error = get_string('fetchtypesfailure', 'core_h5p');
            }
            return $request;
        }

        $contenttypes = json_decode($request->results);
        $contenttypes->error = '';

        return $contenttypes;
    }

    /**
     * Checks that the required H5P core API version or higher is installed.
     *
     * @param stdClass $coreapi Object with properties major and minor for the core API version required.
     * @return bool True if the required H5P core API version is installed. False if not.
     */
    public function is_required_core_api($coreapi): bool {
        if (isset($coreapi) && !empty($coreapi)) {
            if (($coreapi->major > H5PCore::$coreApi['majorVersion']) ||
                (($coreapi->major == H5PCore::$coreApi['majorVersion']) && ($coreapi->minor > H5PCore::$coreApi['minorVersion']))) {
                return false;
            }
        }
        return true;
    }

}

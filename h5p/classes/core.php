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

use H5PCore;
use H5PFrameworkInterface;
use stdClass;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

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
}

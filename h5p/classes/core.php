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
 * H5P player class.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use H5PCore;
use stdClass;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P player class, for displaying any local H5P content.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core extends \H5PCore {

    protected $libraries;

    protected function getDependencyPath(array $dependency): string {
        $library = $this->find_library($dependency);

        return "libraries/{$library->id}/{$library->machinename}-{$library->majorversion}.{$library->minorversion}";
    }

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

    protected function find_library($dependency): \stdClass {
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

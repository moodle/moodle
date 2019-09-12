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
 * Generator for the core_h5p subsystem.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generator for the core_h5p subsystem.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_h5p_generator extends \component_generator_base {

    /**
     * Convenience function to create a file.
     *
     * @param  string $file path to a file.
     * @param  string $content file content.
     */
    public function create_file(string $file, string $content=''): void {
        $handle = fopen($file, 'w+');
        // File content is not relevant.
        if (empty($content)) {
            $content = hash("md5", $file);
        }
        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     * Creates the file record. Currently used for the cache tests.
     *
     * @param string $type    Either 'scripts' or 'styles'.
     * @param string $path    Path to the file in the file system.
     * @param string $version Not really needed at the moment.
     */
    protected function add_libfile_to_array(string $type, string $path, string $version, &$files): void {
        $files[$type][] = (object) [
            'path' => $path,
            'version' => "?ver=$version"
        ];
    }

    /**
     * Create the necessary files and return an array structure for a library.
     *
     * @param  string $uploaddirectory Base directory for the library.
     * @param  int    $libraryid       Library id.
     * @param  string $machinename     Name for this library.
     * @param  int    $majorversion    Major version (any number will do).
     * @param  int    $minorversion    Minor version (any number will do).
     * @return array A list of library data and files that the core API will understand.
     */
    public function create_library(string $uploaddirectory, int $libraryid, string $machinename, int $majorversion, int
$minorversion): array {
        /** @var array $files an array used in the cache tests. */
        $files = ['scripts' => [], 'styles' => []];

        check_dir_exists($uploaddirectory . '/' . 'scripts');
        check_dir_exists($uploaddirectory . '/' . 'styles');

        $jsonfile = $uploaddirectory . '/' . 'library.json';
        $jsfile = $uploaddirectory . '/' . 'scripts/testlib.min.js';
        $cssfile = $uploaddirectory . '/' . 'styles/testlib.min.css';
        $this->create_file($jsonfile);
        $this->create_file($jsfile);
        $this->create_file($cssfile);

        $lib = [
            'title' => 'Test lib',
            'description' => 'Test library description',
            'majorVersion' => $majorversion,
            'minorVersion' => $minorversion,
            'patchVersion' => 2,
            'machineName' => $machinename,
            'preloadedJs' => [
                [
                    'path' => 'scripts' . '/' . 'testlib.min.js'
                ]
            ],
            'preloadedCss' => [
                [
                    'path' => 'styles' . '/' . 'testlib.min.css'
                ]
            ],
            'uploadDirectory' => $uploaddirectory,
            'libraryId' => $libraryid
        ];

        $version = "{$majorversion}.{$minorversion}.2";
        $libname = "{$machinename}-{$majorversion}.{$minorversion}";
        $path = '/' . 'libraries' . '/' . $libraryid . '/' . $libname . '/' . 'scripts' . '/' . 'testlib.min.js';
        $this->add_libfile_to_array('scripts', $path, $version, $files);
        $path = '/' . 'libraries' . '/' . $libraryid .'/' . $libname . '/' . 'styles' . '/' . 'testlib.min.css';
        $this->add_libfile_to_array('styles', $path, $version, $files);

        return [$lib, $files];
    }

}
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

use core_h5p\local\library\autoloader;
use core_h5p\core;
use core_h5p\player;
use core_h5p\factory;
use core_xapi\local\statement\item_activity;

/**
 * Generator for the core_h5p subsystem.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_h5p_generator extends \component_generator_base {

    /** Url pointing to webservice plugin file. */
    public const WSPLUGINFILE = 0;
    /** Url pointing to token plugin file. */
    public const TOKENPLUGINFILE = 1;
    /** Url pointing to plugin file. */
    public const PLUGINFILE = 2;

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
        $files[$type][] = (object)[
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
     * @param  array  $langs           Languages to be included into the library.
     * @return array A list of library data and files that the core API will understand.
     */
    public function create_library(string $uploaddirectory, int $libraryid, string $machinename, int $majorversion,
            int $minorversion, ?array $langs = []): array {
        // Array $files used in the cache tests.
        $files = ['scripts' => [], 'styles' => [], 'language' => []];

        check_dir_exists($uploaddirectory . '/' . 'scripts');
        check_dir_exists($uploaddirectory . '/' . 'styles');
        if (!empty($langs)) {
            check_dir_exists($uploaddirectory . '/' . 'language');
        }

        $jsonfile = $uploaddirectory . '/' . 'library.json';
        $jsfile = $uploaddirectory . '/' . 'scripts/testlib.min.js';
        $cssfile = $uploaddirectory . '/' . 'styles/testlib.min.css';
        $this->create_file($jsonfile);
        $this->create_file($jsfile);
        $this->create_file($cssfile);
        foreach ($langs as $lang => $value) {
            $jsonfile = $uploaddirectory . '/' . 'language/' . $lang . '.json';
            $this->create_file($jsonfile, $value);
        }

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
        foreach ($langs as $lang => $notused) {
            $path = '/' . 'libraries' . '/' . $libraryid . '/' . $libname . '/' . 'language' . '/' . $lang . '.json';
            $this->add_libfile_to_array('language', $path, $version, $files);
        }

        return [$lib, $files];
    }

    /**
     * Save the library files on the filesystem.
     *
     * @param stdClss $lib The library data
     */
    private function save_library(stdClass $lib) {
        // Get a temp path.
        $filestorage = new \core_h5p\file_storage();
        $temppath = $filestorage->getTmpPath();

        // Create and save the library files on the filesystem.
        $basedirectorymain = $temppath . '/' . $lib->machinename . '-' .
            $lib->majorversion . '.' . $lib->minorversion;

        list($library, $libraryfiles) = $this->create_library($basedirectorymain, $lib->id, $lib->machinename,
            $lib->majorversion, $lib->minorversion);

        $filestorage->saveLibrary($library);
    }

    /**
     * Populate H5P database tables with relevant data to simulate the process of adding H5P content.
     *
     * @param bool $createlibraryfiles Whether to create and store library files on the filesystem
     * @param array|null $filerecord The file associated to the H5P entry.
     * @return stdClass An object representing the added H5P records
     */
    public function generate_h5p_data(bool $createlibraryfiles = false, ?array $filerecord = null): stdClass {
        // Create libraries.
        $mainlib = $libraries[] = $this->create_library_record('MainLibrary', 'Main Lib', 1, 0, 1, '', null,
            'http://tutorial.org', 'http://example.org');
        $lib1 = $libraries[] = $this->create_library_record('Library1', 'Lib1', 2, 0, 1, '', null, null,  'http://example.org');
        $lib2 = $libraries[] = $this->create_library_record('Library2', 'Lib2', 2, 1, 1, '', null, 'http://tutorial.org');
        $lib3 = $libraries[] = $this->create_library_record('Library3', 'Lib3', 3, 2, 1, '', null, null, null, true, 0);
        $lib4 = $libraries[] = $this->create_library_record('Library4', 'Lib4', 1, 1);
        $lib5 = $libraries[] = $this->create_library_record('Library5', 'Lib5', 1, 3);

        if ($createlibraryfiles) {
            foreach ($libraries as $lib) {
                // Create and save the library files on the filesystem.
                $this->save_library($lib);
            }
        }

        // Create h5p content.
        $h5p = $this->create_h5p_record($mainlib->id, null, null, $filerecord);
        // Create h5p content library dependencies.
        $this->create_contents_libraries_record($h5p, $mainlib->id);
        $this->create_contents_libraries_record($h5p, $lib1->id);
        $this->create_contents_libraries_record($h5p, $lib2->id);
        $this->create_contents_libraries_record($h5p, $lib3->id);
        $this->create_contents_libraries_record($h5p, $lib4->id);
        // Create library dependencies for $mainlib.
        $this->create_library_dependency_record($mainlib->id, $lib1->id);
        $this->create_library_dependency_record($mainlib->id, $lib2->id);
        $this->create_library_dependency_record($mainlib->id, $lib3->id);
        // Create library dependencies for $lib1.
        $this->create_library_dependency_record($lib1->id, $lib2->id);
        $this->create_library_dependency_record($lib1->id, $lib3->id);
        $this->create_library_dependency_record($lib1->id, $lib4->id);
        // Create library dependencies for $lib3.
        $this->create_library_dependency_record($lib3->id, $lib5->id);

        return (object) [
            'h5pcontent' => (object) array(
                'h5pid' => $h5p,
                'contentdependencies' => array($mainlib, $lib1, $lib2, $lib3, $lib4)
            ),
            'mainlib' => (object) array(
                'data' => $mainlib,
                'dependencies' => array($lib1, $lib2, $lib3)
            ),
            'lib1' => (object) array(
                'data' => $lib1,
                'dependencies' => array($lib2, $lib3, $lib4)
            ),
            'lib2' => (object) array(
                'data' => $lib2,
                'dependencies' => array()
            ),
            'lib3' => (object) array(
                'data' => $lib3,
                'dependencies' => array($lib5)
            ),
            'lib4' => (object) array(
                'data' => $lib4,
                'dependencies' => array()
            ),
            'lib5' => (object) array(
                'data' => $lib5,
                'dependencies' => array()
            ),
        ];
    }

    /**
     * Create a record in the h5p_libraries database table.
     *
     * @param string $machinename The library machine name
     * @param string $title The library's name
     * @param int $majorversion The library's major version
     * @param int $minorversion The library's minor version
     * @param int $patchversion The library's patch version
     * @param string $semantics Json describing the content structure for the library
     * @param string $addto The plugin configuration data
     * @param string $tutorial The tutorial URL
     * @param string $examlpe The example URL
     * @param bool $enabled Whether the library is enabled or not
     * @param int $runnable Whether the library is runnable (1) or not (0)
     * @return stdClass An object representing the added library record
     */
    public function create_library_record(string $machinename, string $title, int $majorversion = 1,
            int $minorversion = 0, int $patchversion = 1, string $semantics = '', string $addto = null,
            string $tutorial = null, string $example = null, bool $enabled = true, int $runnable = 1): stdClass {
        global $DB;

        $content = [
            'machinename' => $machinename,
            'title' => $title,
            'majorversion' => $majorversion,
            'minorversion' => $minorversion,
            'patchversion' => $patchversion,
            'runnable' => $runnable,
            'fullscreen' => 1,
            'preloadedjs' => 'js/example.js',
            'preloadedcss' => 'css/example.css',
            'droplibrarycss' => '',
            'semantics' => $semantics,
            'addto' => $addto,
            'tutorial' => $tutorial,
            'example' => $example,
            'enabled' => $enabled,
        ];

        $libraryid = $DB->insert_record('h5p_libraries', $content);

        return $DB->get_record('h5p_libraries', ['id' => $libraryid]);
    }

    /**
     * Create a record in the h5p database table.
     *
     * @param int $mainlibid The ID of the content's main library
     * @param string $jsoncontent The content in json format
     * @param string $filtered The filtered content parameters
     * @param array|null $filerecord The file associated to the H5P entry.
     * @return int The ID of the added record
     */
    public function create_h5p_record(int $mainlibid, string $jsoncontent = null, string $filtered = null,
            ?array $filerecord = null): int {
        global $DB;

        if (!$jsoncontent) {
            $jsoncontent = json_encode(
                array(
                    'text' => '<p>Dummy text<\/p>\n',
                    'questions' => '<p>Test question<\/p>\n'
                )
            );
        }

        if (!$filtered) {
            $filtered = json_encode(
                array(
                    'text' => 'Dummy text',
                    'questions' => 'Test question'
                )
            );
        }

        // Load the H5P file into DB.
        $pathnamehash = sha1('pathname');
        $contenthash = sha1('content');
        if ($filerecord) {
            $fs = get_file_storage();
            if (!$fs->get_file(
                    $filerecord['contextid'],
                    $filerecord['component'],
                    $filerecord['filearea'],
                    $filerecord['itemid'],
                    $filerecord['filepath'],
                    $filerecord['filename'])) {
                $file = $fs->create_file_from_string($filerecord, $jsoncontent);
                $pathnamehash = $file->get_pathnamehash();
                $contenthash = $file->get_contenthash();
                if (array_key_exists('addxapistate', $filerecord) && $filerecord['addxapistate']) {
                    // Save some xAPI state associated to this H5P content.
                    $params = [
                        'component' => $filerecord['component'],
                        'activity' => item_activity::create_from_id($filerecord['contextid']),
                    ];
                    global $CFG;
                    require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
                    \core_xapi\test_helper::create_state($params, true);
                }
            }
        }

        return $DB->insert_record(
            'h5p',
            [
                'jsoncontent' => $jsoncontent,
                'displayoptions' => 8,
                'mainlibraryid' => $mainlibid,
                'timecreated' => time(),
                'timemodified' => time(),
                'filtered' => $filtered,
                'pathnamehash' => $pathnamehash,
                'contenthash' => $contenthash,
            ]
        );
    }

    /**
     * Create a record in the h5p_contents_libraries database table.
     *
     * @param string $h5pid The ID of the H5P content
     * @param int $libid The ID of the library
     * @param string $dependencytype The dependency type
     * @return int The ID of the added record
     */
    public function create_contents_libraries_record(string $h5pid, int $libid,
            string $dependencytype = 'preloaded'): int {
        global $DB;

        return $DB->insert_record(
            'h5p_contents_libraries',
            array(
                'h5pid' => $h5pid,
                'libraryid' => $libid,
                'dependencytype' => $dependencytype,
                'dropcss' => 0,
                'weight' => 1
            )
        );
    }

    /**
     * Create a record in the h5p_library_dependencies database table.
     *
     * @param int $libid The ID of the library
     * @param int $requiredlibid The ID of the required library
     * @param string $dependencytype The dependency type
     * @return int The ID of the added record
     */
    public function create_library_dependency_record(int $libid, int $requiredlibid,
            string $dependencytype = 'preloaded'): int {
        global $DB;

        return $DB->insert_record(
            'h5p_library_dependencies',
            array(
                'libraryid' => $libid,
                'requiredlibraryid' => $requiredlibid,
                'dependencytype' => $dependencytype
            )
        );
    }

    /**
     * Create H5P content type records in the h5p_libraries database table.
     *
     * @param array $typestonotinstall H5P content types that should not be installed
     * @param core $core h5p_test_core instance required to use the exttests URL
     * @return array Data of the content types not installed.
     *
     * @throws invalid_response_exception If request to get the latest content types fails (usually due to a transient error)
     */
    public function create_content_types(array $typestonotinstall, core $core): array {
        global $DB;

        autoloader::register();

        // Get info of latest content types versions.
        $response = $core->get_latest_content_types();
        if (!empty($response->error)) {
            throw new invalid_response_exception($response->error);
        }

        $installedtypes = 0;

        // Fake installation of all other H5P content types.
        foreach ($response->contentTypes as $contenttype) {
            // Don't install pending content types.
            if (in_array($contenttype->id, $typestonotinstall)) {
                continue;
            }
            $library = [
                'machinename' => $contenttype->id,
                'majorversion' => $contenttype->version->major,
                'minorversion' => $contenttype->version->minor,
                'patchversion' => $contenttype->version->patch,
                'runnable' => 1,
                'coremajor' => $contenttype->coreApiVersionNeeded->major,
                'coreminor' => $contenttype->coreApiVersionNeeded->minor
            ];
            $DB->insert_record('h5p_libraries', (object) $library);
            $installedtypes++;
        }

        return [$installedtypes, count($typestonotinstall)];
    }

    /**
     * Add a record on files table for a file that belongs to
     *
     * @param string $file File name and path inside the filearea.
     * @param string $filearea The filearea in which the file is ("editor" or "content").
     * @param int $contentid Id of the H5P content to which the file belongs. null if the file is in the editor.
     *
     * @return stored_file;
     * @throws coding_exception
     */
    public function create_content_file(string $file, string $filearea, int $contentid = 0): stored_file {
        global $USER;

        $filepath = '/'.dirname($file).'/';
        $filename = basename($file);

        if (($filearea === 'content') && ($contentid == 0)) {
            throw new coding_exception('Files belonging to an H5P content must specify the H5P content id');
        }

        if ($filearea === 'draft') {
            $usercontext = \context_user::instance($USER->id);
            $context = $usercontext->id;
            $component = 'user';
            $itemid = 0;
        } else {
            $systemcontext = context_system::instance();
            $context = $systemcontext->id;
            $component = \core_h5p\file_storage::COMPONENT;
            $itemid = $contentid;
        }

        $content = 'fake content';

        $filerecord = array(
            'contextid' => $context,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
        );

        $fs = new file_storage();
        return $fs->create_file_from_string($filerecord, $content);
    }

    /**
     * Create a fake export H5P deployed file.
     *
     * @param string $filename Name of the H5P file to deploy.
     * @param int $contextid Context id of the H5P activity.
     * @param string $component component.
     * @param string $filearea file area.
     * @param int $typeurl Type of url to create the export url plugin file.
     * @return array return deployed file information.
     */
    public function create_export_file(string $filename, int $contextid,
        string $component,
        string $filearea,
        int $typeurl = self::WSPLUGINFILE): array {
        global $CFG;

        // We need the autoloader for H5P player.
        autoloader::register();

        $path = $CFG->dirroot.'/h5p/tests/fixtures/'.$filename;
        $filerecord = [
            'contextid' => $contextid,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        // Load the h5p file into DB.
        $fs = get_file_storage();
        if (!$fs->get_file($contextid, $component, $filearea, $filerecord['itemid'], $filerecord['filepath'], $filename)) {
            $fs->create_file_from_pathname($filerecord, $path);
        }

        // Make the URL to pass to the player.
        if ($typeurl == self::WSPLUGINFILE) {
            $url = \moodle_url::make_webservice_pluginfile_url(
                $filerecord['contextid'],
                $filerecord['component'],
                $filerecord['filearea'],
                $filerecord['itemid'],
                $filerecord['filepath'],
                $filerecord['filename']
            );
        } else {
            $includetoken = false;
            if ($typeurl == self::TOKENPLUGINFILE) {
                $includetoken = true;
            }
            $url = \moodle_url::make_pluginfile_url(
                $filerecord['contextid'],
                $filerecord['component'],
                $filerecord['filearea'],
                $filerecord['itemid'],
                $filerecord['filepath'],
                $filerecord['filename'],
                false,
                $includetoken
            );
        }

        $config = new stdClass();
        $h5pplayer = new player($url->out(false), $config);
        // We need to add assets to page to create the export file.
        $h5pplayer->add_assets_to_page();

        // Call the method. We need the id of the new H5P content.
        $rc = new \ReflectionClass(player::class);
        $rcp = $rc->getProperty('h5pid');
        $h5pid = $rcp->getValue($h5pplayer);

        // Get the info export file.
        $factory = new factory();
        $core = $factory->get_core();
        $content = $core->loadContent($h5pid);
        $slug = $content['slug'] ? $content['slug'] . '-' : '';
        $exportfilename = "{$slug}{$h5pid}.h5p";
        $fileh5p = $core->fs->get_export_file($exportfilename);
        $deployedfile = [];
        $deployedfile['filename'] = $fileh5p->get_filename();
        $deployedfile['filepath'] = $fileh5p->get_filepath();
        $deployedfile['mimetype'] = $fileh5p->get_mimetype();
        $deployedfile['filesize'] = $fileh5p->get_filesize();
        $deployedfile['timemodified'] = $fileh5p->get_timemodified();

        // Create the url depending the request was made through typeurl.
        if ($typeurl == self::WSPLUGINFILE) {
            $url  = \moodle_url::make_webservice_pluginfile_url(
                $fileh5p->get_contextid(),
                $fileh5p->get_component(),
                $fileh5p->get_filearea(),
                '',
                '',
                $fileh5p->get_filename()
            );
        } else {
            $includetoken = false;
            if ($typeurl == self::TOKENPLUGINFILE) {
                $includetoken = true;
            }
            $url = \moodle_url::make_pluginfile_url(
                $fileh5p->get_contextid(),
                $fileh5p->get_component(),
                $fileh5p->get_filearea(),
                '',
                '',
                $fileh5p->get_filename(),
                false,
                $includetoken
            );
        }
        $deployedfile['fileurl'] = $url->out(false);

        return $deployedfile;
    }
}

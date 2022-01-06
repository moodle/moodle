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
 * The mod_hvp file storage
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/hvp/library/h5p-file-storage.interface.php');

/**
 * The mod_hvp file storage class.
 *
 * @package    mod_hvp
 * @since      Moodle 2.7
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class file_storage implements \H5PFileStorage {

    /**
     * Store the library folder.
     *
     * @param array $library
     *  Library properties
     */
    // @codingStandardsIgnoreLine
    public function saveLibrary($library) {
        // Libraries are stored in a system context.
        $context = \context_system::instance();
        $options = array(
            'contextid' => $context->id,
            'component' => 'mod_hvp',
            'filearea' => 'libraries',
            'itemid' => 0,
            'filepath' => '/' . \H5PCore::libraryToString($library, true) . '/',
        );

        // Remove any old existing library files.
        self::deleteFileTree($context->id, $options['filearea'], $options['filepath']);

        // Move library folder.
        self::readFileTree($library['uploadDirectory'], $options);
    }

    /**
     * Store the content folder.
     *
     * @param string $source
     *  Path on file system to content directory.
     * @param array $content
     *  Content properties
     */
    // @codingStandardsIgnoreLine
    public function saveContent($source, $content) {
        // Remove any old content.
        $this->deleteContent($content);

        // Contents are stored in a course context.
        $context = \context_module::instance($content['coursemodule']);
        $options = array(
            'contextid' => $context->id,
            'component' => 'mod_hvp',
            'filearea' => 'content',
            'itemid' => $content['id'],
            'filepath' => '/',
        );

        // Move content folder.
        self::readFileTree($source, $options);
    }

    /**
     * Remove content folder.
     *
     * @param array $content
     *  Content properties
     */
    // @codingStandardsIgnoreLine
    public function deleteContent($content) {
        $context = \context_module::instance($content['coursemodule']);
        self::deleteFileTree($context->id, 'content', '/', $content['id']);
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function cloneContent($id, $newid) {
        // Not implemented in Moodle.
    }

    /**
     * Get path to a new unique tmp folder.
     *
     * @return string Path
     */
    // @codingStandardsIgnoreLine
    public function getTmpPath() {
        global $CFG;

        return $CFG->tempdir . uniqid('/hvp-');
    }

    /**
     * Fetch content folder and save in target directory.
     *
     * @param int $id
     *  Content identifier
     * @param string $target
     *  Where the content folder will be saved
     */
    // @codingStandardsIgnoreLine
    public function exportContent($id, $target) {
        $cm = \get_coursemodule_from_instance('hvp', $id);
        $context = \context_module::instance($cm->id);
        self::exportFileTree($target, $context->id, 'content', '/', $id);
    }

    /**
     * Fetch library folder and save in target directory.
     *
     * @param array $library
     *  Library properties
     * @param string $target
     *  Where the library folder will be saved
     */
    // @codingStandardsIgnoreLine
    public function exportLibrary($library, $target) {
        $folder = \H5PCore::libraryToString($library, true);
        $context = \context_system::instance();
        self::exportFileTree("{$target}/{$folder}", $context->id, 'libraries', "/{$folder}/");
    }

    /**
     * Save export in file system
     *
     * @param string $source
     *  Path on file system to temporary export file.
     * @param string $filename
     *  Name of export file.
     */
    // @codingStandardsIgnoreLine
    public function saveExport($source, $filename) {
        global $COURSE;

        // Remove old export.
        $this->deleteExport($filename);

        // Create record.
        $context = \context_course::instance($COURSE->id);
        $record = array(
            'contextid' => $context->id,
            'component' => 'mod_hvp',
            'filearea' => 'exports',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename
        );

        // Store new export.
        $fs = get_file_storage();
        $fs->create_file_from_pathname($record, $source);
    }

    /**
     * Get file object for given export file.
     *
     * @param string $filename
     * @return stdClass Moodle file object
     */
    // @codingStandardsIgnoreLine
    private function getExportFile($filename) {
        global $COURSE;
        $context = \context_course::instance($COURSE->id);

        // Check if file exists.
        $fs = get_file_storage();
        return $fs->get_file($context->id, 'mod_hvp', 'exports', 0, '/', $filename);
    }

    /**
     * Removes given export file
     *
     * @param string $filename
     */
    // @codingStandardsIgnoreLine
    public function deleteExport($filename) {
        $file = $this->getExportFile($filename);
        if ($file) {
            // Remove old export.
            $file->delete();
        }
    }

    /**
     * Check if the given export file exists
     *
     * @param string $filename
     * @return boolean
     */
    // @codingStandardsIgnoreLine
    public function hasExport($filename) {
        return !!$this->getExportFile($filename);
    }

    /**
     * Will concatenate all JavaScrips and Stylesheets into two files in order
     * to improve page performance.
     *
     * @param array $files
     *  A set of all the assets required for content to display
     * @param string $key
     *  Hashed key for cached asset
     */
    // @codingStandardsIgnoreLine
    public function cacheAssets(&$files, $key) {
        $context = \context_system::instance();
        $fs = get_file_storage();

        foreach ($files as $type => $assets) {
            if (empty($assets)) {
                continue;
            }

            $content = '';
            foreach ($assets as $asset) {
                // Find location of asset.
                $location = array();
                preg_match('/^\/(libraries|development)(.+\/)([^\/]+)$/', $asset->path, $location);

                // Locate file.
                $file = $fs->get_file($context->id, 'mod_hvp', $location[1], 0, $location[2], $location[3]);

                // Get file content and concatenate.
                if ($type === 'scripts') {
                    $content .= $file->get_content() . ";\n";
                } else {
                    // Rewrite relative URLs used inside stylesheets.
                    $content .= preg_replace_callback(
                            '/url\([\'"]?([^"\')]+)[\'"]?\)/i',
                            function ($matches) use ($location) {
                                if (preg_match("/^(data:|([a-z0-9]+:)?\/)/i", $matches[1]) === 1) {
                                    return $matches[0]; // Not relative, skip.
                                }
                                return 'url("../' . $location[1] . $location[2] . $matches[1] . '")';
                            },
                            $file->get_content()) . "\n";
                }
            }

            // Create new file for cached assets.
            $ext = ($type === 'scripts' ? 'js' : 'css');
            $fileinfo = array(
                'contextid' => $context->id,
                'component' => 'mod_hvp',
                'filearea' => 'cachedassets',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => "{$key}.{$ext}"
            );

            // Store concatenated content.
            $fs->create_file_from_string($fileinfo, $content);
            $files[$type] = array((object) array(
                'path' => "/cachedassets/{$key}.{$ext}",
                'version' => ''
            ));
        }
    }

    /**
     * Will check if there are cache assets available for content.
     *
     * @param string $key
     *  Hashed key for cached asset
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function getCachedAssets($key) {
        $context = \context_system::instance();
        $fs = get_file_storage();

        $files = array();

        $js = $fs->get_file($context->id, 'mod_hvp', 'cachedassets', 0, '/', "{$key}.js");
        if ($js) {
            $files['scripts'] = array((object) array(
                'path' => "/cachedassets/{$key}.js",
                'version' => ''
            ));
        }

        $css = $fs->get_file($context->id, 'mod_hvp', 'cachedassets', 0, '/', "{$key}.css");
        if ($css) {
            $files['styles'] = array((object) array(
                'path' => "/cachedassets/{$key}.css",
                'version' => ''
            ));
        }

        return empty($files) ? null : $files;
    }

    /**
     * Remove the aggregated cache files.
     *
     * @param array $keys
     *   The hash keys of removed files
     */
    // @codingStandardsIgnoreLine
    public function deleteCachedAssets($keys) {
        $context = \context_system::instance();
        $fs = get_file_storage();

        foreach ($keys as $hash) {
            foreach (array('js', 'css') as $type) {
                $cachedasset = $fs->get_file($context->id, 'mod_hvp', 'cachedassets', 0, '/', "{$hash}.{$type}");
                if ($cachedasset) {
                    $cachedasset->delete();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function getContent($filepath) {
        // Grab context and file storage.
        $context = \context_system::instance();
        $fs      = get_file_storage();

        // Find location of file.
        $location = [];
        preg_match('/^\/(libraries|development|cachedassets)(.*\/)([^\/]+)$/', $filepath, $location);

        // Locate file.
        $file = $fs->get_file($context->id, 'mod_hvp', $location[1], 0, $location[2], $location[3]);
        if (!$file) {
            throw new \file_serving_exception(
                'Could not retrieve the requested file, check your file permissions.'
            );
        }

        // Return content.
        return $file->get_content();
    }

    /**
     * Save files uploaded through the editor.
     *
     * @param \H5peditorFile $file
     * @param int $contentid
     * @param \stdClass $contextid Course Context ID
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    public function saveFile($file, $contentid, $contextid = null) {
        if ($contentid !== 0) {
            // Grab cm context.
            $cm = \get_coursemodule_from_instance('hvp', $contentid);
            $context = \context_module::instance($cm->id);
            $contextid = $context->id;
        } else if ($contextid === null) {
            // Check for context id in params.
            $contextid = optional_param('contextId', null, PARAM_INT);
        }

        // Files not yet related to any activities are stored in a course context
        // These are temporary files and should not be part of backups.

        $record = array(
            'contextid' => $contextid,
            'component' => 'mod_hvp',
            'filearea' => $contentid === 0 ? 'editor' : 'content',
            'itemid' => $contentid,
            'filepath' => '/' . $file->getType() . 's/',
            'filename' => $file->getName()
        );
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_pathname($record, $_FILES['file']['tmp_name']);

        return $storedfile->get_id();
    }

    /**
     * Copy a file from another content or editor tmp dir.
     * Used when copy pasting content in H5P.
     *
     * @param string $file path + name
     * @param string|int $fromid Content ID or 'editor' string
     * @param stdClass $tocontent Target Content
     */
    // @codingStandardsIgnoreLine
    public function cloneContentFile($file, $fromid, $tocontent) {

        // Determine source file area and item id.
        if ($fromid === 'editor') {
            $sourcefilearea = 'editor';
            if (empty($tocontent->instance)) {
                $sourceitemid = \context_course::instance($tocontent->course);
            } else {
                $sourceitemid = \context_module::instance($tocontent->coursemodule);
            }
        } else {
            $sourcefilearea = 'content';
            $sourceitemid   = $fromid;
        };

        // Check to see if source exist.
        $sourcefile = $this->getFile($sourcefilearea, $sourceitemid, $file);
        if ($sourcefile === false) {
            return; // Nothing to copy from.
        }

        // Check to make sure source doesn't exist already.
        if ($this->getFile('content', $tocontent, $file) !== false) {
            return; // File exists, no need to copy.
        }

        // Grab context for CM.
        $context = \context_module::instance($tocontent->coursemodule);

        // Create new file record.
        $record = [
            'contextid' => $context->id,
            'component' => 'mod_hvp',
            'filearea'  => 'content',
            'itemid'    => $tocontent->id,
            'filepath'  => $this->getFilepath($file),
            'filename'  => $this->getFilename($file),
        ];
        $fs = get_file_storage();
        $fs->create_file_from_storedfile($record, $sourcefile);
    }

    /**
     * Checks to see if content has the given file.
     * Used when saving content.
     *
     * @param string $file path + name
     * @param stdClass $content
     * @return string|int File ID or null if not found
     */
    // @codingStandardsIgnoreLine
    public function getContentFile($file, $content) {
        $file = $this->getFile('content', $content, $file);
        return ($file === false ? null : $file->get_id());
    }

    /**
     * Remove content files that are no longer used.
     * Used when saving content.
     *
     * @param string $file path + name
     * @param stdClass $content
     */
    // @codingStandardsIgnoreLine
    public function removeContentFile($file, $content) {
        $file = $this->getFile('content', $content, $file);
        if ($file !== false) {
            $file->delete();
        }
    }

    /**
     * Copies files from tmp folder to Moodle storage.
     *
     * @param string $source
     *  Path to source directory
     * @param array $options
     *  For Moodle's file record
     * @throws \Exception Unable to copy
     */
    // @codingStandardsIgnoreLine
    private static function readFileTree($source, $options) {
        $dir = opendir($source);
        if ($dir === false) {
            trigger_error('Unable to open directory ' . $source, E_USER_WARNING);
            throw new \Exception('unabletocopy');
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..') && $file != '.git' && $file != '.gitignore') {
                if (is_dir($source . DIRECTORY_SEPARATOR . $file)) {
                    $suboptions = $options;
                    $suboptions['filepath'] .= $file . '/';
                    self::readFileTree($source . '/' . $file, $suboptions);
                } else {
                    $record = $options;
                    $record['filename'] = $file;
                    $fs = get_file_storage();
                    $fs->create_file_from_pathname($record, $source . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Copies files from Moodle storage to temporary folder.
     *
     * @param string $target
     *  Path to temporary folder
     * @param int $contextid
     *  Moodle context where the files are found
     * @param string $filearea
     *  Moodle file area
     * @param string $filepath
     *  Moodle file path
     * @param int $itemid
     *  Optional Moodle item ID
     */
    // @codingStandardsIgnoreLine
    private static function exportFileTree($target, $contextid, $filearea, $filepath, $itemid = 0) {
        // Make sure target folder exists.
        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }

        // Read source files.
        $fs = get_file_storage();
        $files = $fs->get_directory_files($contextid, 'mod_hvp', $filearea, $itemid, $filepath, true);

        foreach ($files as $file) {
            // Correct target path for file.
            $path = $target . str_replace($filepath, '/', $file->get_filepath());

            if ($file->is_directory()) {
                // Create directory.
                $path = rtrim($path, '/');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
            } else {
                // Copy file.
                $file->copy_content_to($path . $file->get_filename());
            }
        }
    }

    /**
     * Recursive removal of given filepath.
     *
     * @param int $contextid
     * @param string $filearea
     * @param string $filepath
     * @param int $itemid
     */
    // @codingStandardsIgnoreLine
    private static function deleteFileTree($contextid, $filearea, $filepath, $itemid = 0) {
        $fs = get_file_storage();
        if ($filepath === '/') {
            // Remove complete file area.
            $fs->delete_area_files($contextid, 'mod_hvp', $filearea, $itemid);
            return;
        }

        // Look up files and remove.
        $files = $fs->get_directory_files($contextid, 'mod_hvp', $filearea, $itemid, $filepath, true);
        foreach ($files as $file) {
            $file->delete();
        }

        // Remove root dir.
        $file = $fs->get_file($contextid, 'mod_hvp', $filearea, $itemid, $filepath, '.');
        if ($file) {
            $file->delete();
        }
    }

    /**
     * Help make it easy to load content files.
     *
     * @param string $filearea
     * @param int|object $itemid
     * @param string $file path + name
     *
     * @return \stored_file|bool
     */
    // @codingStandardsIgnoreLine
    private function getFile($filearea, $itemid, $file) {
        if ($filearea === 'editor') {
            // Itemid is actually cm or course context.
            $context = $itemid;
            $itemid = 0;
        } else if (is_object($itemid)) {
            // Grab CM context from item.
            $context = \context_module::instance($itemid->coursemodule);
            $itemid = $itemid->id;
        } else {
            // Use item ID to find CM context.
            $cm = \get_coursemodule_from_instance('hvp', $itemid);
            $context = \context_module::instance($cm->id);
        }

        // Load file.
        $fs = get_file_storage();
        return $fs->get_file($context->id, 'mod_hvp', $filearea, $itemid, $this->getFilepath($file), $this->getFilename($file));
    }

    /**
     * Extract Moodle compatible filepath
     *
     * @param string $file
     * @return string With slashes
     */
    // @codingStandardsIgnoreLine
    private function getFilepath($file) {
        return '/' . dirname($file) . '/';
    }

    /**
     * Extract filename from filepath string
     *
     * @param string $file
     * @return string Without slashes
     */
    // @codingStandardsIgnoreLine
    private function getFilename($file) {
        return basename($file);
    }

    /**
     * Checks if a file exists
     *
     * @method fileExists
     * @param  string     $filearea [description]
     * @param  string     $filepath [description]
     * @param  string     $filename [description]
     * @return boolean
     */
    // @codingStandardsIgnoreLine
    public static function fileExists($contextid, $filearea, $filepath, $filename) {
        // Check if file exists.
        $fs = get_file_storage();
        return ($fs->get_file($contextid, 'mod_hvp', $filearea, 0, $filepath, $filename) !== false);
    }

    /**
     * Check if server setup has write permission to
     * the required folders
     *
     * @return bool true if server has the proper write access
     */
    // @codingStandardsIgnoreLine
    public function hasWriteAccess() {
        global $CFG;

        if (!is_dir($CFG->dataroot)) {
            trigger_error('Path is not a directory ' . $CFG->dataroot, E_USER_WARNING);
            return false;
        }

        if (!is_writable($CFG->dataroot)) {
            trigger_error('Unable to write to ' . $CFG->dataroot . ' – check directory permissions –', E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * Copy a content from one directory to another. Defaults to cloning
     * content from the current temporary upload folder to the editor path.
     *
     * @param string $source path to source directory
     * @param string $contentid path of target directory. Defaults to editor path
     *
     * @return object|null Object containing h5p json and content json data
     */
    // @codingStandardsIgnoreLine
    public function moveContentDirectory($source, $contentid = null) {
        if ($source === null) {
            return null;
        }

        // Default to 0 (editor).
        if (!isset($contentid)) {
            $contentid = 0;
        }

        // Find content context.
        if ($contentid > 0) {
            // Grab cm context.
            $cm = \get_coursemodule_from_instance('hvp', $contentid);
            $context = \context_module::instance($cm->id);
            $contextid = $context->id;
        }

        // Get context from parameters.
        if (!isset($contextid)) {
            $contextid = required_param('contextId', PARAM_INT);
        }

        // Get h5p and content json.
        $contentsource = $source . DIRECTORY_SEPARATOR . 'content';

        // Move all temporary content files to editor.
        $contentfiles = array_diff(scandir($contentsource), array('.', '..', 'content.json'));
        foreach ($contentfiles as $file) {
            if (is_dir("{$contentsource}/{$file}")) {
                self::moveFileTree("{$contentsource}/{$file}", $contextid, $contentid);
            } else {
                self::moveFile("{$contentsource}/{$file}", $contextid, $contentid);
            }
        }

        // TODO: Return list of all files so they can be marked as temporary. JI-366.
    }

    /**
     * Move a single file to editor
     *
     * @param string $sourcefile Path to source fil
     * @param int $contextid Id of context
     * @param int $contentid Id of content, 0 if editor
     */
    // @codingStandardsIgnoreLine
    private static function moveFile($sourcefile, $contextid, $contentid) {
        $fs = get_file_storage();

        $pathparts = pathinfo($sourcefile);
        $filename  = $pathparts['basename'];
        $filepath  = $pathparts['dirname'];
        $foldername = basename($filepath);

        if ($contentid > 0) {
            // Create file record for content.
            $record = array(
                'contextid' => $contextid,
                'component' => 'mod_hvp',
                'filearea' => $contentid > 0 ? 'content' : 'editor',
                'itemid' => $contentid,
                'filepath' => '/' . $foldername . '/',
                'filename' => $filename
            );
        } else {
            // Create file record for editor.
            $record = array(
                'contextid' => $contextid,
                'component' => 'mod_hvp',
                'filearea' => 'editor',
                'itemid' => 0,
                'filepath' => '/' . $foldername . '/',
                'filename' => $filename
            );
        }

        $sourcedata = file_get_contents($sourcefile);

        // Check if file already exists.
        $fileexists = $fs->file_exists($record['contextid'], 'mod_hvp',
            $record['filearea'], $record['itemid'], $record['filepath'],
            $record['filename']
        );

        if ($fileexists) {
            // Delete it to make sure that it is replaced with correct content.
            $file = $fs->get_file($record['contextid'], 'mod_hvp',
                $record['filearea'], $record['itemid'], $record['filepath'],
                $record['filename']
            );
            if ($file) {
                $file->delete();
            }
        }

        $fs->create_file_from_string($record, $sourcedata);
    }

    /**
     * Move a complete file tree to the editor
     *
     * @param string $sourcefiletree Path of file tree that should be moved
     * @param int $contextid Id of context
     * @param int $contentid Id of content, 0 for editor
     *
     * @throws \Exception
     */
    // @codingStandardsIgnoreLine
    private static function moveFileTree($sourcefiletree, $contextid, $contentid) {
        $dir = opendir($sourcefiletree);
        if ($dir === false) {
            trigger_error('Unable to open directory ' . $sourcefiletree, E_USER_WARNING);
            throw new \Exception('unabletocopy');
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..') && $file != '.git' && $file != '.gitignore') {
                if (is_dir("{$sourcefiletree}/{$file}")) {
                    self::moveFileTree("{$sourcefiletree}/{$file}", $contextid, $contentid);
                } else {
                    self::moveFile("{$sourcefiletree}/{$file}", $contextid, $contentid);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Check if the library has a presave.js in the root folder
     *
     * @param string $libraryname
     * @param string $developmentpath
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function hasPresave($libraryname, $developmentpath = null) {
        // TODO: Implement.
        return false;
    }

    /**
     * Check if upgrades script exist for library.
     *
     * @param string $machineName
     * @param int $majorVersion
     * @param int $minorVersion
     * @return string Relative path
     */
    // @codingStandardsIgnoreLine
    public function getUpgradeScript($machinename, $majorversion, $minorversion) {
        $context = \context_system::instance();
        $fs = get_file_storage();
        $area = 'libraries';
        $path = "/{$machinename}-{$majorversion}.{$minorversion}/";
        $file = 'upgrades.js';
        if ($fs->get_file($context->id, 'mod_hvp', $area, 0, $path, $file)) {
            return "/{$area}{$path}{$file}";
        } else {
            return null;
        }
    }

    /**
     * Store the given stream into the given file.
     *
     * @param string $path
     * @param string $file
     * @param resource $stream
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function saveFileFromZip($path, $file, $stream) {
        $filepath = $path . '/' . $file;

        // Make sure the directory exists first.
        $matches = array();
        preg_match('/(.+)\/[^\/]*$/', $filepath, $matches);
        // Recursively make directories.
        if (!file_exists($matches[1])) {
            mkdir($matches[1], 0777, true);
        }

        // Store in local storage folder.
        return file_put_contents($filepath, $stream);
    }
}

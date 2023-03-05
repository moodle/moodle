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
 * Class \core_h5p\file_storage.
 *
 * @package    core_h5p
 * @copyright  2019 Victor Deniz <victor@moodle.com>, base on code by Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use stored_file;
use Moodle\H5PCore;
use Moodle\H5peditorFile;
use Moodle\H5PFileStorage;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Class to handle storage and export of H5P Content.
 *
 * @package    core_h5p
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_storage implements H5PFileStorage {

    /** The component for H5P. */
    public const COMPONENT   = 'core_h5p';
    /** The library file area. */
    public const LIBRARY_FILEAREA = 'libraries';
    /** The content file area */
    public const CONTENT_FILEAREA = 'content';
    /** The cached assest file area. */
    public const CACHED_ASSETS_FILEAREA = 'cachedassets';
    /** The export file area */
    public const EXPORT_FILEAREA = 'export';
    /** The export css file area */
    public const CSS_FILEAREA = 'css';
    /** The icon filename */
    public const ICON_FILENAME = 'icon.svg';

    /**
     * @var \context $context Currently we use the system context everywhere.
     * Don't feel forced to keep it this way in the future.
     */
    protected $context;

    /** @var \file_storage $fs File storage. */
    protected $fs;

    /**
     * Initial setup for file_storage.
     */
    public function __construct() {
        // Currently everything uses the system context.
        $this->context = \context_system::instance();
        $this->fs = get_file_storage();
    }

    /**
     * Stores a H5P library in the Moodle filesystem.
     *
     * @param array $library Library properties.
     */
    public function saveLibrary($library) {
        $options = [
            'contextid' => $this->context->id,
            'component' => self::COMPONENT,
            'filearea' => self::LIBRARY_FILEAREA,
            'filepath' => '/' . H5PCore::libraryToFolderName($library) . '/',
            'itemid' => $library['libraryId'],
        ];

        // Easiest approach: delete the existing library version and copy the new one.
        $this->delete_library($library);
        $this->copy_directory($library['uploadDirectory'], $options);
    }

    /**
     * Delete library folder.
     *
     * @param array $library
     */
    public function deleteLibrary($library) {
        // Although this class had a method (delete_library()) for removing libraries before this was added to the interface,
        // it's not safe to call it from here because looking at the place where it's called, it's not clear what are their
        // expectation. This method will be implemented once more information will be added to the H5P technical doc.
    }


    /**
     * Store the content folder.
     *
     * @param string $source Path on file system to content directory.
     * @param array $content Content properties
     */
    public function saveContent($source, $content) {
        $options = [
                'contextid' => $this->context->id,
                'component' => self::COMPONENT,
                'filearea' => self::CONTENT_FILEAREA,
                'itemid' => $content['id'],
                'filepath' => '/',
        ];

        $this->delete_directory($this->context->id, self::COMPONENT, self::CONTENT_FILEAREA, $content['id']);
        // Copy content directory into Moodle filesystem.
        $this->copy_directory($source, $options);
    }

    /**
     * Remove content folder.
     *
     * @param array $content Content properties
     */
    public function deleteContent($content) {

        $this->delete_directory($this->context->id, self::COMPONENT, self::CONTENT_FILEAREA, $content['id']);
    }

    /**
     * Creates a stored copy of the content folder.
     *
     * @param string $id Identifier of content to clone.
     * @param int $newid The cloned content's identifier
     */
    public function cloneContent($id, $newid) {
        // Not implemented in Moodle.
    }

    /**
     * Get path to a new unique tmp folder.
     * Please note this needs to not be a directory.
     *
     * @return string Path
     */
    public function getTmpPath(): string {
        return make_request_directory() . '/' . uniqid('h5p-');
    }

    /**
     * Fetch content folder and save in target directory.
     *
     * @param int $id Content identifier
     * @param string $target Where the content folder will be saved
     */
    public function exportContent($id, $target) {
        $this->export_file_tree($target, $this->context->id, self::CONTENT_FILEAREA, '/', $id);
    }

    /**
     * Fetch library folder and save in target directory.
     *
     * @param array $library Library properties
     * @param string $target Where the library folder will be saved
     */
    public function exportLibrary($library, $target) {
        $folder = H5PCore::libraryToFolderName($library);
        $this->export_file_tree($target . '/' . $folder, $this->context->id, self::LIBRARY_FILEAREA,
                '/' . $folder . '/', $library['libraryId']);
    }

    /**
     * Save export in file system
     *
     * @param string $source Path on file system to temporary export file.
     * @param string $filename Name of export file.
     */
    public function saveExport($source, $filename) {
        global $USER;

        // Remove old export.
        $this->deleteExport($filename);

        $filerecord = [
            'contextid' => $this->context->id,
            'component' => self::COMPONENT,
            'filearea' => self::EXPORT_FILEAREA,
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
            'userid' => $USER->id
        ];
        $this->fs->create_file_from_pathname($filerecord, $source);
    }

    /**
     * Removes given export file
     *
     * @param string $filename filename of the export to delete.
     */
    public function deleteExport($filename) {
        $file = $this->get_export_file($filename);
        if ($file) {
            $file->delete();
        }
    }

    /**
     * Check if the given export file exists
     *
     * @param string $filename The export file to check.
     * @return boolean True if the export file exists.
     */
    public function hasExport($filename) {
        return !!$this->get_export_file($filename);
    }

    /**
     * Will concatenate all JavaScrips and Stylesheets into two files in order
     * to improve page performance.
     *
     * @param array $files A set of all the assets required for content to display
     * @param string $key Hashed key for cached asset
     */
    public function cacheAssets(&$files, $key) {

        foreach ($files as $type => $assets) {
            if (empty($assets)) {
                continue;
            }

            // Create new file for cached assets.
            $ext = ($type === 'scripts' ? 'js' : 'css');
            $filename = $key . '.' . $ext;
            $fileinfo = [
                'contextid' => $this->context->id,
                'component' => self::COMPONENT,
                'filearea' => self::CACHED_ASSETS_FILEAREA,
                'itemid' => 0,
                'filepath' => '/',
                'filename' => $filename
            ];

            // Store concatenated content.
            $this->fs->create_file_from_string($fileinfo, $this->concatenate_files($assets, $type, $this->context));
            $files[$type] = [
                (object) [
                    'path' => '/' . self::CACHED_ASSETS_FILEAREA . '/' . $filename,
                    'version' => ''
                ]
            ];
        }
    }

    /**
     * Will check if there are cache assets available for content.
     *
     * @param string $key Hashed key for cached asset
     * @return array
     */
    public function getCachedAssets($key) {
        $files = [];

        $js = $this->fs->get_file($this->context->id, self::COMPONENT, self::CACHED_ASSETS_FILEAREA, 0, '/', "{$key}.js");
        if ($js && $js->get_filesize() > 0) {
            $files['scripts'] = [
                (object) [
                    'path' => '/' . self::CACHED_ASSETS_FILEAREA . '/' . "{$key}.js",
                    'version' => ''
                ]
            ];
        }

        $css = $this->fs->get_file($this->context->id, self::COMPONENT, self::CACHED_ASSETS_FILEAREA, 0, '/', "{$key}.css");
        if ($css && $css->get_filesize() > 0) {
            $files['styles'] = [
                (object) [
                    'path' => '/' . self::CACHED_ASSETS_FILEAREA . '/' . "{$key}.css",
                    'version' => ''
                ]
            ];
        }

        return empty($files) ? null : $files;
    }

    /**
     * Remove the aggregated cache files.
     *
     * @param array $keys The hash keys of removed files
     */
    public function deleteCachedAssets($keys) {

        if (empty($keys)) {
            return;
        }

        foreach ($keys as $hash) {
            foreach (['js', 'css'] as $type) {
                $cachedasset = $this->fs->get_file($this->context->id, self::COMPONENT, self::CACHED_ASSETS_FILEAREA, 0, '/',
                        "{$hash}.{$type}");
                if ($cachedasset) {
                    $cachedasset->delete();
                }
            }
        }
    }

    /**
     * Read file content of given file and then return it.
     *
     * @param string $filepath
     * @return string contents
     */
    public function getContent($filepath) {
        list(
            'filearea' => $filearea,
            'filepath' => $filepath,
            'filename' => $filename,
            'itemid' => $itemid
        ) = $this->get_file_elements_from_filepath($filepath);

        if (!$itemid) {
            throw new \file_serving_exception('Could not retrieve the requested file, check your file permissions.');
        }

        // Locate file.
        $file = $this->fs->get_file($this->context->id, self::COMPONENT, $filearea, $itemid, $filepath, $filename);

        // Return content.
        return $file->get_content();
    }

    /**
     * Save files uploaded through the editor.
     *
     * @param H5peditorFile $file
     * @param int $contentid
     *
     * @return int The id of the saved file.
     */
    public function saveFile($file, $contentid) {
        global $USER;

        $context = $this->context->id;
        $component = self::COMPONENT;
        $filearea = self::CONTENT_FILEAREA;
        if ($contentid === 0) {
            $usercontext = \context_user::instance($USER->id);
            $context = $usercontext->id;
            $component = 'user';
            $filearea = 'draft';
        }

        $record = array(
            'contextid' => $context,
            'component' => $component,
            'filearea' => $filearea,
            'itemid' => $contentid,
            'filepath' => '/' . $file->getType() . 's/',
            'filename' => $file->getName()
        );

        $storedfile = $this->fs->create_file_from_pathname($record, $_FILES['file']['tmp_name']);

        return $storedfile->get_id();
    }

    /**
     * Copy a file from another content or editor tmp dir.
     * Used when copy pasting content in H5P.
     *
     * @param string $file path + name
     * @param string|int $fromid Content ID or 'editor' string
     * @param \stdClass $tocontent Target Content
     *
     * @return void
     */
    public function cloneContentFile($file, $fromid, $tocontent): void {
        // Determine source filearea and itemid.
        if ($fromid === 'editor') {
            $sourcefilearea = 'draft';
            $sourceitemid = 0;
        } else {
            $sourcefilearea = self::CONTENT_FILEAREA;
            $sourceitemid = (int)$fromid;
        }

        $filepath = '/' . dirname($file) . '/';
        $filename = basename($file);

        // Check to see if source exists.
        $sourcefile = $this->get_file($sourcefilearea, $sourceitemid, $file);
        if ($sourcefile === null) {
            return; // Nothing to copy from.
        }

        // Check to make sure that file doesn't exist already in target.
        $targetfile = $this->get_file(self::CONTENT_FILEAREA, $tocontent->id, $file);
        if ( $targetfile !== null) {
            return; // File exists, no need to copy.
        }

        // Create new file record.
        $record = [
            'contextid' => $this->context->id,
            'component' => self::COMPONENT,
            'filearea' => self::CONTENT_FILEAREA,
            'itemid' => $tocontent->id,
            'filepath' => $filepath,
            'filename' => $filename,
        ];

        $this->fs->create_file_from_storedfile($record, $sourcefile);
    }

    /**
     * Copy content from one directory to another.
     * Defaults to cloning content from the current temporary upload folder to the editor path.
     *
     * @param string $source path to source directory
     * @param string $contentid Id of content
     *
     */
    public function moveContentDirectory($source, $contentid = null) {
        $contentidint = (int)$contentid;

        if ($source === null) {
            return;
        }

        // Get H5P and content json.
        $contentsource = $source . '/content';

        // Move all temporary content files to editor.
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($contentsource, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $it->rewind();
        while ($it->valid()) {
            $item = $it->current();
            $pathname = $it->getPathname();
            if (!$item->isDir() && !($item->getFilename() === 'content.json')) {
                $this->move_file($pathname, $contentidint);
            }
            $it->next();
        }
    }

    /**
     * Get the file URL or given library and then return it.
     *
     * @param int $itemid
     * @param string $machinename
     * @param int $majorversion
     * @param int $minorversion
     * @return string url or false if the file doesn't exist
     */
    public function get_icon_url(int $itemid, string $machinename, int $majorversion, int $minorversion) {
        $filepath = '/' . "{$machinename}-{$majorversion}.{$minorversion}" . '/';
        if ($file = $this->fs->get_file(
            $this->context->id,
            self::COMPONENT,
            self::LIBRARY_FILEAREA,
            $itemid,
            $filepath,
            self::ICON_FILENAME)
        ) {
            $iconurl  = \moodle_url::make_pluginfile_url(
                $this->context->id,
                self::COMPONENT,
                self::LIBRARY_FILEAREA,
                $itemid,
                $filepath,
                $file->get_filename());

            // Return image URL.
            return $iconurl->out();
        }

        return false;
    }

    /**
     * Checks to see if an H5P content has the given file.
     *
     * @param string $file File path and name.
     * @param int $content Content id.
     *
     * @return int|null File ID or NULL if not found
     */
    public function getContentFile($file, $content): ?int {
        if (is_object($content)) {
            $content = $content->id;
        }
        $contentfile = $this->get_file(self::CONTENT_FILEAREA, $content, $file);

        return ($contentfile === null ? null : $contentfile->get_id());
    }

    /**
     * Remove content files that are no longer used.
     *
     * Used when saving content.
     *
     * @param string $file File path and name.
     * @param int $contentid Content id.
     *
     * @return void
     */
    public function removeContentFile($file, $contentid): void {
        // Although the interface defines $contentid as int, object given in H5peditor::processParameters.
        if (is_object($contentid)) {
            $contentid = $contentid->id;
        }
        $existingfile = $this->get_file(self::CONTENT_FILEAREA, $contentid, $file);
        if ($existingfile !== null) {
            $existingfile->delete();
        }
    }

    /**
     * Check if server setup has write permission to
     * the required folders
     *
     * @return bool True if server has the proper write access
     */
    public function hasWriteAccess() {
        // Moodle has access to the files table which is where all of the folders are stored.
        return true;
    }

    /**
     * Check if the library has a presave.js in the root folder
     *
     * @param string $libraryname
     * @param string $developmentpath
     * @return bool
     */
    public function hasPresave($libraryname, $developmentpath = null) {
        return false;
    }

    /**
     * Check if upgrades script exist for library.
     *
     * @param string $machinename
     * @param int $majorversion
     * @param int $minorversion
     * @return string Relative path
     */
    public function getUpgradeScript($machinename, $majorversion, $minorversion) {
        $path = '/' . "{$machinename}-{$majorversion}.{$minorversion}" . '/';
        $file = 'upgrade.js';
        $itemid = $this->get_itemid_for_file(self::LIBRARY_FILEAREA, $path, $file);
        if ($this->fs->get_file($this->context->id, self::COMPONENT, self::LIBRARY_FILEAREA, $itemid, $path, $file)) {
            return '/' . self::LIBRARY_FILEAREA . $path. $file;
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
     * @return bool|int
     */
    public function saveFileFromZip($path, $file, $stream) {
        $fullpath = $path . '/' . $file;
        check_dir_exists(pathinfo($fullpath, PATHINFO_DIRNAME));
        return file_put_contents($fullpath, $stream);
    }

    /**
     * Deletes a library from the file system.
     *
     * @param  array $library Library details
     */
    public function delete_library(array $library): void {
        global $DB;

        // A library ID of false would result in all library files being deleted, which we don't want. Return instead.
        if (empty($library['libraryId'])) {
            return;
        }

        $areafiles = $this->fs->get_area_files($this->context->id, self::COMPONENT, self::LIBRARY_FILEAREA, $library['libraryId']);
        $this->delete_directory($this->context->id, self::COMPONENT, self::LIBRARY_FILEAREA, $library['libraryId']);
        $librarycache = \cache::make('core', 'h5p_library_files');
        foreach ($areafiles as $file) {
            if (!$DB->record_exists('files', array('contenthash' => $file->get_contenthash(),
                                                   'component' => self::COMPONENT,
                                                   'filearea' => self::LIBRARY_FILEAREA))) {
                $librarycache->delete($file->get_contenthash());
            }
        }
    }

    /**
     * Remove an H5P directory from the filesystem.
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area or all areas in context if not specified
     * @param int $itemid item ID or all files if not specified
     */
    private function delete_directory(int $contextid, string $component, string $filearea, int $itemid): void {

        $this->fs->delete_area_files($contextid, $component, $filearea, $itemid);
    }

    /**
     * Copy an H5P directory from the temporary directory into the file system.
     *
     * @param  string $source  Temporary location for files.
     * @param  array  $options File system information.
     */
    private function copy_directory(string $source, array $options): void {
        $librarycache = \cache::make('core', 'h5p_library_files');
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST);

        $root = $options['filepath'];

        $it->rewind();
        while ($it->valid()) {
            $item = $it->current();
            $subpath = $it->getSubPath();
            if (!$item->isDir()) {
                $options['filename'] = $it->getFilename();
                if (!$subpath == '') {
                    $options['filepath'] = $root . $subpath . '/';
                } else {
                    $options['filepath'] = $root;
                }

                $file = $this->fs->create_file_from_pathname($options, $item->getPathName());

                if ($options['filearea'] == self::LIBRARY_FILEAREA) {
                    if (!$librarycache->has($file->get_contenthash())) {
                        $librarycache->set($file->get_contenthash(), file_get_contents($item->getPathName()));
                    }
                }
            }
            $it->next();
        }
    }

    /**
     * Copies files from storage to temporary folder.
     *
     * @param string $target Path to temporary folder
     * @param int $contextid context where the files are found
     * @param string $filearea file area
     * @param string $filepath file path
     * @param int $itemid Optional item ID
     */
    private function export_file_tree(string $target, int $contextid, string $filearea, string $filepath, int $itemid = 0): void {
        // Make sure target folder exists.
        check_dir_exists($target);

        // Read source files.
        $files = $this->fs->get_directory_files($contextid, self::COMPONENT, $filearea, $itemid, $filepath, true);

        $librarycache = \cache::make('core', 'h5p_library_files');

        foreach ($files as $file) {
            $path = $target . str_replace($filepath, DIRECTORY_SEPARATOR, $file->get_filepath());
            if ($file->is_directory()) {
                check_dir_exists(rtrim($path));
            } else {
                if ($filearea == self::LIBRARY_FILEAREA) {
                    $cachedfile = $librarycache->get($file->get_contenthash());
                    if (empty($cachedfile)) {
                        $file->copy_content_to($path . $file->get_filename());
                        $librarycache->set($file->get_contenthash(), file_get_contents($path . $file->get_filename()));
                    } else {
                        file_put_contents($path . $file->get_filename(), $cachedfile);
                    }
                } else {
                    $file->copy_content_to($path . $file->get_filename());
                }
            }
        }
    }

    /**
     * Adds all files of a type into one file.
     *
     * @param  array    $assets  A list of files.
     * @param  string   $type    The type of files in assets. Either 'scripts' or 'styles'
     * @param  \context $context Context
     * @return string All of the file content in one string.
     */
    private function concatenate_files(array $assets, string $type, \context $context): string {
        $content = '';
        foreach ($assets as $asset) {
            // Find location of asset.
            list(
                'filearea' => $filearea,
                'filepath' => $filepath,
                'filename' => $filename,
                'itemid' => $itemid
            ) = $this->get_file_elements_from_filepath($asset->path);

            if ($itemid === false) {
                continue;
            }

            // Locate file.
            $file = $this->fs->get_file($context->id, self::COMPONENT, $filearea, $itemid, $filepath, $filename);

            // Get file content and concatenate.
            if ($type === 'scripts') {
                $content .= $file->get_content() . ";\n";
            } else {
                // Rewrite relative URLs used inside stylesheets.
                $content .= preg_replace_callback(
                    '/url\([\'"]?([^"\')]+)[\'"]?\)/i',
                    function ($matches) use ($filearea, $filepath, $itemid) {
                        if (preg_match("/^(data:|([a-z0-9]+:)?\/)/i", $matches[1]) === 1) {
                            return $matches[0]; // Not relative, skip.
                        }
                        // Find "../" in matches[1].
                        // If it exists, we have to remove "../".
                        // And switch the last folder in the filepath for the first folder in $matches[1].
                        // For instance:
                        // $filepath: /H5P.Question-1.4/styles/
                        // $matches[1]: ../images/plus-one.svg
                        // We want to avoid this: H5P.Question-1.4/styles/ITEMID/../images/minus-one.svg
                        // We want this: H5P.Question-1.4/images/ITEMID/minus-one.svg.
                        if (preg_match('/\.\.\//', $matches[1], $pathmatches)) {
                            $path = preg_split('/\//', $filepath, -1, PREG_SPLIT_NO_EMPTY);
                            $pathfilename = preg_split('/\//', $matches[1], -1, PREG_SPLIT_NO_EMPTY);
                            // Remove the first element: ../.
                            array_shift($pathfilename);
                            // Replace pathfilename into the filepath.
                            $path[count($path) - 1] = $pathfilename[0];
                            $filepath = '/' . implode('/', $path) . '/';
                            // Remove the element used to replace.
                            array_shift($pathfilename);
                            $matches[1] = implode('/', $pathfilename);
                        }
                        return 'url("../' . $filearea . $filepath . $itemid . '/' . $matches[1] . '")';
                    },
                    $file->get_content()) . "\n";
            }
        }
        return $content;
    }

    /**
     * Get files ready for export.
     *
     * @param  string $filename File name to retrieve.
     * @return bool|\stored_file Stored file instance if exists, false if not
     */
    public function get_export_file(string $filename) {
        return $this->fs->get_file($this->context->id, self::COMPONENT, self::EXPORT_FILEAREA, 0, '/', $filename);
    }

    /**
     * Converts a relative system file path into Moodle File API elements.
     *
     * @param  string $filepath The system filepath to get information from.
     * @return array File information.
     */
    private function get_file_elements_from_filepath(string $filepath): array {
        $sections = explode('/', $filepath);
        // Get the filename.
        $filename = array_pop($sections);
        // Discard first element.
        if (empty($sections[0])) {
            array_shift($sections);
        }
        // Get the filearea.
        $filearea = array_shift($sections);
        $itemid = array_shift($sections);
        // Get the filepath.
        $filepath = implode('/', $sections);
        $filepath = '/' . $filepath . '/';

        return ['filearea' => $filearea, 'filepath' => $filepath, 'filename' => $filename, 'itemid' => $itemid];
    }

    /**
     * Returns the item id given the other necessary variables.
     *
     * @param  string $filearea The file area.
     * @param  string $filepath The file path.
     * @param  string $filename The file name.
     * @return mixed the specified value false if not found.
     */
    private function get_itemid_for_file(string $filearea, string $filepath, string $filename) {
        global $DB;
        return $DB->get_field('files', 'itemid', ['component' => self::COMPONENT, 'filearea' => $filearea, 'filepath' => $filepath,
                'filename' => $filename]);
    }

    /**
     * Helper to make it easy to load content files.
     *
     * @param string $filearea File area where the file is saved.
     * @param int $itemid Content instance or content id.
     * @param string $file File path and name.
     *
     * @return stored_file|null
     */
    private function get_file(string $filearea, int $itemid, string $file): ?stored_file {
        global $USER;

        $component = self::COMPONENT;
        $context = $this->context->id;
        if ($filearea === 'draft') {
            $itemid = 0;
            $component = 'user';
            $usercontext = \context_user::instance($USER->id);
            $context = $usercontext->id;
        }

        $filepath = '/'. dirname($file). '/';
        $filename = basename($file);

        // Load file.
        $existingfile = $this->fs->get_file($context, $component, $filearea, $itemid, $filepath, $filename);
        if (!$existingfile) {
            return null;
        }

        return $existingfile;
    }

    /**
     * Move a single file
     *
     * @param string $sourcefile Path to source file
     * @param int $contentid Content id or 0 if the file is in the editor file area
     *
     * @return void
     */
    private function move_file(string $sourcefile, int $contentid): void {
        $pathparts = pathinfo($sourcefile);
        $filename  = $pathparts['basename'];
        $filepath  = $pathparts['dirname'];
        $foldername = basename($filepath);

        // Create file record for content.
        $record = array(
            'contextid' => $this->context->id,
            'component' => $contentid > 0 ? self::COMPONENT : 'user',
            'filearea' => $contentid > 0 ? self::CONTENT_FILEAREA : 'draft',
            'itemid' => $contentid > 0 ? $contentid : 0,
            'filepath' => '/' . $foldername . '/',
            'filename' => $filename
        );

        $file = $this->fs->get_file(
            $record['contextid'], $record['component'],
            $record['filearea'], $record['itemid'], $record['filepath'],
            $record['filename']
        );

        if ($file) {
            // Delete it to make sure that it is replaced with correct content.
            $file->delete();
        }

        $this->fs->create_file_from_pathname($record, $sourcefile);
    }
}

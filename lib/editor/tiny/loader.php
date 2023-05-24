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
 * Tiny text editor integration - TinyMCE Loader.
 *
 * @package    editor_tiny
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace editor_tiny;

// Disable moodle specific debug messages and any errors in output.
define('NO_DEBUG_DISPLAY', true);

// We need just the values from config.php and minlib.php.
define('ABORT_AFTER_CONFIG', true);

// This stops immediately at the beginning of lib/setup.php.
require('../../../config.php');

/**
 * An anonymous class to handle loading and serving TinyMCE JavaScript.
 *
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class loader {
    /** @var string The filepath requested */
    protected $filepath;

    /** @var int The revision requested */
    protected $rev;

    /** @var string The mimetype to send */
    protected $mimetype = null;

    /** @var string The component to use */
    protected $component;

    /** @var string The complete path to the candidate file */
    protected $candidatefile;

    /**
     * Initialise the class, parse the request and serve the content.
     */
    public function __construct() {
        $this->parse_file_information_from_url();
        $this->serve_file();
    }

    /**
     * Parse the file information from the URL.
     */
    protected function parse_file_information_from_url(): void {
        global $CFG;

        // The URL format is /[revision]/[filepath].
        // The revision is an integer with negative values meaning the file is not cached.
        // The filepath is a child of the TinyMCE js/tinymce directory containing all upstream code.
        // The filepath is cleaned using the SAFEPATH option, which does not allow directory traversal.
        if ($slashargument = min_get_slash_argument()) {
            $slashargument = ltrim($slashargument, '/');
            if (substr_count($slashargument, '/') < 1) {
                $this->send_not_found();
            }

            [$rev, $filepath] = explode('/', $slashargument, 2);
            $this->rev  = min_clean_param($rev, 'INT');
            $this->filepath = min_clean_param($filepath, 'SAFEPATH');
        } else {
            $this->rev  = min_optional_param('rev', 0, 'INT');
            $this->filepath = min_optional_param('filepath', 'standard', 'SAFEPATH');
        }

        $extension = pathinfo($this->filepath, PATHINFO_EXTENSION);
        if ($extension === 'css') {
            $this->mimetype = 'text/css';
        } else if ($extension === 'js') {
            $this->mimetype = 'application/javascript';
        } else if ($extension === 'map') {
            $this->mimetype = 'application/json';
        } else {
            $this->send_not_found();
        }

        $filepathhash = sha1("{$this->filepath}");
        if (preg_match('/^plugins\/tiny_/', $this->filepath)) {
            $parts = explode('/', $this->filepath);
            array_shift($parts);
            $component = array_shift($parts);
            $this->component = preg_replace('/^tiny_/', '', $component);
            $this->filepath = implode('/', $parts);
        }
        $this->candidatefile = "{$CFG->localcachedir}/editor_tiny/{$this->rev}/{$filepathhash}";
    }

    /**
     * Serve the requested file from the most appropriate location, caching if possible.
     */
    public function serve_file(): void {
        // Attempt to send the cached filepathpack.
        if ($this->rev > 0) {
            if ($this->is_candidate_file_available()) {
                // The send_cached_file_if_available function will exit if successful.
                // In theory the file could become unavailable after checking that the file exists.
                // Whilst this is unlikely, fall back to caching the content below.
                $this->send_cached_file_if_available();
            }

            // The file isn't cached yet.
            // Store it in the cache and serve it.
            $this->store_filepath_file();
            $this->send_cached();
        } else {
            // If the revision is less than 0, then do not cache anything.
            // Moodle is configured to not cache javascript or css.
            $this->send_uncached_from_dirroot();
        }
    }

    /**
     * Get the full filepath to the requested file.
     *
     * @return string
     */
    protected function get_filepath_from_dirroot(): ?string {
        global $CFG;

        $rootdir = "{$CFG->dirroot}/lib/editor/tiny";
        if ($this->component) {
            $rootdir .= "/plugins/{$this->component}/js";
        } else {
            $rootdir .= "/js/tinymce";
        }

        $filepath = "{$rootdir}/{$this->filepath}";
        if (file_exists($filepath)) {
            return $filepath;
        }

        return null;
    }

    /**
     * Load the file content from the dirroot.
     *
     * @return string
     */
    protected function load_content_from_dirroot(): ?string {
        if ($filepath = $this->get_filepath_from_dirroot()) {
            return file_get_contents($filepath);
        }

        return null;
    }

    /**
     * Send the file content from the dirroot.
     *
     * If the file is not found, send the 404 response instead.
     */
    protected function send_uncached_from_dirroot(): void {
        if ($filepath = $this->get_filepath_from_dirroot()) {
            $this->send_uncached_file($filepath);
        }

        $this->send_not_found();
    }

    /**
     * Check whether the candidate file exists.
     *
     * @return bool
     */
    protected function is_candidate_file_available(): bool {
        return file_exists($this->candidatefile);
    }

    /**
     * Send the candidate file.
     */
    protected function send_cached_file_if_available(): void {
        global $_SERVER;

        if (file_exists($this->candidatefile)) {
            // The candidate file exists so will be sent regardless.

            if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                // The browser sent headers to check if the file has changed.
                // We do not actually need to verify the eTag value or compare modification headers because our files
                // never change in cache. When changes are made we increment the revision counter.
                $this->send_unmodified_headers(filemtime($this->candidatefile));
            }

            // No modification headers were sent so simply serve the file from cache.
            $this->send_cached($this->candidatefile);
        }
    }

    /**
     * Store the file content in the candidate file.
     */
    protected function store_filepath_file(): void {
        global $CFG;

        clearstatcache();
        if (!file_exists(dirname($this->candidatefile))) {
            @mkdir(dirname($this->candidatefile), $CFG->directorypermissions, true);
        }

        // Prevent serving of incomplete file from concurrent request,
        // the rename() should be more atomic than fwrite().
        ignore_user_abort(true);

        $filename = $this->candidatefile;
        if ($fp = fopen($filename . '.tmp', 'xb')) {
            $content = $this->load_content_from_dirroot();
            fwrite($fp, $content);
            fclose($fp);
            rename($filename . '.tmp', $filename);
            @chmod($filename, $CFG->filepermissions);
            @unlink($filename . '.tmp'); // Just in case anything fails.
        }

        ignore_user_abort(false);
        if (connection_aborted()) {
            die;
        }
    }

    /**
     * Get the eTag for the candidate file.
     *
     * This is a unique hash based on the file arguments.
     * It does not need to consider the file content because we use a cache busting URL.
     *
     * @return string The eTag content
     */
    protected function get_etag(): string {
        $etag = [
            $this->filepath,
            $this->rev,
        ];

        return sha1(implode('/', $etag));
    }

    /**
     * Send the candidate file, with aggressive cachign headers.
     *
     * This includdes eTags, a last-modified, and expiry approximately 90 days in the future.
     */
    protected function send_cached(): void {
        $path = $this->candidatefile;

        // 90 days only - based on Moodle point release cadence being every 3 months.
        $lifetime = 60 * 60 * 24 * 90;

        header('Etag: "' . $this->get_etag() . '"');
        header('Content-Disposition: inline; filename="filepath.php"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
        header('Pragma: ');
        header('Cache-Control: public, max-age=' . $lifetime . ', immutable');
        header('Accept-Ranges: none');
        header("Content-Type: {$this->mimetype}; charset=utf-8");
        if (!min_enable_zlib_compression()) {
            header('Content-Length: ' . filesize($path));
        }

        readfile($path);
        die;
    }

    /**
     * Sends the content directly without caching it.
     *
     * No aggressive caching is used, and the expiry is set to the current time.
     *
     * @param string $filepath
     */
    protected function send_uncached_file(string $filepath): void {
        header('Content-Disposition: inline; filename="styles_debug.php"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Pragma: ');
        header('Accept-Ranges: none');
        header("Content-Type: {$this->mimetype}; charset=utf-8");

        readfile($filepath);
        die;
    }

    /**
     * Send headers to indicate that the file has not been modified at all
     *
     * @param int $lastmodified
     */
    protected function send_unmodified_headers(int $lastmodified): void {
        // 90 days only - based on Moodle point release cadence being every 3 months.
        $lifetime = 60 * 60 * 24 * 90;
        header('HTTP/1.1 304 Not Modified');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
        header('Cache-Control: public, max-age=' . $lifetime);
        header("Content-Type: {$this->mimetype}; charset=utf-8");
        header('Etag: "' . $this->get_etag() . '"');
        if ($lastmodified) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
        }
        die;
    }

    /**
     * Sends a 404 message to indicate that the content was not found.
     */
    protected function send_not_found(): void {
        header('HTTP/1.0 404 not found');
        die('TinyMCE file was not found, sorry.');
    }
}

new loader();

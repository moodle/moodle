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
 * Tiny text editor integration - Language Producer.
 *
 * @package    editor_tiny
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace editor_tiny;

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// We need just the values from config.php and minlib.php.
define('ABORT_AFTER_CONFIG', true);

// This stops immediately at the beginning of lib/setup.php.
require('../../../config.php');

/**
 * An anonymous class to handle loading and serving lang files for TinyMCE.
 *
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang {
    /** @var string The language code to load */
    protected $lang;

    /** @var int The revision requested */
    protected $rev;

    /** @var bool Whether Moodle is fully loaded or not */
    protected $fullyloaded = false;

    /** @var string The complete path to the candidate file */
    protected $candidatefile;

    /**
     * Constructor to load and serve the langfile.
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

        // The URL format is /[revision]/[lang].
        // The revision is an integer with negative values meaning the file is not cached.
        // The lang is a simple word with no directory separators or special characters.
        if ($slashargument = min_get_slash_argument()) {
            $slashargument = ltrim($slashargument, '/');
            if (substr_count($slashargument, '/') < 1) {
                css_send_css_not_found();
            }

            [$rev, $lang] = explode('/', $slashargument, 2);
            $rev  = min_clean_param($rev, 'INT');
            $lang = min_clean_param($lang, 'SAFEDIR');
        } else {
            $rev  = min_optional_param('rev', 0, 'INT');
            $lang = min_optional_param('lang', 'standard', 'SAFEDIR');
        }

        // Retrieve the correct language by converting to Moodle's language code format.
        $this->lang = str_replace('-', '_', $lang);
        $this->rev = $rev;
        $this->candidatefile = "{$CFG->localcachedir}/editor_tiny/{$this->rev}/lang/{$this->lang}/lang.json";
    }

    /**
     * Serve the language pack content.
     */
    protected function serve_file(): void {
        // Attempt to send the cached langpack.
        if ($this->rev > 0) {
            if ($this->is_candidate_file_available()) {
                // The send_cached_file_if_available function will exit if successful.
                // In theory the file could become unavailable after checking that the file exists.
                // Whilst this is unlikely, fall back to caching the content below.
                $this->send_cached_pack();
            }

            // The file isn't cached yet.
            // Load the content. store it in the cache, and serve it.
            $strings = $this->load_language_pack();
            $this->store_lang_file($strings);
            $this->send_cached();
        } else {
            // If the revision is less than 0, then do not cache anything.
            $strings = $this->load_language_pack();
            $this->send_uncached($strings);
        }
    }

    /**
     * Load the full Moodle Framework.
     */
    protected function load_full_moodle(): void {
        global $CFG, $DB, $SESSION, $OUTPUT, $PAGE;

        if ($this->is_full_moodle_loaded()) {
            return;
        }

        // Ok, now we need to start normal moodle script, we need to load all libs and $DB.
        define('ABORT_AFTER_CONFIG_CANCEL', true);

        // Session not used here.
        define('NO_MOODLE_COOKIES', true);

        // Ignore upgrade check.
        define('NO_UPGRADE_CHECK', true);

        require("{$CFG->dirroot}/lib/setup.php");
        $this->fullyloaded = true;
    }

    /**
     * Check whether Moodle is fully loaded.
     *
     * @return bool
     */
    public function is_full_moodle_loaded(): bool {
        return $this->fullyloaded;
    }

    /**
     * Load the language pack strings.
     *
     * @return string[]
     */
    protected function load_language_pack(): array {
        // We need to load the full moodle API to use the string manager.
        $this->load_full_moodle();

        // We maintain a list of string identifier to original TinyMCE string.
        // TinyMCE uses English language strings to perform translations.
        $stringlist = file_get_contents(__DIR__ . "/tinystrings.json");
        if (empty($stringlist)) {
            $this->send_not_found("Failed to load strings from tinystrings.json");
        }

        $stringlist = json_decode($stringlist, true);
        if (empty($stringlist)) {
            $this->send_not_found("Failed to load strings from tinystrings.json");
        }

        // Load all strings for the TinyMCE Editor which have a prefix of `tiny:` from the Moodle String Manager.
        $stringmanager = get_string_manager();
        $translatedvalues = array_filter(
            $stringmanager->load_component_strings('editor_tiny', $this->lang),
            function(string $value, string $key): bool {
                return strpos($key, 'tiny:') === 0;
            },
            ARRAY_FILTER_USE_BOTH
        );

        // We will associate the _original_ TinyMCE string to its translation, but only where it is different.
        // Where the original TinyMCE string matches the Moodle translation of it, we do not supply the string.
        $strings = [];
        foreach ($stringlist as $key => $value) {
            if (array_key_exists($key, $translatedvalues)) {
                if ($translatedvalues[$key] !== $value) {
                    $strings[$value] = $translatedvalues[$key];
                }
            }
        }

        // TinyMCE uses a secret string only present in some languages to set a language direction.
        // Rather than applying to only some languages, we just apply to all from our own langconfig.
        // Note: Do not rely on right_to_left() as the current language is unset.
        $strings['_dir'] = $stringmanager->get_string('thisdirection', 'langconfig', null, $this->lang);

        return $strings;
    }

    /**
     * Send a cached language pack.
     */
    protected function send_cached_pack(): void {
        global $CFG;

        if (file_exists($this->candidatefile)) {
            if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                // We do not actually need to verify the etag value because our files
                // never change in cache because we increment the rev counter.
                $this->send_unmodified_headers(filemtime($this->candidatefile));
            }
            $this->send_cached($this->candidatefile);
        }
    }

    /**
     * Store a langauge cache file containing all of the processed strings.
     *
     * @param string[] $strings The strings to store
     */
    protected function store_lang_file(array $strings): void {
        global $CFG;

        clearstatcache();
        if (!file_exists(dirname($this->candidatefile))) {
            @mkdir(dirname($this->candidatefile), $CFG->directorypermissions, true);
        }

        // Prevent serving of incomplete file from concurrent request,
        // the rename() should be more atomic than fwrite().
        ignore_user_abort(true);

        // First up write out the single file for all those using decent browsers.
        $content = json_encode($strings, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES);

        $filename = $this->candidatefile;
        if ($fp = fopen($filename . '.tmp', 'xb')) {
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
     * Check whether the candidate file exists.
     *
     * @return bool
     */
    protected function is_candidate_file_available(): bool {
        return file_exists($this->candidatefile);
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
            $this->lang,
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
        header('Content-Disposition: inline; filename="lang.php"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
        header('Pragma: ');
        header('Cache-Control: public, max-age=' . $lifetime . ', immutable');
        header('Accept-Ranges: none');
        header('Content-Type: application/json; charset=utf-8');
        if (!min_enable_zlib_compression()) {
            header('Content-Length: ' . filesize($path));
        }

        readfile($path);
        die;
    }

    /**
     * Sends the content directly without caching it.
     *
     * @param string[] $strings
     */
    protected function send_uncached(array $strings): void {
        header('Content-Disposition: inline; filename="styles_debug.php"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Pragma: ');
        header('Accept-Ranges: none');
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($strings, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES);
        die;
    }

    /**
     * Send file not modified headers.
     *
     * @param int $lastmodified
     */
    protected function send_unmodified_headers($lastmodified): void {
        // 90 days only - based on Moodle point release cadence being every 3 months.
        $lifetime = 60 * 60 * 24 * 90;
        header('HTTP/1.1 304 Not Modified');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
        header('Cache-Control: public, max-age=' . $lifetime);
        header('Content-Type: application/json; charset=utf-8');
        header('Etag: "' . $this->get_etag() . '"');
        if ($lastmodified) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
        }
        die;
    }

    /**
     * Sends a 404 message to indicate that the content was not found.
     *
     * @param null|string $message An optional informative message to include to help debugging
     */
    protected function send_not_found(?string $message = null): void {
        header('HTTP/1.0 404 not found');

        if ($message) {
            die($message);
        } else {
            die('Language data was not found, sorry.');
        }
    }
};

$loader = new lang();

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

namespace core_external;

/**
 * Singleton to handle the external settings..
 *
 * We use singleton to encapsulate the "logic".
 *
 * @package    core_external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_settings {

    /** @var settings|null the singleton instance */
    public static $instance = null;

    /** @var bool Should the external function return raw text or formatted */
    private $raw = false;

    /** @var bool Should the external function filter the text */
    private $filter = false;

    /** @var bool Should the external function rewrite plugin file url */
    private $fileurl = true;

    /** @var string In which file should the urls be rewritten */
    private $file = 'webservice/pluginfile.php';

    /** @var string The session lang */
    private $lang = '';

    /** @var string The timezone to use during this WS request */
    private $timezone = '';

    /**
     * Constructor - protected - can not be instanciated
     */
    protected function __construct() {
        if ((AJAX_SCRIPT == false) && (CLI_SCRIPT == false) && (WS_SERVER == false)) {
            // For normal pages, the default should match the default for format_text.
            $this->filter = true;
            // Use pluginfile.php for web requests.
            $this->file = 'pluginfile.php';
        }
    }

    /**
     * Return only one instance
     *
     * @return self
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance.
     */
    public static function reset(): void {
        self::$instance = null;
    }

    /**
     * Set raw
     *
     * @param boolean $raw
     */
    public function set_raw($raw) {
        $this->raw = $raw;
    }

    /**
     * Get raw
     *
     * @return boolean
     */
    public function get_raw() {
        return $this->raw;
    }

    /**
     * Set filter
     *
     * @param boolean $filter
     */
    public function set_filter($filter) {
        $this->filter = $filter;
    }

    /**
     * Get filter
     *
     * @return boolean
     */
    public function get_filter() {
        return $this->filter;
    }

    /**
     * Set fileurl
     *
     * @param bool $fileurl
     */
    public function set_fileurl($fileurl) {
        $this->fileurl = $fileurl;
    }

    /**
     * Get fileurl
     *
     * @return bool
     */
    public function get_fileurl() {
        return $this->fileurl;
    }

    /**
     * Set file
     *
     * @param string $file
     */
    public function set_file($file) {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function get_file() {
        return $this->file;
    }

    /**
     * Set lang
     *
     * @param string $lang
     */
    public function set_lang($lang) {
        $this->lang = $lang;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function get_lang() {
        return $this->lang;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     */
    public function set_timezone($timezone) {
        $this->timezone = $timezone;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function get_timezone() {
        return $this->timezone;
    }
}

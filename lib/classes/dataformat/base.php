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
 * Base class for dataformat.
 *
 * @package    core
 * @subpackage dataformat
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\dataformat;

use coding_exception;

/**
 * Base class for dataformat.
 *
 * @package    core
 * @subpackage dataformat
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /** @var $mimetype */
    protected $mimetype = "text/plain";

    /** @var $extension */
    protected $extension = ".txt";

    /** @var $filename */
    protected $filename = '';

    /** @var string The location to store the output content */
    protected $filepath = '';

    /**
     * Get the file extension
     *
     * @return string file extension
     */
    public function get_extension() {
        return $this->extension;
    }

    /**
     * Set download filename base
     *
     * @param string $filename
     */
    public function set_filename($filename) {
        $this->filename = $filename;
    }

    /**
     * Set file path when writing to file
     *
     * @param string $filepath
     * @throws coding_exception
     */
    public function set_filepath(string $filepath): void {
        $filedir = dirname($filepath);
        if (!is_writable($filedir)) {
            throw new coding_exception('File path is not writable');
        }

        $this->filepath = $filepath;

        // Some dataformat writers may expect filename to be set too.
        $this->set_filename(pathinfo($this->filepath, PATHINFO_FILENAME));
    }

    /**
     * Set the title of the worksheet inside a spreadsheet
     *
     * For some formats this will be ignored.
     *
     * @param string $title
     */
    public function set_sheettitle($title) {
    }

    /**
     * Output file headers to initialise the download of the file.
     */
    public function send_http_headers() {
        if (defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST) {
            // For text based formats - we cannot test the output with behat if we force a file download.
            return;
        }
        if (is_https()) {
            // HTTPS sites - watch out for IE! KB812935 and KB316431.
            header('Cache-Control: max-age=10');
            header('Pragma: ');
        } else {
            // Normal http - prevent caching at all cost.
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            header('Pragma: no-cache');
        }
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header("Content-Type: $this->mimetype\n");
        $filename = $this->filename . $this->get_extension();
        header("Content-Disposition: attachment; filename=\"$filename\"");
    }

    /**
     * Set the dataformat to be output to current file. Calling code must call {@see base::close_output_to_file()} when finished
     */
    public function start_output_to_file(): void {
        // Raise memory limit to ensure we can store the entire content. Start collecting output.
        raise_memory_limit(MEMORY_EXTRA);

        ob_start();
        $this->start_output();
    }

    /**
     * Write the start of the file.
     */
    public function start_output() {
        // Override me if needed.
    }

    /**
     * Write the start of the sheet we will be adding data to.
     *
     * @param array $columns
     */
    public function start_sheet($columns) {
        // Override me if needed.
    }

    /**
     * Method to define whether the dataformat supports export of HTML
     *
     * @return bool
     */
    public function supports_html(): bool {
        return false;
    }

    /**
     * Apply formatting to the cells of a given record
     *
     * @param array|\stdClass $record
     * @return array
     */
    protected function format_record($record): array {
        $record = (array)$record;

        // If the dataformat supports export of HTML, we need to allow them to manage embedded images.
        if ($this->supports_html()) {
            $record = array_map([$this, 'replace_pluginfile_images'], $record);
        }

        return $record;
    }

    /**
     * Given a stored_file, return a suitable source attribute for an img element in the export (or null to use the original)
     *
     * @param \stored_file $file
     * @return string|null
     */
    protected function export_html_image_source(\stored_file $file): ?string {
        return null;
    }

    /**
     * We need to locate all img tags within a given cell that match pluginfile URL's. Partly so the exported file will show
     * the image without requiring the user is logged in; and also to prevent some of the dataformats requesting the file
     * themselves, which is likely to fail due to them not having an active session
     *
     * @param string|null $content
     * @return string
     */
    protected function replace_pluginfile_images(?string $content): string {
        $content = (string)$content;

        // Examine content to see if it contains any HTML image tags.
        return preg_replace_callback('/(?<pre><img[^>]+src=")(?<source>[^"]*)(?<post>".*>)/i', function(array $matches) {
            $source = $matches['source'];

            // Now check if the image source looks like a pluginfile URL.
            if (preg_match('/pluginfile.php\/(?<context>\d+)\/(?<component>[^\/]+)\/(?<filearea>[^\/]+)\/(?:(?<itemid>\d+)\/)?' .
                    '(?<path>.*)/u', $source, $args)) {

                $context = $args['context'];
                $component = clean_param($args['component'], PARAM_COMPONENT);
                $filearea = clean_param($args['filearea'], PARAM_AREA);
                $itemid = $args['itemid'] ?: 0;
                $path = clean_param(urldecode($args['path']), PARAM_PATH);

                // Try and get the matching file from storage, allow the dataformat to define the replacement source.
                $fullpath = "/{$context}/{$component}/{$filearea}/{$itemid}/{$path}";
                if ($file = get_file_storage()->get_file_by_hash(sha1($fullpath))) {
                    $exportsource = $this->export_html_image_source($file);

                    if ($exportsource) {
                        $source = $exportsource;
                    }
                }
            }

            return $matches['pre'] . $source . $matches['post'];
        }, $content);
    }

    /**
     * Write a single record
     *
     * @param array $record
     * @param int $rownum
     */
    abstract public function write_record($record, $rownum);

    /**
     * Write the end of the sheet containing the data.
     *
     * @param array $columns
     */
    public function close_sheet($columns) {
        // Override me if needed.
    }

    /**
     * Write the end of the file.
     */
    public function close_output() {
        // Override me if needed.
    }

    /**
     * Write the data to disk. Calling code should have previously called {@see base::start_output_to_file()}
     *
     * @return bool Whether the write succeeded
     */
    public function close_output_to_file(): bool {
        $this->close_output();

        $filecontent = ob_get_contents();
        ob_end_clean();

        return file_put_contents($this->filepath, $filecontent) !== false;
    }
}

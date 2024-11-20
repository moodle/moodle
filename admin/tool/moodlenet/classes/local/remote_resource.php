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
 * Contains the remote_resource class definition.
 *
 * @package tool_moodlenet
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlenet\local;

/**
 * The remote_resource class.
 *
 * Objects of type remote_resource provide a means of interacting with resources over HTTP.
 *
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remote_resource {

    /** @var \curl $curl the curl http helper.*/
    protected $curl;

    /** @var url $url the url to the remote resource.*/
    protected $url;

    /** @var string $filename the name of this remote file.*/
    protected $filename;

    /** @var string $extension the file extension of this remote file.*/
    protected $extension;

    /** @var array $headinfo the array of information for the most recent HEAD request.*/
    protected $headinfo = [];

    /** @var \stdClass $metadata information about the resource. */
    protected $metadata;

    /**
     * The remote_resource constructor.
     *
     * @param \curl $curl a curl object for HTTP requests.
     * @param url $url the URL of the remote resource.
     * @param \stdClass $metadata resource metadata such as name, summary, license, etc.
     */
    public function __construct(\curl $curl, url $url, \stdClass $metadata) {
        $this->curl = $curl;
        $this->url = $url;
        $this->filename = pathinfo($this->url->get_path() ?? '', PATHINFO_FILENAME);
        $this->extension = pathinfo($this->url->get_path() ?? '', PATHINFO_EXTENSION);
        $this->metadata = $metadata;
    }

    /**
     * Return the URL for this remote resource.
     *
     * @return url the url object.
     */
    public function get_url(): url {
        return $this->url;
    }

    /**
     * Get the name of the file as taken from the metadata.
     */
    public function get_name(): string {
        return $this->metadata->name ?? '';
    }

    /**
     * Get the resource metadata.
     *
     * @return \stdClass the metadata.
     */
    public function get_metadata(): \stdClass {
        return$this->metadata;
    }

    /**
     * Get the description of the resource as taken from the metadata.
     *
     * @return string
     */
    public function get_description(): string {
        return $this->metadata->description ?? '';
    }

    /**
     * Return the extension of the file, if found.
     *
     * @return string the extension of the file, if found.
     */
    public function get_extension(): string {
        return $this->extension;
    }

    /**
     * Returns the file size of the remote file, in bytes, or null if it cannot be determined.
     *
     * @return int|null the content length, if able to be determined, otherwise null.
     */
    public function get_download_size(): ?int {
        $this->get_resource_info();
        return $this->headinfo['download_content_length'] ?? null;
    }

    /**
     * Download the remote resource to a local requestdir, returning the path and name of the resulting file.
     *
     * @return array an array containing filepath adn filename, e.g. [filepath, filename].
     * @throws \moodle_exception if the file cannot be downloaded.
     */
    public function download_to_requestdir(): array {
        $filename = sprintf('%s.%s', $this->filename, $this->get_extension());
        $path = make_request_directory();
        $fullpathwithname = sprintf('%s/%s', $path, $filename);

        // In future, use a timeout (download and/or connection) controlled by a tool_moodlenet setting.
        $downloadtimeout = 30;

        $result = $this->curl->download_one($this->url->get_value(), null, ['filepath' => $fullpathwithname,
            'timeout' => $downloadtimeout]);
        if ($result !== true) {
            throw new \moodle_exception('errorduringdownload', 'tool_moodlenet', '', $result);
        }

        return [$path, $filename];
    }

    /**
     * Fetches information about the remote resource via a HEAD request.
     *
     * @throws \coding_exception if any connection problems occur.
     */
    protected function get_resource_info() {
        if (!empty($this->headinfo)) {
            return;
        }
        $options['CURLOPT_RETURNTRANSFER'] = 1;
        $options['CURLOPT_FOLLOWLOCATION'] = 1;
        $options['CURLOPT_MAXREDIRS'] = 5;
        $options['CURLOPT_FAILONERROR'] = 1; // We want to consider http error codes as errors to report, not just status codes.

        $this->curl->head($this->url->get_value(), $options);
        $errorno = $this->curl->get_errno();
        $this->curl->resetopt();

        if ($errorno !== 0) {
            $message = 'Problem during HEAD request for remote resource \''.$this->url->get_value().'\'. Curl Errno: ' . $errorno;
            throw new \coding_exception($message);
        }
        $this->headinfo = $this->curl->get_info();
    }

}

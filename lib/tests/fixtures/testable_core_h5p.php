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
 * Fixture for testing the functionality of core_h5p.
 *
 * @package     core
 * @subpackage  fixtures
 * @category    test
 * @copyright   2019 Victor Deniz <victor@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P factory class stub for testing purposes.
 *
 * This class extends the real one to return the H5P core class stub.
 *
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class h5p_test_factory extends factory {
    /**
     * Returns an instance of the \core_h5p\core stub class.
     *
     * @return core_h5p\core
     */
    public function get_core(): core {
        if ($this->core === null ) {
            $fs = new file_storage();
            $language = framework::get_language();
            $context = \context_system::instance();

            $url = \moodle_url::make_pluginfile_url($context->id, 'core_h5p', '', null,
                '', '')->out();

            $this->core = new h5p_test_core($this->get_framework(), $fs, $url, $language, true);
        }

        return $this->core;
    }
}

/**
 * H5P core class stub for testing purposes.
 *
 * Modifies get_api_endpoint method to use local URLs.
 *
 * @copyright   2019 Victor Deniz <victor@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class h5p_test_core extends core {
    /** @var $endpoint Endpoint URL for testing H5P API */
    protected $endpoint;

    /**
     * Set the endpoint URL
     *
     * @param string $url Endpoint URL
     * @return void
     */
    public function set_endpoint($url): void {
        $this->endpoint = $url;
    }

    /**
     * Get the URL of the test endpoints instead of the H5P ones.
     *
     * If $endpoint = 'content' and $library is null, moodle_url is the endpoint of the latest version of the H5P content
     * types; however, if $library is the machine name of a content type, moodle_url is the endpoint to download the content type.
     * The SITES endpoint ($endpoint = 'site') may be use to get a site UUID or send site data.
     *
     * @param string|null $library The machineName of the library whose endpoint is requested.
     * @param string $endpoint The endpoint required. Valid values: "site", "content".
     * @return \moodle_url The endpoint moodle_url object.
     */
    public function get_api_endpoint(?string $library = null, string $endpoint = 'content'): \moodle_url {

        if ($library) {
            $h5purl = $this->endpoint . '/' . $library . '.h5p';
        } else if ($endpoint == 'content') {
            $h5purl = $this->endpoint . '/h5pcontenttypes.json';
        } else {
            $h5purl = $this->endpoint . '/h5puuid.json';
        }

        return new \moodle_url($h5purl);
    }
}
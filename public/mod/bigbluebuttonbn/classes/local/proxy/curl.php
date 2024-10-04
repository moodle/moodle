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
 * A curl wrapper for bbb.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bigbluebuttonbn\local\proxy;

use SimpleXMLElement;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("{$CFG->libdir}/filelib.php");

/**
 * A curl wrapper for bbb.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class curl extends \curl {
    /** @var string */
    protected $contenttype;

    /**
     * Constructor for the class.
     */
    public function __construct() {
        $settings = [];
        if (debugging()) {
            $settings = ['ignoresecurity' => true];
        }
        parent::__construct($settings);

        $this->setopt(['SSL_VERIFYPEER' => true]);
        $this->set_content_type('application/xml');
    }

    /**
     * Fetch the content type.
     */
    public function get_content_type(): string {
        return $this->contenttype;
    }

    /**
     * Set the desired current content type.
     *
     * @param string $type
     * @return self
     */
    public function set_content_type(string $type): self {
        $this->contenttype = $type;

        return $this;
    }

    /**
     * HTTP POST method
     *
     * @param string $url
     * @param array|string $params
     * @param array $options
     * @return null|SimpleXMLElement Null on error
     */
    public function post($url, $params = '', $options = []) {
        if (!is_string($params)) {
            debugging('Only string parameters are supported', DEBUG_DEVELOPER);
            $params = '';
        }
        $options = array_merge($options, [
            'CURLOPT_HTTPHEADER' => [
                'Content-Type: ' . $this->get_content_type(),
                'Content-Length: ' . strlen($params),
                'Content-Language: en-US',
            ]
        ]);

        return $this->handle_response(parent::post($url, $params, $options));
    }

    /**
     * Fetch the specified URL via a HEAD request.
     *
     * @param string $url
     * @param array $options
     */
    public function head($url, $options = []) {
        $options['followlocation'] = true;
        $options['timeout'] = 1;

        parent::head($url, $options);

        return $this->get_info();
    }

    /**
     * Fetch the specified URL via a GET request.
     *
     * @param string $url
     * @param string $params
     * @param array $options
     */
    public function get($url, $params = [], $options = []) {
        return $this->handle_response(parent::get($url, $params, $options));
    }

    /**
     * Handle the response.
     *
     * @param mixed $response
     * @return null|SimpleXMLElement Null on error
     */
    protected function handle_response($response): ?SimpleXMLElement {
        if (!$response) {
            debugging('No response returned for call', DEBUG_DEVELOPER);
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        try {
            $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        } catch (Exception $e) {
            libxml_use_internal_errors($previous);
            debugging('Caught exception: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return null;
        }

        if ($xml instanceof SimpleXMLElement) {
            return $xml;
        }

        $debugabstract = html_to_text($response);
        $debugabstract = substr($debugabstract, 0, 1024); // Limit to small amount of info so we do not overload logs.
        debugging('Issue retrieving information from the server: ' . $debugabstract, DEBUG_DEVELOPER);
        return null;
    }
}

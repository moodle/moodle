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
 * Serializer.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\serializer;

/**
 * Serializer.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url_serializer implements serializer_with_read_structure {

    /** @var bool Whether we are in a WS request. */
    protected $iswsrequest;
    /** @var string The base URL. */
    protected $wsbaseurl;

    /**
     * Constructor.
     */
    public function __construct() {
        global $CFG;
        $this->iswsrequest = WS_SERVER;
        $this->wsbaseurl = $CFG->httpswwwroot . '/webservice';
    }

    /**
     * Serialize.
     *
     * @param mixed $url The URL.
     * @return array|scalar
     */
    public function serialize($url) {
        $url = is_object($url) ? (string) $url : null;

        // This is a bit ugly, but that's needed for compat with Mobile app.
        if ($this->iswsrequest) {
            if (is_string($url) && strpos($url, '/pluginfile.php') > 0 && strpos($url, '/webservice/pluginfile.php') === false) {
                $url = $this->wsbaseurl . substr($url, strpos($url, '/pluginfile.php'));
            }
        }

        return $url;
    }

    /**
     * Return the structure for external services.
     *
     * @param int $required Value constant.
     * @param scalar $default Default value.
     * @param int $null Whether null is allowed.
     * @return external_value
     */
    public function get_read_structure($required = VALUE_REQUIRED, $default = null, $null = NULL_ALLOWED) {
        return new \external_value(PARAM_URL, '', $required, $default, $null);
    }

}

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
 * Class for generating and representing a Safe Exam Browser config key.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for generating and representing a Safe Exam Browser config key.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_key {

    /** @var string $hash The Config Key hash. */
    private $hash;

    /**
     * The config_key constructor.
     *
     * @param string $hash The Config Key hash.
     */
    public function __construct(string $hash) {
        $this->hash = $hash;
    }

    /**
     * Generate the Config Key hash from an SEB Config XML string.
     *
     * See  https://safeexambrowser.org/developer/seb-config-key.html for more information about the process.
     *
     * @param string $xml A PList XML string, representing SEB config.
     * @return config_key This config key instance.
     */
    public static function generate(string $xml) : config_key {
        if (!empty($xml) && !helper::is_valid_seb_config($xml)) {
            throw new \invalid_parameter_exception('Invalid a PList XML string, representing SEB config');
        }

        $plist = new property_list($xml);
        // Remove the key "originatorVersion" first. This key is exempted from the SEB-JSON hash (it's a special key
        // which doesn't have any functionality, it's just meta data indicating which SEB version saved the config file).
        $plist->delete_element('originatorVersion');
        // Convert the plist XML of a decrypted/unencrypted SEB config file to a ordered JSON-like "SEB-JSON" object.
        $hash = $plist->to_json();
        // Hash the JSON with SHA256. Defaults to required Base16 encoding.
        $hash = hash('SHA256', $hash);

        return new self($hash);
    }

    /**
     * Get the Config Key hash.
     *
     * @return string The Config Key hash
     */
    public function get_hash() : string {
        return $this->hash;
    }
}

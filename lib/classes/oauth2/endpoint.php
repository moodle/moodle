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
 * Class for loading/storing oauth2 endpoints from the DB.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

use core\persistent;
use lang_string;

/**
 * Class for loading/storing oauth2 endpoints from the DB
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class endpoint extends persistent {

    const TABLE = 'oauth2_endpoint';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'issuerid' => array(
                'type' => PARAM_INT
            ),
            'name' => array(
                'type' => PARAM_ALPHANUMEXT,
            ),
            'url' => array(
                'type' => PARAM_URL,
            )
        );
    }

    /**
     * Custom validator for end point URLs.
     * Because we send Bearer tokens we must ensure SSL.
     *
     * @param string $value The value to check.
     * @return lang_string|boolean
     */
    protected function validate_url($value) {
        if (strpos($value, 'https://') !== 0) {
            return new lang_string('sslonlyaccess', 'error');
        }
        return true;
    }
}

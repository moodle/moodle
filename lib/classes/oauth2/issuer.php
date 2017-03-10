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
 * Class for loading/storing issuers from the DB.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class for loading/storing issuer from the DB
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issuer extends persistent {

    const TABLE = 'oauth2_issuer';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT
            ),
            'image' => array(
                'type' => PARAM_URL,
                'null' => NULL_ALLOWED,
                'default' => null
            ),
            'clientid' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'clientsecret' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'baseurl' => array(
                'type' => PARAM_URL,
                'default' => ''
            ),
            'showonloginpage' => array(
                'type' => PARAM_BOOL,
                'default' => false
            ),
            'scopessupported' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null
            ),
            'loginscopes' => array(
                'type' => PARAM_RAW,
                'default' => 'openid profile email'
            ),
            'loginscopesoffline' => array(
                'type' => PARAM_RAW,
                'default' => 'openid profile email'
            ),
            'loginparams' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'loginparamsoffline' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'default' => 0,
            )
        );
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        if (($this->get('id') && $this->get('sortorder') === null) || !$this->get('id')) {
            $this->set('sortorder', $this->count_records());
        }
    }

    /**
     * Helper the get a named service endpoint.
     * @param string $type
     * @return string|false
     */
    public function get_endpoint_url($type) {
        $endpoint = endpoint::get_record([
            'issuerid' => $this->get('id'),
            'name' => $type . '_endpoint'
        ]);

        if ($endpoint) {
            return $endpoint->get('url');
        }
        return false;
    }

    /**
     * Does this OAuth service support user authentication?
     * @return boolean
     */
    public function is_authentication_supported() {
        return (!empty($this->get_endpoint_url('userinfo')));
    }

    /**
     * Does this OAuth service support system authentication?
     * @return boolean
     */
    public function is_system_account_setup_supported() {
        return true;
    }

    /**
     * Do we have a refresh token for a system account?
     * @return boolean
     */
    public function is_system_account_connected() {
        $sys = system_account::get_record(['issuerid' => $this->get('id')]);
        if (!empty($sys) and !empty($sys->get('refreshtoken'))) {
            return true;
        }
        return false;
    }
}

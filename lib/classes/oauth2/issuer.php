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
 * @package    core_oauth2
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

    const BEHAVIOUR_OPENID_CONNECT = 'Open ID Connect';
    const BEHAVIOUR_MICROSOFT = 'Microsoft OAuth 2.0';
    const BEHAVIOUR_OAUTH2 = 'OAuth 2.0';

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
                'type' => PARAM_RAW
            ),
            'clientsecret' => array(
                'type' => PARAM_RAW
            ),
            'behaviour' => array(
                'type' => PARAM_NOTAGS,
                'choices' => array(self::BEHAVIOUR_OPENID_CONNECT, self::BEHAVIOUR_MICROSOFT, self::BEHAVIOUR_OAUTH2),
                'default' => self::BEHAVIOUR_OPENID_CONNECT
            ),
            'baseurl' => array(
                'type' => PARAM_URL
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

    public function is_authentication_supported() {
        $supportedloginbehaviours = [
            self::BEHAVIOUR_OPENID_CONNECT,
            self::BEHAVIOUR_MICROSOFT,
        ];
        return in_array($this->get('behaviour'), $supportedloginbehaviours);
    }

    public function is_system_account_setup_supported() {
        $supportedsystemaccountbehaviours = [
            self::BEHAVIOUR_OPENID_CONNECT,
            self::BEHAVIOUR_MICROSOFT,
        ];
        return in_array($this->get('behaviour'), $supportedsystemaccountbehaviours);
    }

    public function get_behaviour_list() {
        return [
            self::BEHAVIOUR_OPENID_CONNECT => self::BEHAVIOUR_OPENID_CONNECT,
            self::BEHAVIOUR_OAUTH2 => self::BEHAVIOUR_OAUTH2,
            self::BEHAVIOUR_MICROSOFT => self::BEHAVIOUR_MICROSOFT
        ];
    }

    public function is_system_account_connected() {
        $sys = system_account::get_record(['issuerid' => $this->get('id')]);
        if (!empty($sys) and !empty($sys->get('refreshtoken'))) {
            return true;
        }
        return false;
    }
}

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
 * Class representing Microsoft 365 user information.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\obj;

use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing Microsoft 365 user information.
 */
class o365user {
    /**
     * @var int|null
     */
    protected $muserid = null;
    /**
     * @var string|null
     */
    protected $oidctoken = null;
    /**
     * @var string|null
     */
    public $objectid = null;
    /**
     * @var string|null
     */
    public $username = null;
    /**
     * @var string|null
     */
    public $upn = null;

    /**
     * Constructor.
     *
     * @param int $userid
     * @param string $oidctoken
     */
    protected function __construct($userid, $oidctoken) {
        $this->muserid = $userid;
        $this->oidctoken = $oidctoken;
        $this->objectid = $oidctoken->oidcuniqid;
        $this->username = $oidctoken->oidcusername;
        $this->upn = $oidctoken->oidcusername;
    }

    /**
     * Return ID token.
     *
     * @return mixed
     */
    public function get_idtoken() {
        return $this->oidctoken->idtoken;
    }

    /**
     * Create a new instance of the o365user object from the user ID.
     *
     * @param int $userid
     * @return o365user|null
     */
    public static function instance_from_muserid($userid) {
        global $DB;

        $aadresource = \local_o365\rest\unified::get_tokenresource();
        $params = ['userid' => $userid, 'tokenresource' => $aadresource];
        $oidctoken = $DB->get_record('auth_oidc_token', $params);
        if (empty($oidctoken)) {
            return null;
        }
        return new \local_o365\obj\o365user($userid, $oidctoken);
    }
}

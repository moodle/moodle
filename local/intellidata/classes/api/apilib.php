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

namespace local_intellidata\api;

use local_intellidata\helpers\DeprecatedHelper;
use local_intellidata\services\encryption_service;

/**
 * IntelliData Api lib.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class apilib {

    /**
     * Status success.
     */
    const STATUS_SUCCESS = 'success';
    /**
     * Status error.
     */
    const STATUS_ERROR = 'error';

    /**
     * Validate API Response and keys.
     *
     * @return bool
     * @throws \moodle_exception
     */
    public static function check_auth() {

        $encryptionservice = new encryption_service();
        $headers = DeprecatedHelper::getallheaders();
        $authheader = '';

        if (isset($headers['Auth'])) {
            $authheader = $headers['Auth'];
        } else if (isset($headers['auth'])) {
            $authheader = $headers['auth'];
        } else if (isset($headers['Authorization'])) {
            $authheader = $headers['Authorization'];
        } else if (isset($headers['authorization'])) {
            $authheader = $headers['authorization'];
        }

        if (empty($authheader)) {
            throw new \moodle_exception('Auth header required');
        }

        $authheader = json_decode($encryptionservice->decrypt($authheader), true);
        $identifier = !empty($authheader['clientidentifier']) ? $authheader['clientidentifier'] : '';

        if (!$identifier) {
            throw new \moodle_exception('Invalid encryption credentials');
        }

        $exp = !empty($authheader['exp']) ? $authheader['exp'] : 0;
        if ($exp < time()) {
            throw new \moodle_exception('Authentication expired');
        }

        if ($identifier !== $encryptionservice->clientidentifier) {
            throw new \moodle_exception('Invalid Authentication');
        }

        return true;
    }

    /**
     * Validate API params.
     *
     * @param $params
     * @param array $rules
     * @return mixed
     * @throws \coding_exception
     */
    public static function validate_parameters($params, $rules = []) {

        $encryptionservice = new encryption_service();
        $params = self::clean_parameters(
            json_decode($encryptionservice->decrypt($params), true),
            $rules
        );

        return $params;
    }

    /**
     * Clean API params.
     *
     * @param $params
     * @param array $rules
     * @return mixed
     * @throws \coding_exception
     */
    public static function clean_parameters($params, $rules = []) {

        if (count($rules)) {
            foreach ($rules as $name => $type) {
                if (isset($params[$name])) {
                    $params[$name] = (is_array($params[$name]))
                        ? clean_param_array($params[$name], $type)
                        : clean_param($params[$name], $type);
                }
            }
        }

        return $params;
    }

}

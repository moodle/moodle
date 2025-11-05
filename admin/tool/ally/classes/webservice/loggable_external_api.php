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
 * Abstract class for logging erroneous service consumption.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

defined('MOODLE_INTERNAL') || die();

use external_api;
use moodle_exception;
use Exception;
use tool_ally\logging\logger;

global $CFG;

require_once("$CFG->libdir/externallib.php");

/**
 * Abstract class for logging erroneous service consumption.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class loggable_external_api extends external_api {
    use user_fill_from_context_error;

    /**
     * @throws Exception
     */
    public static function service() {
        $params = func_get_args();
        $classname = static::class;

        // Catching and releasing exception.
        try {
            return call_user_func_array([$classname, 'execute_service'], $params);
        } catch (Exception $ex) {
            // Moodle exceptions are within the scope of normal functioning of this plugin.
            // There is no need to log them.
            if (!($ex instanceof moodle_exception) || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
                self::log_exception($ex, $classname, $params);
            }
            throw $ex;
        }
    }

    /**
     * Logs an exception in the Ally PSR log.
     * @param Exception $ex
     * @param string $classname
     * @param array $params
     */
    private static function log_exception(Exception $ex, string $classname, array $params) {
        $logstr = 'logger:servicefailure';
        $msg = get_string($logstr . '_exp', 'tool_ally', (object)[
            'class' => $classname,
            'params' => var_export($params, true)
        ]);
        logger::get()->error($logstr, [
            '_explanation' => $msg,
            '_exception' => $ex
        ]);
    }
}
